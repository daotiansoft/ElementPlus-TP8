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

namespace app\common\basics;

use app\BaseController;
use app\common\exception\NotAuthException;
use app\common\model\PermsModel;
use app\common\model\UserModel;
use app\common\service\LoginService;
use think\App;
use think\facade\Request;


/**
 * 后台基类
 */
abstract class Auth extends BaseController
{
    /**
     * 账号信息
     */
    protected $userItem = [];

    /**
     * 不校验登录的方法
     * @var array
     */
    protected $notNeedLogin = [];

    /**
     * 不校验权限的方法
     * @var array
     */
    protected $notNeedPower = [];

    /**
     * 构造方法
     *
     * Backend constructor.
     * @param App $app
     * @throws NotAuthException
     */
    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->checkLogin();

        $this->checkPower();
    }

    /**
     * 初始方法
     *
     * @return void
     * @throws NotAuthException
     * @author zero
     */
    protected function initialize(): void
    {

    }

    /**
     * 验证登录
     *
     * @return bool
     * @throws NotAuthException
     * @author zero
     */
    protected function checkLogin(): bool
    {
        $module = app('http')->getName();
        $class = request()->controller();
        $action = request()->action();
        if (in_array($action, $this->notNeedLogin) || (isset($this->notNeedLogin[0]) && $this->notNeedLogin[0] == '*')) {
            return true;
        } else {
            $token = request()->header('Api-Token');
            if(empty($token)){
                throw new NotAuthException('请登录后再操作!');
            }
            $userData = LoginService::checkAuthorization($token);
            if ($userData) {
                $userModel = new UserModel();
                $this->userItem = $userModel->getItemById($userData->uid);
                return true;
            } else {
                throw new NotAuthException('请登录后再操作!');
            }
        }
    }

    /**
     * 验证权限
     *
     * @return bool
     * @throws NotAuthException
     * @author zero
     */
    protected function checkPower(): bool
    {
        $module = strtolower(app('http')->getName());
        $controller = strtolower(request()->controller());
        $action = strtolower(request()->action());
        if (in_array($action, $this->notNeedPower)  || (isset($this->notNeedPower[0]) && $this->notNeedPower[0] == '*')) {
            return true;
        }
        if(!isset($this->userItem['type'])){
            return false;
        }
        if($this->userItem['id'] == 1){
            return true;
        }
        $requestUrl = lcfirst( request()->baseUrl());
        $requestUrl = ltrim($requestUrl, '/');
        $requestUrl = strtolower(($requestUrl == 'index' || !$requestUrl) ? 'index/index' : $requestUrl);

        $permsModel = new PermsModel();
        $items = $permsModel
            ->whereIn('type', $this->userItem['type'])
            ->where(['status'=>1])
            ->field('module,controller,action')
            ->select()->toArray();

        foreach($items as $item){
            if(
                strtolower($item['module']) == $module
                && strtolower($item['controller']) == $controller
                && strtolower($item['action']) == $action
            ){
                return true;
            }
        }

        //判断是否存在 存在话 就创建一个无权限的
        /*if(check_action_exists($module,request()->controller(),request()->action())){
            $data = [];
            $data['type'] = $this->userItem['type'];
            $data['module'] = $module;
            $data['controller'] = request()->controller();
            $data['action'] = request()->action();

            $item = $permsModel->where($data)->field('id')->find();
            if(!$item){
                $data['status'] = PermsModel::STATUS_OFF;
                $permsModel->insert($data);
            }
        }*/
        throw new NotAuthException('无权限['.$requestUrl.']');
    }

}