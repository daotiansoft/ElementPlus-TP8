<?php

declare (strict_types = 1);

namespace app\admin\service;


use app\common\basics\Service;
use app\common\enums\CodeEnum;
use app\common\enums\StatusEnum;
use app\common\model\PermsModel;
use app\common\model\RolePermsModel;
use app\common\model\RolesModel;
use app\common\utils\CacheUtils;
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
        CacheUtils::clearTag('perms');
    }

}