<?php

declare (strict_types = 1);

namespace app\common\service;

use app\common\enums\StatusEnum;
use app\common\exception\NotAuthException;
use app\common\exception\RequestException;
use app\common\model\ConfigModel;
use app\common\model\LoginLogModel;
use app\common\model\UserModel;
use app\common\utils\UrlUtils;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use think\facade\Cache;

/**
 * 登录服务类
 */
class LoginService
{
    protected static function getJwtKey(){
        $key = (string) config('jwt.secret', '');
        if (!empty($key)) {
            return $key;
        }

        // 未配置 JWT_SECRET 时，自动生成密钥并持久化到「根」runtime 目录。
        // 注意：多应用模式下 runtime_path() 会按应用分区（runtime/common、runtime/super…），
        // 必须使用根 runtime 目录，保证登录（common）与鉴权（super）共享同一密钥
        $keyFile = root_path() . 'runtime' . DIRECTORY_SEPARATOR . 'jwt_key';
        if (is_file($keyFile)) {
            $key = trim((string) file_get_contents($keyFile));
        }
        if (empty($key)) {
            $key = md5(get_rand_str(32) . get_rand_str(32));
            file_put_contents($keyFile, $key, LOCK_EX);
        }
        return $key;
    }

    /*
     * 创建授权信息
     */
    public static function buildAuthorization($data = []){
        $payload = array(
            "iss" => UrlUtils::getDomain(), // 发行人
            "aud" => UrlUtils::getDomain(), // 接收者
            "iat" => time(), // 发行时间
            "nbf" => time(), // 生效时间
            "exp" => time() + (int) config('jwt.expire', 3600 * 24 * 7), // 过期时间
            "data" => $data
        );
        $jwt = JWT::encode($payload, self::getJwtKey(), 'HS256');
        return $jwt;
    }

    /*
     * 验证授权信息 返回用户信息
     */
    public static function checkAuthorization($jwt_str = ''){
        if(empty($jwt_str)){
            throw new NotAuthException('登陆已失效，请重新登录！');
        }
        if(intval(Cache::get('jwt_token_die_' . md5($jwt_str))) > 0){
            throw new NotAuthException('登陆已失效，请重新登录！');
        }
        try {
            $decoded = JWT::decode($jwt_str, new Key(self::getJwtKey(), 'HS256'));
            return $decoded->data; // 获取用户信息
        }catch (\Exception $e){
            throw new NotAuthException('登陆已失效，请重新登录！');
        }
    }

    public static function login($params = []){
        $loginLogModel = new LoginLogModel();

        $cacke_key = 'login_fail_' . $params['username'];
        $loginFailCount = intval(Cache::get($cacke_key));
        $loginFailMax = 5; //最大尝试次数

        $userModel = new UserModel();
        $userItem = $userModel->where('username','=',$params['username'])->field($userModel->fields)->find();
        if(!$userItem || $userItem['status'] != StatusEnum::STATUS_1){
            throw new RequestException('账号不存在或已停用！');
        }

        if(isset($params['password']) && !verify_password($params['password'], $userItem['password'])){
            $loginFailCount += 1;

            Cache::set($cacke_key,$loginFailCount);
            $loginLogModel->log($userItem['id'],'',StatusEnum::STATUS_OFF,request()->ip());

            if($loginFailCount >= $loginFailMax){
                //锁定账号
                $userModel->where('username','=',$params['username'])->update(['status'=>StatusEnum::STATUS_OFF]);
                //清空重试失败次数
                Cache::delete($cacke_key);

                throw new RequestException('密码重试次数过多，账号已停用！');
            }

            throw new RequestException('密码不正确，您还可以尝试['.($loginFailMax - $loginFailCount).']次!');
        }

        //清空重试失败次数
        Cache::delete($cacke_key);

        $loginLogModel->log($userItem['id'],'',StatusEnum::STATUS_1,request()->ip());

        //系统停用 限制登录
        if($userItem['type'] != UserModel::TYPE_SUPER){
            $config = new ConfigModel();
            $site_status = intval($config->getContentByKey('site_status'));
            if($site_status != StatusEnum::STATUS_1){
                $site_stop_msg = $config->getContentByKey('site_stop_msg');
                throw new RequestException($site_stop_msg);
            }
        }

        $data = [
            'uid'=>$userItem['id'],
            'username'=>$userItem['username'],
            'type'=>$userItem['type']
        ];
        return self::buildAuthorization($data);
    }


    public static function logout($jwt_token){
        if(!empty($jwt_token)){
            Cache::set('jwt_token_die_' . md5($jwt_token),time(),60*60*24);
        }
    }
}