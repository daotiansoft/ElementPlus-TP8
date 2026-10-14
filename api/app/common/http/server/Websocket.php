<?php

declare (strict_types = 1);
namespace app\common\http\server;

use app\common\utils\AjaxUtils;
use think\exception\ErrorException;
use think\worker\Server;
use Workerman\Connection\TcpConnection;
use Workerman\Lib\Timer;
use Workerman\Worker;

class Websocket extends Server
{

    protected $socket = 'websocket://0.0.0.0:1000';
    // 心跳间隔（秒）
    protected $heartbeatInterval = 5;
    // 客户端超时时间（秒）
    protected $clientTimeout = 10;

    protected $userItems = [];//{"user":{"conn":"","lastTime":0}}

    const TEXT_TYPE_SEND_MESSAGE = 'text_type_send_message';//text内部通信 发送消息
    const TEXT_TYPE_GET_ONLINE_USER = 'text_type_get_online_user';//text内部通信 获取在线用户

    public function __construct()
    {
        parent::__construct();

        $this->worker->onWorkerStart = function($worker)
        {
            echo "Websocket starting...\n";

            // 设置心跳定时器
            Timer::add($this->heartbeatInterval, function() use ($worker) {
                $this->checkHeartbeat();
            });

            $text_worker = new Worker('text://0.0.0.0:5678');
            $text_worker->onMessage = function($connection, $data)
            {
                $res = 'error';
                $data = json_decode($data, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $res = $this->textSendMessage($data);
                }
                $connection->send($res);
            };
            $text_worker->listen();
        };

        /**
         * 新消息
         */
        $this->worker->onMessage = function(TcpConnection $connection, $data)
        {
            $user = $this->getUserByConnection($connection);
            if($user){
                $this->userItems[$user]['lastTime'] = time();//标记最后通信时间
                $data = json_decode($data, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $this->commandOnMessage($user,$data);
                }
            }
        };

        /**
         * 客户端链接成功
         */
        $this->worker->onWebSocketConnect = function (TcpConnection $connection, $http_header)
        {
            $uri = $_SERVER['REQUEST_URI'] ?? '';
            $queryString = parse_url($uri, PHP_URL_QUERY);
            parse_str($queryString, $params);

            $user = $params['user'] ?? '';
            if(!empty($user)){
                if(isset($this->userItems[$user])){
                    $this->userItems[$user]['conn']->close();
                }
                $this->userItems[$user] = [
                    'conn'=>$connection,
                    'lastTime'=>time()
                ];
            }
        };

        /**
         * 断开连接事件
         */
        $this->worker->onClose = function (TcpConnection $connection){
            $user = $this->getUserByConnection($connection);
            if($user){
                $this->closeByUser($user);
            }
        };

        $this->worker->onError = function (TcpConnection $connection, $code, $msg)
        {
            $user = $this->getUserByConnection($connection);
            if($user){
                $this->closeByUser($user);
                dump("客户端异常：".$user);
            }
        };
    }

    /**
     * 检查心跳（检测超时连接）
     */
    protected function checkHeartbeat()
    {
        $now = time();
        foreach ($this->userItems as $user => $item) {
            // 检查是否超时
            if ($now - $item['lastTime'] > $this->clientTimeout) {
                $this->closeByUser($user);
                dump('超时：'.$user);
            } else {
                // 发送心跳 ping
                $message = [
                    'type'=>self::TEXT_TYPE_SEND_MESSAGE,
                    'user'=>$user,
                    'message'=>[
                        'type' => 'ping',
                        'time' => $now
                    ]
                ];
                $this->textSendMessage($message);
            }
        }
    }

    /**
     * 内部发送出去消息 处理
     */
    protected function textSendMessage(array $data){
        $type = $data['type'] ?? '';
        $user = $data['user'] ?? '';
        $message = $data['message'] ?? '';

        try {
            if($type == self::TEXT_TYPE_SEND_MESSAGE && !empty($user)){
                $conn = $this->getConnectionByUser($user);
                if($conn){
                    $conn->send(is_array($message) ? json_encode($message) : $message);
                }
            }
            if($type == self::TEXT_TYPE_GET_ONLINE_USER){
                $userList = array_keys($this->userItems);
                $data = [];
                foreach($userList as $item){
                    $data[] = [
                        'user'=>$item,
                        'lastTime'=>$this->userItems[$item]['lastTime'],
                        'ip'=>$this->userItems[$item]['conn']->getRemoteIp(),
                    ];
                }
                return json_encode($data);
            }
        }catch (ErrorException $e){
            dump("发送失败：".$e->getMessage());
            return "Error：".$e->getMessage();
        }
        return 'Success';
    }

    /**
     * 收到用户端消息处理
     */
    protected function commandOnMessage($user,array $data){
        dump(json_encode($data,JSON_UNESCAPED_UNICODE));
        $type = $data['type'] ?? '';
        if($type == 'pong'){

        }
        if($type == 'ping'){
            $message = [
                'type'=>self::TEXT_TYPE_SEND_MESSAGE,
                'user'=>$user,
                'message'=>[
                    'type'=>'pong',
                    'time'=>time()
                ]
            ];
            $this->textSendMessage($message);
        }
    }

    protected function getUserByConnection(TcpConnection $connection){
        foreach($this->userItems as $user=>$item){
            if($item['conn'] == $connection){
                return $user;
            }
        }
        return false;
    }

    protected function getConnectionByUser($user){
        if(isset($this->userItems[$user])){
            return $this->userItems[$user]['conn'];
        }
        return false;
    }
    /**
     * 统一关闭客户端
     */
    protected function closeByUser($user){
        if(isset($this->userItems[$user])){
            $this->userItems[$user]['conn']->close();
            unset($this->userItems[$user]);
        }
    }



    /** websocket内部发送消息
     * @param array $data
     * @return false|string
     *  $message = [];
        $message['type'] = Websocket::TEXT_TYPE_SEND_MESSAGE;
        $message['user'] = 1;
        $message['message'] = [
            'time'=>time()
        ];
        $res = Websocket::sendData($message);
     */
    public static function sendData($data = []){
        $client = stream_socket_client('tcp://127.0.0.1:5678');
        if (!$client) {
            return false;
        }
        if(is_array($data)){
            $data = json_encode($data);
        }
        $message = $data."\n";
        fwrite($client, $message);
        $response = fgets($client);
        fclose($client);
        return $response;
    }

    /** websocket内部
     * 获取在线用户
     */
    public static function getOnlineUser(){
        $message = [];
        $message['type'] = self::TEXT_TYPE_GET_ONLINE_USER;
        $res = self::sendData($message);
        if($res){
            $res = json_decode($res,true);
            return AjaxUtils::success(['items'=>$res]);
        }else{
            return AjaxUtils::error('获取失败');
        }

    }
}