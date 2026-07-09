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
use app\common\enums\StatusEnum;
use app\common\model\PermsModel;
use app\common\utils\TimeUtils;

class PermsService extends Service
{
    public static function getTreeData($parentId = 0, $fields = ['id', 'name', 'parent_id', 'sort'])
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
        ], ['id'=>intval($post['id'])]);
    }

    public static function del(array $ids): void
    {
        PermsModel::destroy($ids,true);
        PermsModel::where('parent_id','in',$ids)->delete();
    }


}