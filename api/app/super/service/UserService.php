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
use app\common\model\UserModel;
use app\common\utils\TimeUtils;
use think\facade\Cache;

class UserService extends Service
{
    public static function getItemByUsername($username){
        $model = new UserModel();
        return $model->where('username','=',$username)->find();
    }

    public static function lists(array $get): array
    {
        self::setSearch([
            '%like%'   => ['keyword@user_model.username'],
            'datetime' => ['time@user_model.create_time'],
        ],$get);
        $model = new UserModel();
        $lists = $model
            ->withoutField(['password','rand_code'])
            ->withJoin(['role'],'LEFT')
            ->where(self::$searchWhere)
            ->order('id desc')
            ->paginate([
                'page'      => $get['page']  ?? 1,
                'list_rows' => $get['pagesize'] ?? 20
            ])->toArray();
        foreach ($lists['data'] as &$item) {
            $item['role_name'] = $item['role']['name'] ?? '';
            $item['create_time'] = TimeUtils::formatTime($item['create_time']);
            $item['update_time'] = TimeUtils::formatTime($item['update_time']);

            unset($item['role']);
        }

        return ['count'=>$lists['total'], 'items'=>$lists['data']] ?? [];
    }

    public static function add(array $post): void
    {
        UserModel::create([
            'type'          => $post['type'],
            'username'        => $post['username'],
            'password'        => $post['password'],
            'rand_code'        => $post['rand_code'],
            'status'        => $post['status'] ?? 1,
            'create_time'  => time(),
            'update_time'  => time()
        ]);
    }

    public static function edit(array $post): void
    {
        $data = [
            'type'          => $post['type'],
            'username'        => $post['username'],
            'password'        => $post['password'] ?? '',
            'rand_code'        => $post['rand_code'] ?? '',
            'status'        => $post['status'] ?? 1,
            'update_time'  => time()
        ];
        if(empty($data['password'])){
            unset($data['password']);
            unset($data['rand_code']);
        }
        UserModel::update($data, ['id'=>intval($post['id'])]);

        //重置登录次数限制
        $cacke_key = 'login_fail_' . $data['username'];
        Cache::delete($cacke_key);
    }

    public static function del(array $ids): void
    {
        if(!in_array(1,$ids)){
            UserModel::destroy($ids,true);
        }
    }


}