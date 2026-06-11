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
use app\common\model\FormFieldModel;
use app\common\utils\TimeUtils;

class FormFieldService extends Service
{
    public static function lists(array $get): array
    {
        self::setSearch([
            '%like%'   => ['name'],
            '='        => ['key'],
            'datetime' => ['create_time'],
        ],$get);

        $model = new FormFieldModel();
        $lists = $model
            ->where(self::$searchWhere)
            ->order('id desc')
            ->paginate([
                'page'      => $get['page']  ?? 1,
                'list_rows' => $get['pagesize'] ?? 20
            ])->toArray();
        foreach ($lists['data'] as &$item) {
            $item['create_time'] = TimeUtils::formatTime($item['create_time']);
            $item['update_time'] = TimeUtils::formatTime($item['update_time']);
        }
        return ['count'=>$lists['total'], 'items'=>$lists['data']] ?? [];
    }

    public static function add(array $post): void
    {
        FormFieldModel::create([
            'key'          => $post['key'],
            'name'        => $post['name'],
            'field_json'        => $post['field_json'] ?? '',
            'create_time'  => time(),
            'update_time'  => time()
        ]);
    }

    public static function edit(array $post): void
    {
        FormFieldModel::update([
            'key'          => $post['key'],
            'name'        => $post['name'],
            'field_json'        => $post['field_json'] ?? '',
            'update_time'  => time()
        ], ['id'=>intval($post['id'])]);
    }

    public static function del(array $ids): void
    {
        FormFieldModel::destroy($ids,true);
    }


}