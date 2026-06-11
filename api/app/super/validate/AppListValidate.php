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

namespace app\super\validate;

use app\common\basics\Validate;


class AppListValidate extends Validate
{
    protected $rule = [
        'id'           => 'require|integer',
        'ids'          => 'require|array',
        'uid'        => 'require',
        'appid'        => 'max:32',
        'secret'        => 'max:32',
        'status'   => 'require|in:-1,1'
    ];

    public function __construct()
    {
        $this->field = [
            'uid'        => '账号',
            'appid'        => 'APPID',
            'secret'        => 'SECRET',
            'status'   => '状态',
        ];
        parent::__construct();
    }
}