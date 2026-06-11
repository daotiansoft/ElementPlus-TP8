<?php
namespace app\common\model;

use think\model\relation\HasOne;

class UserModel extends CommonModel
{
    protected $name = 'user';

    //账号类型
    const TYPE_ADMIN = 'admin';
    const TYPE_USER = 'user';
    //状态
    const STATUS_ON=1;
    const STATUS_OFF=-1;

    protected $schema = [
        'id'=>'int',
        'type'=>'string',
        'username'=>'string',
        'nickname'=>'string',
        'password'=>'string',
        'avatar'=>'string',
        'rand_code'=>'string',
        'status'=>'int',
        'create_time'=>'int',
        'update_time'=>'int'
    ];

    public function getItemByUsername($username){
        return $this->where('username','=',$username)->find();
    }

    public function role(): HasOne
    {
        return $this->hasOne(RolesModel::class, 'type', 'type');
    }

}
