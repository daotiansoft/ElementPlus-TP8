<?php

declare (strict_types = 1);

namespace app\admin\service;


use app\common\basics\Service;
use app\common\enums\StatusEnum;
use app\common\model\RolesModel;
use app\common\utils\TimeUtils;

class RolesService extends Service
{

    public static function lists(array $get,array $userItem): array
    {
        self::setSearch([
            '%like%'   => ['keyword@remark'],
            '='        => ['type@type', 'name@name'],
            'datetime' => ['time@create_time'],
        ],$get);

        $model = new RolesModel();
        $lists = $model
            ->with('perms')
            ->where(self::$searchWhere)
            ->order('id desc')
            ->paginate([
                'page'      => $get['page']  ?? 1,
                'list_rows' => $get['pagesize'] ?? 20
            ])->toArray();
        foreach ($lists['data'] as &$item) {
            $item['create_time'] = TimeUtils::formatTime($item['create_time']);
            $item['update_time'] = TimeUtils::formatTime($item['update_time']);
            $item['perms_ids'] = array_column($item['perms'], 'perms_id');
            unset($item['perms']);
        }
        return ['count'=>$lists['total'], 'items'=>$lists['data']] ?? [];
    }

    public static function getOptions(array $userItem): array
    {
        $model = new RolesModel();
        return $model
            ->field('type,name')
            ->where(['status'=>StatusEnum::STATUS_1])
            ->order('id')
            ->select()->toArray();
    }

    public static function add(array $post,array $userItem): void
    {
        RolesModel::create([
            'type'          => $post['type'],
            'name'        => $post['name'],
            'status'        => $post['status'] ?? StatusEnum::STATUS_1,
            'remark'        => $post['remark'] ?? '',
            'create_time'  => time(),
            'update_time'  => time()
        ]);
    }

    public static function edit(array $post,array $userItem): void
    {
        RolesModel::update([
            'type'          => $post['type'],
            'name'        => $post['name'],
            'status'        => $post['status'] ?? StatusEnum::STATUS_1,
            'remark'        => $post['remark'] ?? '',
            'update_time'  => time()
        ], ['id'=>intval($post['id'])]);
    }

    public static function del(array $ids,array $userItem): void
    {
        RolesModel::destroy($ids,true);
    }


}