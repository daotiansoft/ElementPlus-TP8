<?php
namespace app\super\validate;


use app\common\basics\Validate;

class UserValidate extends Validate
{
    protected $rule =   [
        'type'  => 'require',
        'username'  => 'require',
        'password'  => 'require|min:6'
    ];
    public function __construct()
    {
        $this->field = [
            'type'        => '角色代码',
            'username'        => '账号名称',
            'password'        => '密码',
        ];
        parent::__construct();
    }
    protected $scene = [
        'add'  =>  ['type','username','password'],
        'edit'  =>  ['type','username'],
        'password'  =>  ['password'],
        'login'  =>  ['username','password'],
    ];

}