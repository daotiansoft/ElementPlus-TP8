<?php
namespace app\common\model;

use think\model\relation\HasOne;

class PermsModel extends CommonModel
{
    protected $name = 'perms';

    protected $schema = [
        'id'=>'int',
        'parent_id'=>'int',
        'name'=>'string',
        'menu'=>'int',
        'module'=>'string',
        'controller'=>'string',
        'action'=>'string',
        'sort'=>'int',
        'status'=>'int',
        'route_status'=>'int',
        'route_name'=>'string',
        'only_api'=>'int',

        'create_time'=>'int',
        'update_time'=>'int',
    ];

    public function roleperms(): HasOne
    {
        return $this->hasOne(RolePermsModel::class, 'perms_id', 'id');
    }
}
