<?php

declare (strict_types = 1);

namespace app\super\service;

use app\common\basics\Service;
use app\common\model\ConfigModel;
use app\common\model\UserModel;
use app\common\utils\TimeUtils;
use think\facade\Cache;

class ConfigService extends Service
{
    public static function lists(array $get): array
    {
        self::setSearch([
            '%like%'   => ['keyword@config_model.name'],
            '='        => ['key@config_model.key','group_id@config_model.group_id'],
            'datetime' => ['time@config_model.create_time'],
        ],$get);
        $model = new ConfigModel();
        $lists = $model
            ->withJoin(['group'],'LEFT')
            ->where(self::$searchWhere)
            ->order('sort desc')
            ->paginate([
                'page'      => $get['page']  ?? 1,
                'list_rows' => $get['pagesize'] ?? 20
            ])->toArray();
        foreach ($lists['data'] as &$item) {
            $item['group_name'] = $item['group']['name'] ?? '';
            $item['create_time'] = TimeUtils::formatTime($item['create_time']);
            $item['update_time'] = TimeUtils::formatTime($item['update_time']);

            unset($item['group']);
        }

        return ['count'=>$lists['total'], 'items'=>$lists['data']] ?? [];
    }

    public static function add(array $post): void
    {
        ConfigModel::create([
            'group_id'          => $post['group_id'],
            'key'        => $post['key'],
            'type'        => $post['type'],
            'name'        => $post['name'],
            'content'        => $post['content'],
            'placeholder'        => $post['placeholder'],
            'params'        => $post['params'],
            'sort'        => $post['sort'],
            'show'        => $post['show'],
            'create_time'  => time(),
            'update_time'  => time()
        ]);
    }

    public static function edit(array $post): void
    {
        ConfigModel::update([
            'group_id'          => $post['group_id'],
            'key'        => $post['key'],
            'type'        => $post['type'],
            'name'        => $post['name'],
            'content'        => $post['content'],
            'placeholder'        => $post['placeholder'],
            'params'        => $post['params'],
            'sort'        => $post['sort'],
            'show'        => $post['show'],
            'update_time'  => time()
        ], ['id'=>intval($post['id'])]);
    }

    public static function del(array $ids): void
    {
        ConfigModel::destroy($ids,true);
    }


}