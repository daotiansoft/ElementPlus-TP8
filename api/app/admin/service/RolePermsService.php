<?php

declare (strict_types = 1);

namespace app\admin\service;


use app\common\basics\Service;
use app\common\enums\CodeEnum;
use app\common\enums\StatusEnum;
use app\common\exception\RequestException;
use app\common\model\PermsModel;
use app\common\model\RolePermsModel;
use app\common\model\RolesModel;
use app\common\utils\CacheUtils;
use app\common\utils\TimeUtils;
use think\facade\Db;

class RolePermsService extends Service
{
    public static function save(array $post): void
    {
        $type = trim((string) ($post['type'] ?? ''));
        $perms_ids = array_values(array_unique(array_filter(array_map('intval', (array) ($post['perms_ids'] ?? [])))));
        if ($type === '') {
            throw new RequestException('缺少角色标识');
        }

        $items = [];
        if (!empty($perms_ids)) {
            $permModel = new PermsModel();
            $permsItems = $permModel->where('id','in',$perms_ids)->select();
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
        }

        // 先删后插必须在事务内，否则中途失败会让该角色的权限全部丢失
        $model = new RolePermsModel();
        Db::transaction(function () use ($model, $type, $items) {
            $model->where('type','=',$type)->delete();
            if (!empty($items)) {
                $model->insertAll($items);
            }
        });

        CacheUtils::clearTag('perms');
    }

}