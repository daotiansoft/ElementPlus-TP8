<?php
namespace app\common\model;

class UserModel extends CommonModel
{
    protected $name = 'user';

    //账号类型
    const TYPE_SUPER = 'super';

    protected $schema = [
        'id'=>'int',
        'type'=>'string',
        'username'=>'string',
        'nickname'=>'string',
        'password'=>'string',
        'avatar'=>'string',
        'balance'=>'int',
        'status'=>'int',
        'create_time'=>'int',
        'update_time'=>'int'
    ];

    public function getItemByUsername($username){
        return $this->where('username','=',$username)->find();
    }

    // 数据库存逗号分隔的角色标识，对外统一返回数组，兼容原单角色数据。
    public function getTypeAttr($value): array
    {
        return $value === '' ? [] : explode(',', $value);
    }

    public function setTypeAttr($value): string
    {
        return implode(',', array_unique($value));
    }

}
