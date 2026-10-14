<?php

declare (strict_types = 1);

namespace app\common\basics;

use app\BaseController;
use app\common\enums\StatusEnum;
use app\common\exception\NotAuthException;
use app\common\exception\NotPermsException;
use app\common\model\UserModel;
use app\common\service\LoginService;
use app\common\service\PermsService;
use think\App;

/**
 * 后台基类（Api-Token / JWT 鉴权）
 *
 * 与开放接口基类 Api 的区别：登录态来自 Api-Token（JWT）校验，
 * 而非 appid + sign 签名；权限按登录账号的角色校验。
 */
abstract class Auth extends BaseController
{
    /**
     * 超级管理员账号ID（不受权限限制）
     */
    protected const SUPER_ADMIN_ID = 1;

    /**
     * 账号信息
     * @var array
     */
    protected $userItem = [];

    /**
     * 不校验登录的方法，['*'] 表示全部不校验
     * @var array
     */
    protected $notNeedLogin = [];

    /**
     * 不校验权限的方法，['*'] 表示全部不校验
     * @var array
     */
    protected $notNeedPower = [];

    /**
     * 构造方法
     *
     * Backend constructor.
     * @param App $app
     * @throws NotAuthException
     * @throws NotPermsException
     */
    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->checkLogin();

        $this->checkPower();
    }

    /**
     * 验证登录（Api-Token）
     *
     * @return void
     * @throws NotAuthException
     * @author zero
     */
    protected function checkLogin(): void
    {
        if ($this->isWithoutLogin()) {
            return;
        }

        $token = $this->request->header('Api-Token');
        if (empty($token)) {
            throw new NotAuthException('请登录后再操作!');
        }

        $userData = LoginService::checkAuthorization($token);
        if (empty($userData)) {
            throw new NotAuthException('请登录后再操作!');
        }

        $userItem = (new UserModel())->getItemById($userData->uid ?? 0);
        if (empty($userItem)) {
            throw new NotAuthException('登录账号不存在，请重新登录!');
        }
        if ((int) $userItem['status'] !== StatusEnum::STATUS_1) {
            throw new NotAuthException('账号已停用，请联系管理员!');
        }

        $this->userItem = $userItem;
    }

    /**
     * 验证权限
     *
     * @return bool
     * @throws NotPermsException
     * @author zero
     */
    protected function checkPower(): bool
    {
        $module = strtolower(app('http')->getName());
        $controller = strtolower($this->request->controller());
        $action = strtolower($this->request->action());
        // 免登录的方法没有登录身份，也就无从校验权限，直接放行
        if ($this->isWithoutLogin() || $this->isSkip($this->notNeedPower, $action)) {
            return true;
        }
        if (empty($this->userItem['type'])) {
            throw new NotPermsException('无权限['.$this->requestUrl().']');
        }
        if ((int) $this->userItem['id'] === self::SUPER_ADMIN_ID) {
            return true;
        }

        $permsSet = PermsService::getPowerSet($this->userItem['type']);
        if (isset($permsSet[$module . '/' . $controller . '/' . $action])) {
            return true;
        }

        throw new NotPermsException('无权限['.$this->requestUrl().']');
    }

    /**
     * 当前请求是否免登录
     *
     * 免登录的方法不会注入登录身份，因此也不参与权限校验。
     *
     * @return bool
     */
    protected function isWithoutLogin(): bool
    {
        return $this->isSkip($this->notNeedLogin, $this->request->action());
    }

    /**
     * 是否命中免校验名单，['*'] 表示全部免校验
     *
     * @param array $skipList
     * @param string $action
     * @return bool
     */
    protected function isSkip(array $skipList, string $action): bool
    {
        return in_array('*', $skipList, true) || in_array($action, $skipList, true);
    }

    /**
     * 当前请求的规范化标识（用于错误提示）
     *
     * @return string
     */
    protected function requestUrl(): string
    {
        $url = ltrim(lcfirst($this->request->baseUrl()), '/');

        return strtolower(($url === 'index' || !$url) ? 'index/index' : $url);
    }
}