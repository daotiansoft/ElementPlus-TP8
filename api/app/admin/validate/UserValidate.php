<?php
namespace app\admin\validate;


use app\common\basics\Validate;
use app\common\model\RolesModel;

class UserValidate extends Validate
{
    protected $rule =   [
        'type'  => 'require|array|checkRoles',
        'username'  => 'require|unique:user',
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
    protected function checkRoles($value): bool|string
    {
        foreach ($value as $type) {
            if (!is_string($type) || $type === '' || str_contains($type, ',')) {
                return '角色代码格式错误';
            }
        }
        if (mb_strlen(implode(',', array_unique($value))) > 255) {
            return '角色代码总长度不能超过255个字符';
        }
        return count(array_unique($value)) === RolesModel::whereIn('type', $value)->count()
            ? true : '角色不存在';
    }

    protected $scene = [
        // 编辑场景由 Member::edit 显式指定，仅校验角色与账号名
        'edit'  =>  ['type','username'],
    ];

}