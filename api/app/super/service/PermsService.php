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
use app\common\model\PermsModel;
use app\common\utils\TimeUtils;

class PermsService extends Service
{

    public static function lists(array $get): array
    {
        self::setSearch([
            '%like%'   => ['action@perms_model.action'],
            '='        => ['type@perms_model.type','name@perms_model.name','module@perms_model.module','controller@perms_model.controller','action@perms_model.action']
        ],$get);

        $model = new PermsModel();
        $lists = $model
            ->withJoin(['role'])
            ->where(self::$searchWhere)
            ->order('id desc')
            ->paginate([
                'page'      => $get['page']  ?? 1,
                'list_rows' => $get['pagesize'] ?? 20
            ])->toArray();
        foreach ($lists['data'] as &$item) {
            $item['type_name'] = $item['role']['name'] ?? '';

            unset($item['role']);
        }
        return ['count'=>$lists['total'], 'items'=>$lists['data']] ?? [];
    }

    public static function add(array $post): void
    {
        PermsModel::create([
            'type'          => $post['type'],
            'name'        => $post['name'],
            'module'        => $post['module'],
            'controller'        => $post['controller'],
            'action'        => $post['action'],
            'status'        => $post['status'] ?? 1,
        ]);
    }

    public static function edit(array $post): void
    {
        PermsModel::update([
            'type'          => $post['type'],
            'name'        => $post['name'],
            'module'        => $post['module'],
            'controller'        => $post['controller'],
            'action'        => $post['action'],
            'status'        => $post['status'] ?? 1,
        ], ['id'=>intval($post['id'])]);
    }

    public static function del(array $ids): void
    {
        if(!in_array(1,$ids)){
            PermsModel::destroy($ids,true);
        }
    }


}