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


use app\common\basics\Service;
use app\common\enums\StatusEnum;
use app\common\model\PermsModel;
use app\common\utils\TimeUtils;

class PermsService extends Service
{
    public static function getRoutes($type,$parentId = 0)
    {
        $model = new PermsModel();
        $perms = $model
            ->withJoin('roleperms')
            ->where('perms_model.parent_id','=', $parentId)
            ->where('route_status','=', StatusEnum::STATUS_1)
            ->where('roleperms.type','=', $type)
            ->order('sort', 'asc')
            ->select();
        $result = [];
        foreach ($perms as $perm) {
            $item = $perm->toArray();
            $data = [
                'name'=>$item['route_name'],
            ];
            $children = self::getRoutes($type,$perm->id);
            if (!empty($children)) {
                $data['children'] = $children;
            }
            $result[] = $data;
        }

        return $result;
    }




}