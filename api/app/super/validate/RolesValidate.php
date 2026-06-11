<?php
namespace app\super\validate;

use app\common\basics\Validate;

class RolesValidate extends Validate
{
    protected $rule =   [
        'type'  => 'require|max:10',
        'name'  => 'require'
    ];
    public function __construct()
    {
        $this->field = [
            'type'        => '角色代码',
            'name'        => '名称',
        ];
        parent::__construct();
    }
    protected $scene = [
        'add'  =>  ['type','name'],
    ];

}