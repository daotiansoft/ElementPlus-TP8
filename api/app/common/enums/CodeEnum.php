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

namespace app\common\enums;

/**
 * code枚举类
 */
class CodeEnum
{
    const SUCCESS   = 1; //操作成功;
    const ERROR   = -1; //操作失败;
    const NOT_AUTH   = -10001; //未登录
    const NOT_PERMS   = -10002; //无权限


    /**
     * 根据Code获取描述
     *
     * @param int $code
     * @return string
     * @author zero
     */
    public static function getMsgByCode(int $code): string
    {
        $desc = [
            self::SUCCESS     => '操作成功',
            self::ERROR     => '操作失败',
            self::NOT_AUTH     => '未登录',
            self::NOT_PERMS     => '无权限',
        ];

        return $desc[$code] ?? '未知异常';
    }
}