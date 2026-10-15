<?php

declare (strict_types = 1);

namespace app\admin\service;


use app\common\basics\Service;
use app\common\enums\StatusEnum;
use app\common\exception\RequestException;
use app\common\model\PermsModel;
use app\common\model\RolePermsModel;
use app\common\utils\CacheUtils;
use app\common\utils\TimeUtils;
use think\facade\Db;

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

    public static function add(array $post,array $userItem): void
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
        CacheUtils::clearTag('perms');
    }

    public static function edit(array $post,array $userItem): void
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
        CacheUtils::clearTag('perms');
    }

    public static function del(array $ids,array $userItem): void
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if (empty($ids)) {
            throw new RequestException('请选择要删除的节点');
        }

        // 一次取出全部节点，收集被删节点的整棵子树
        // （界面提示的是「删除该节点及其所有子节点」，必须真的递归删除）
        $childMap = [];
        foreach (PermsModel::field('id,parent_id')->select()->toArray() as $node) {
            $childMap[(int) $node['parent_id']][] = (int) $node['id'];
        }

        $deleteIds = [];
        $stack = $ids;
        while (!empty($stack)) {
            $id = (int) array_pop($stack);
            if (isset($deleteIds[$id])) {
                continue;
            }
            $deleteIds[$id] = true;
            foreach ($childMap[$id] ?? [] as $childId) {
                $stack[] = $childId;
            }
        }
        $deleteIds = array_keys($deleteIds);

        Db::transaction(function () use ($deleteIds) {
            // 同步清理角色授权引用，避免残留无效授权
            RolePermsModel::whereIn('perms_id', $deleteIds)->delete();
            PermsModel::whereIn('id', $deleteIds)->delete();
        });

        CacheUtils::clearTag('perms');
    }


}