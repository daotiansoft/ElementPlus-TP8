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

namespace app\super\service;


use app\common\basics\Service;
use app\common\enums\CodeEnum;
use app\common\enums\StatusEnum;
use app\common\model\PermsModel;
use app\common\model\RolePermsModel;
use app\common\model\RolesModel;
use app\common\utils\TimeUtils;

class RolePermsService extends Service
{
    public static function save(array $post): void
    {
        $type = $post['type'];
        $perms_ids = $post['perms_ids'];

        $permModel = new PermsModel();
        $permsItems = $permModel->where('id','in',$perms_ids)->select();
        $model = new RolePermsModel();
        $items = [];
        foreach($permsItems as $permsItem){
            if($permsItem['route_status'] == StatusEnum::STATUS_0){
                $items[]=[
                    'type'=>$type,
                    'perms_id'=>$permsItem['id'],
                    'create_time'=>time(),
                    'update_time'=>time(),
                ];
            }

        }
        $model->where('type','=',$type)->delete();
        $model->insertAll($items);
    }

}