<?php

declare (strict_types = 1);

namespace app\super\service;


use app\common\basics\Service;
use app\common\enums\StatusEnum;
use app\common\model\PermsModel;
use app\common\utils\TimeUtils;

class PermsService extends Service
{
    public static function getTreeData(int $parentId = 0,array $fields = ['id', 'name', 'parent_id','only_api', 'sort']):array
    {
        $model = new PermsModel();
        $perms = $model->where('parent_id','=', $parentId)
            ->order('sort', 'asc')
            ->select();

        $result = [];
        foreach ($perms as $perm) {
            $item = $perm->toArray();
            $children = self::getTreeData($perm->id, $fields);
            if (!empty($children)) {
                $item['children'] = $children;
            }
            $result[] = $item;
        }

        return $result;
    }

    public static function add(array $post): void
    {
        PermsModel::create([
            'parent_id'          => $post['parent_id'],
            'name'        => $post['name'],
            'module'        => $post['module'],
            'controller'        => $post['controller'],
            'action'        => $post['action'],
            'sort'        => $post['sort'] ?? 50,
            'status'        => $post['status'] ?? StatusEnum::STATUS_1,
            'route_status'        => $post['route_status'] ?? StatusEnum::STATUS_0,
            'route_name'        => $post['route_name'] ?? '',
            'only_api'        => $post['only_api'] ?? StatusEnum::STATUS_0,
        ]);
    }

    public static function edit(array $post): void
    {
        PermsModel::update([
            'parent_id'          => $post['parent_id'],
            'name'        => $post['name'],
            'module'        => $post['module'],
            'controller'        => $post['controller'],
            'action'        => $post['action'],
            'sort'        => $post['sort'] ?? 50,
            'status'        => $post['status'] ?? StatusEnum::STATUS_1,
            'route_status'        => $post['route_status'] ?? StatusEnum::STATUS_0,
            'route_name'        => $post['route_name'] ?? '',
            'only_api'        => $post['only_api'] ?? StatusEnum::STATUS_0,
        ], ['id'=>intval($post['id'])]);
    }

    public static function del(array $ids): void
    {
        PermsModel::destroy($ids,true);
        PermsModel::where('parent_id','in',$ids)->delete();
    }


}