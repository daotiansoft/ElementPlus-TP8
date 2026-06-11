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
use app\common\exception\RequestException;
use app\common\model\AppListModel;
use app\common\utils\TimeUtils;

class AppListService extends Service
{
    public static function lists(array $get): array
    {
        self::setSearch([
            '%like%'   => ['keyword@app_list_model.remark'],
            '='        => ['status@app_list_model.status','appid@app_list_model.appid','username@user.username'],
            'datetime' => ['time@app_list_model.create_time'],
        ],$get);

        $model = new AppListModel();
        $lists = $model
            ->withJoin('user')
            ->where(self::$searchWhere)
            ->order('id desc')
            ->paginate([
                'page'      => $get['page']  ?? 1,
                'list_rows' => $get['pagesize'] ?? 20
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            $item['username'] = $item['user']['username'] ?? '';
            $item['create_time'] = TimeUtils::formatTime($item['create_time']);
            $item['update_time'] = TimeUtils::formatTime($item['update_time']);
            unset($item['user']);
        }

        return ['count'=>$lists['total'], 'items'=>$lists['data']] ?? [];
    }

    public static function add(array $post): void
    {
        AppListModel::create([
            'uid'          => $post['uid'],
            'appid'        => $post['appid'],
            'secret'        => $post['secret'],
            'status'        => $post['status'] ?? '',
            'remark'        => $post['remark'] ?? '',
            'create_time'  => time(),
            'update_time'  => time()
        ]);
    }

    public static function edit(array $post): void
    {
        AppListModel::update([
            'uid'          => $post['uid'],
            'appid'        => $post['appid'],
            'secret'        => $post['secret'],
            'status'        => $post['status'] ?? '',
            'remark'        => $post['remark'] ?? '',
            'update_time'  => time()
        ], ['id'=>intval($post['id'])]);
    }

    public static function del(array $ids): void
    {
        AppListModel::destroy($ids,true);
    }


}