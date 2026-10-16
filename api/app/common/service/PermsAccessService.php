<?php

declare (strict_types = 1);

namespace app\common\service;

use app\common\basics\Service;
use app\common\model\PermsModel;
use app\common\model\RolePermsModel;
use app\common\utils\CacheUtils;

class PermsAccessService extends Service
{
    const CACHE_TAG = 'perms';

    /**
     * Return registered menu/page paths implied by a role's grants.
     */
    public static function getRoutes(string $type, int $parentId = 0): array
    {
        $cacheKey = 'perms_routes_v2_' . $type;
        $cached = CacheUtils::get($cacheKey);
        if (is_array($cached)) {
            return $cached;
        }

        $grantedIds = (new RolePermsModel())
            ->where('type', '=', $type)
            ->column('perms_id');
        if (empty($grantedIds)) {
            return [];
        }

        $rows = (new PermsModel())
            ->field('id,parent_id,name,node_type,route_path,status,sort')
            ->select()
            ->toArray();
        $indexed = [];
        foreach ($rows as $row) {
            $indexed[(int) $row['id']] = $row;
        }

        $routeNodes = [];
        foreach ($grantedIds as $grantedId) {
            $id = (int) $grantedId;
            if (!isset($indexed[$id]) || $indexed[$id]['node_type'] === 'api') {
                continue;
            }

            $visited = [];
            $chain = [];
            $enabled = true;
            while (isset($indexed[$id]) && !isset($visited[$id])) {
                $visited[$id] = true;
                $node = $indexed[$id];
                if ((int) $node['status'] !== 1) {
                    $enabled = false;
                    break;
                }
                $chain[] = $node;
                $id = (int) $node['parent_id'];
                if ($id <= 0) {
                    break;
                }
            }
            if ($id > 0) {
                $enabled = false;
            }
            if (!$enabled) {
                continue;
            }

            foreach ($chain as $node) {
                if (in_array($node['node_type'], ['group', 'page'], true)
                    && !empty($node['route_path'])
                ) {
                    $routeNodes[(int) $node['id']] = [
                        'id' => (int) $node['id'],
                        'name' => $node['route_path'],
                        'parent_id' => (int) $node['parent_id'],
                        'node_type' => $node['node_type'],
                        'sort' => (int) $node['sort'],
                    ];
                }
            }
        }

        $routeNodeIds = array_fill_keys(array_keys($routeNodes), true);
        foreach ($routeNodes as &$node) {
            if ($node['parent_id'] > 0 && !isset($routeNodeIds[$node['parent_id']])) {
                $node['parent_id'] = 0;
            }
        }
        unset($node);

        $tree = self::pruneEmptyGroups(self::buildTree(array_values($routeNodes), $parentId));
        CacheUtils::set(self::CACHE_TAG, $cacheKey, $tree);
        return $tree;
    }

    /**
     * Return active backend endpoint bindings for the role.
     */
    public static function getPowerSet(string $type): array
    {
        $cacheKey = 'perms_power_v2_' . $type;
        $cached = CacheUtils::get($cacheKey);
        if (is_array($cached)) {
            return $cached;
        }

        $grantedIds = (new RolePermsModel())
            ->where('type', '=', $type)
            ->column('perms_id');
        if (empty($grantedIds)) {
            return [];
        }

        $rows = (new PermsModel())
            ->field('id,parent_id,node_type,module,controller,action,status')
            ->select()
            ->toArray();
        $indexed = [];
        foreach ($rows as $row) {
            $indexed[(int) $row['id']] = $row;
        }

        $set = [];
        foreach ($grantedIds as $grantedId) {
            $id = (int) $grantedId;
            if (!isset($indexed[$id])) {
                continue;
            }

            $node = $indexed[$id];
            if (!in_array($node['node_type'], ['action', 'api'], true)
                || empty($node['module'])
                || empty($node['controller'])
                || empty($node['action'])
            ) {
                continue;
            }

            $visited = [];
            $enabled = true;
            while (isset($indexed[$id]) && !isset($visited[$id])) {
                $visited[$id] = true;
                if ((int) $indexed[$id]['status'] !== 1) {
                    $enabled = false;
                    break;
                }
                $id = (int) $indexed[$id]['parent_id'];
                if ($id <= 0) {
                    break;
                }
            }
            if ($id > 0) {
                $enabled = false;
            }

            if ($enabled) {
                $set[strtolower($node['module'] . '.' . $node['controller'] . '.' . $node['action'])] = true;
            }
        }

        CacheUtils::set(self::CACHE_TAG, $cacheKey, $set);
        return $set;
    }

    private static function buildTree(array $nodes, int $parentId): array
    {
        $childrenByParent = [];
        foreach ($nodes as $node) {
            $childrenByParent[(int) $node['parent_id']][] = $node;
        }
        foreach ($childrenByParent as &$children) {
            usort($children, static function (array $left, array $right): int {
                $sortOrder = (int) $left['sort'] <=> (int) $right['sort'];
                return $sortOrder !== 0 ? $sortOrder : ((int) $left['id'] <=> (int) $right['id']);
            });
        }
        unset($children);

        $build = function (int $id, array $ancestors = []) use (&$build, $childrenByParent): array {
            $result = [];
            foreach ($childrenByParent[$id] ?? [] as $node) {
                $nodeId = (int) $node['id'];
                if (isset($ancestors[$nodeId])) {
                    continue;
                }
                $nextAncestors = $ancestors;
                $nextAncestors[$nodeId] = true;
                $children = $build($nodeId, $nextAncestors);
                $item = [
                    'id' => $nodeId,
                    'name' => $node['name'],
                    'parent_id' => (int) $node['parent_id'],
                    'node_type' => $node['node_type'],
                ];
                if (!empty($children)) {
                    $item['children'] = $children;
                }
                $result[] = $item;
            }
            return $result;
        };

        return $build($parentId);
    }

    private static function pruneEmptyGroups(array $nodes): array
    {
        $result = [];
        foreach ($nodes as $node) {
            $children = self::pruneEmptyGroups($node['children'] ?? []);
            if ($node['node_type'] === 'group' && empty($children)) {
                continue;
            }
            if (!empty($children)) {
                $node['children'] = $children;
            } else {
                unset($node['children']);
            }
            $result[] = $node;
        }
        return $result;
    }
}
