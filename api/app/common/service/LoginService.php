<?php
// +----------------------------------------------------------------------
// | WaitAdmin快速开发后台管理系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习程序代码,建议反馈是我们前进的动力
// | 程序完全开源可支持商用,允许去除界面版权信息
// | gitee:   https://gitee.com/wafts/waitadmin-php
// | github:  https://github.com/topwait/waitadmin-php
// | 官方网站: https://www.waitadmin.cn
// | WaitAdmin团队版权所有并拥有最终解释权
// +----------------------------------------------------------------------
// | Author: WaitAdmin Team <2474369941@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\common\service;

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
        $cache_key = 'jwt_key';
        $key = Cache::get($cache_key);
        if(empty($key)){
            $key = md5(get_rand_str(32).get_rand_str(32));
            Cache::set($cache_key,$key);
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
            "exp" => time() + 3600 * 24 * 7, // 过期时间（例如，1小时后过期）
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
        if(!$userItem || $userItem['status'] != UserModel::STATUS_ON){
            throw new RequestException('账号不存在或已停用！');
        }

        if(isset($params['password']) && get_password($params['password'],$userItem['rand_code']) != $userItem['password']){
            $loginFailCount += 1;

            Cache::set($cacke_key,$loginFailCount);
            $loginLogModel->log($userItem['id'],'',$loginLogModel::STATUS_FAIL,request()->ip());

            if($loginFailCount >= $loginFailMax){
                //锁定账号
                $userModel->where('username','=',$params['username'])->update(['status'=>UserModel::STATUS_OFF]);
                //清空重试失败次数
                Cache::delete($cacke_key);

                throw new RequestException('密码重试次数过多，账号已停用！');
            }

            throw new RequestException('密码不正确，您还可以尝试['.($loginFailMax - $loginFailCount).']次!');
        }

        //清空重试失败次数
        Cache::delete($cacke_key);

        $loginLogModel->log($userItem['id'],'',$loginLogModel::STATUS_SUCC,request()->ip());

        //系统停用 限制登录
        if($userItem['type'] != UserModel::TYPE_ADMIN){
            $config = new ConfigModel();
            $site_status = intval($config->getContentByKey('site_status'));
            if($site_status != $config::SITE_STATUS_ON){
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