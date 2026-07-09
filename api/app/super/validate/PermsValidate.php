<?php
namespace app\super\validate;

use app\common\basics\Validate;

class PermsValidate extends Validate
{
    protected $rule =   [
        'name'  => 'require',
        'module'  => 'require',
        'controller'  => 'require',
        'action'  => 'require'
    ];

    public function __construct()
    {
        $this->field = [
            'type'        => '角色代码',
            'name'        => '名称',
            'module'        => '模块',
            'controller'   => '控制器',
            'action'   => '方法名',
        ];
        parent::__construct();
    }

    protected $scene = [
        'add'  =>  ['name'],
        'edit'  =>  ['name'],
    ];

}