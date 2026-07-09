<?php
//下级
namespace app\super\controller;

use app\common\basics\Auth;
use app\super\service\RolePermsService;
use app\common\utils\AjaxUtils;
use think\App;


class RolePerms extends Auth
{
    public function save(){
        RolePermsService::save($this->request->param());
        return AjaxUtils::success();
    }
}
