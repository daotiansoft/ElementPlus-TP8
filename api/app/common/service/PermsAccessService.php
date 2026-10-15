<?php

declare (strict_types = 1);

namespace app\common\service;


use app\common\basics\Service;
use app\common\enums\StatusEnum;
use app\common\model\PermsModel;
use app\common\model\RolePermsModel;
use app\common\utils\CacheUtils;

class PermsAccessService extends Service
{
    /**
     * 权限缓存标签
     */
    const CACHE_TAG = 'perms';

    /**
     * 获取指定角色的路由菜单树
     */
    public static function getRoutes(string $type, int $parentId = 0): array
    {
        $cacheKey = 'perms_routes_' . $type;
        $cached = CacheUtils::get($cacheKey);
        if (is_array($cached)) {
            return $cached;
        }

        $rolePermsModel = new RolePermsModel();
        $grantedIds = $rolePermsModel->where('type', '=', $type)->column('perms_id');
        if (empty($grantedIds)) {
            return [];
        }

        // 一次性取出全部权限，避免逐条递归查库（去 N+1）
        $permsModel = new PermsModel();
        $perms = $permsModel
            ->field('id, route_name as name, parent_id, only_api, route_status')
            ->select()
            ->toArray();

        $indexed = [];
        foreach ($perms as $item) {
            $indexed[(int) $item['id']] = $item;
        }

        // 从已授权节点向上补齐所有父级，得到完整闭包集合
        $needIds = [];
        foreach ($grantedIds as $grantedId) {
            $id = (int) $grantedId;
            $visited = [];
            while (isset($indexed[$id]) && !isset($visited[$id])) {
                $visited[$id] = true;
                $needIds[$id] = true;
                $node = $indexed[$id];
                if ((int) $node['parent_id'] <= 0 || (int) $node['only_api'] === StatusEnum::STATUS_1) {
                    break;
                }
                $id = (int) $node['parent_id'];
            }
        }

        // 仅保留作为路由展示的节点
        $subset = [];
        foreach (array_keys($needIds) as $id) {
            if (isset($indexed[$id]) && (int) $indexed[$id]['route_status'] === StatusEnum::STATUS_1) {
                $subset[$id] = [
                    'id' => $indexed[$id]['id'],
                    'name' => $indexed[$id]['name'],
                    'parent_id' => $indexed[$id]['parent_id'],
                ];
            }
        }

        $tree = self::buildTree(array_values($subset));

        CacheUtils::set(self::CACHE_TAG, $cacheKey, $tree);
        return $tree;
    }

    /**
     * 获取指定角色的权限集合（module/controller/action）
     * 用于登录态权限校验，结果按角色缓存
     */
    public static function getPowerSet(string $type): array
    {
        $cacheKey = 'perms_power_' . $type;
        $cached = CacheUtils::get($cacheKey);
        if (is_array($cached)) {
            return $cached;
        }

        $rolePermsModel = new RolePermsModel();
        $permsIds = $rolePermsModel->where('type', '=', $type)->column('perms_id');
        if (empty($permsIds)) {
            return [];
        }

        $permsModel = new PermsModel();
        $items = $permsModel
            ->where('id', 'in', $permsIds)
            ->where('status', '=', StatusEnum::STATUS_1)
            ->field('module, controller, action')
            ->select()
            ->toArray();

        $set = [];
        foreach ($items as $item) {
            $set[strtolower($item['module'] . '/' . $item['controller'] . '/' . $item['action'])] = true;
        }

        CacheUtils::set(self::CACHE_TAG, $cacheKey, $set);
        return $set;
    }

    /**
     * 构建树形结构
     * @param array $data
     * @return array
     */
    private static function buildTree(array $data):array
    {
        // 按ID索引
        $indexed = [];
        foreach ($data as $item) {
            $item['children'] = [];
            $indexed[$item['id']] = $item;
        }

        $tree = [];
        foreach ($indexed as &$item) {
            if ($item['parent_id'] == 0) {
                // 根节点
                $tree[] = &$item;
            } else {
                // 非根节点，添加到父节点的children中
                if (isset($indexed[$item['parent_id']])) {
                    $indexed[$item['parent_id']]['children'][] = &$item;
                } else {
                    // 如果父节点不在数据中，作为根节点处理
                    $tree[] = &$item;
                }
            }
        }

        return $tree;
    }

}

