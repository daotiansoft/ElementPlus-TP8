<?php

declare (strict_types = 1);

namespace app\common\service;


use app\common\basics\Service;
use app\common\enums\StatusEnum;
use app\common\exception\RequestException;
use app\common\model\PermsModel;
use app\common\model\RolePermsModel;
use app\common\utils\TimeUtils;

class PermsService extends Service
{
    public static function getRoutes(string $type,int $parentId = 0):array
    {
        $model = new RolePermsModel();
        $perms_ids = $model->where('type','=',$type)->column('perms_id');
        $perms = self::getTreeByChildIdsRecursive($perms_ids);
        return $perms;
    }

    /**
     * 通过子ID获取完整树形结构（递归方式）
     * @param array $childIds
     * @return array
     */
    public static function getTreeByChildIdsRecursive(array $childIds):array
    {
        if (empty($childIds)) {
            return [];
        };
        // 递归获取所有父级数据
        $allData = self::getParentsRecursive($childIds);
        // 构建树形结构
        return self::buildTree($allData);
    }

    /**
     * 递归获取父级数据
     * @param array $ids
     * @param array $result
     * @return array
     */
    private static function getParentsRecursive(array $ids, array $result = []):array
    {
        if (empty($ids)) {
            return $result;
        }
        $_ids = [];
        foreach($ids as $id){
            $_ids = array_merge($_ids,self::getParentIds($id));
        }
        $ids = array_merge($_ids,$ids);
        $model = new PermsModel();
        // 查询当前ID的数据
        $data = $model->whereIn('id', $ids)->where('route_status','=',StatusEnum::STATUS_1)->field('id,route_name as name,parent_id')->select()->toArray();

        if (empty($data)) {
            return $result;
        }

        // 合并数据
        $result = array_merge($result, $data);

        // 获取父级ID
        $parentIds = array_unique(array_column($data, 'parent_id'));
        $parentIds = array_filter($parentIds, function($id) {
            return $id > 0;
        });

        // 递归获取父级数据
        return self::getParentsRecursive($parentIds, $result);
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

    public static function getParentIds($id, $parentIds = [])
    {
        $model = new PermsModel();
        $item = $model->getItemById($id);
        // 如果该父级节点还有上级（pid不为0），则继续向上递归查找
        if ($item && $item['parent_id'] > 0 && $item['only_api'] == StatusEnum::STATUS_0) {
            $parentIds[]=$item['parent_id'];
            // 递归调用，将当前节点的 pid 作为新目标向上查找
            $parentIds = array_merge($parentIds,self::getParentIds($item['parent_id'], $parentIds));
        }

        return array_values(array_unique($parentIds));
    }



}