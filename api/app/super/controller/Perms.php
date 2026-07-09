<?php
//下级
namespace app\super\controller;

use app\common\basics\Auth;
use app\common\exception\RequestException;
use app\super\service\PermsService;
use app\super\service\RolesService;
use app\common\utils\AjaxUtils;
use app\super\validate\PermsValidate;
use think\App;
use think\response\Json;


class Perms extends Auth
{
    public function items():Json
    {
        $list = PermsService::getTreeData(0,[]);
        return AjaxUtils::success($list);
    }

    public function all():Json
    {
        $list = RolesService::all();
        return AjaxUtils::success($list);
    }

    public function add(){
        $data = $this->request->param();
        (new PermsValidate())->goCheck('add',$data);
        if(!empty($data['module']) && !empty($data['controller']) && !empty($data['action'])){
            if(!check_action_exists($data['module'],$data['controller'],$data['action'])){
                throw new RequestException('方法不存在');
            }
        }

        PermsService::add($data);
        return AjaxUtils::success();
    }

    public function edit(){
        $data = $this->request->param();
        (new PermsValidate())->goCheck('edit',$data);
        if(!empty($data['module']) && !empty($data['controller']) && !empty($data['action'])){
            if(!check_action_exists($data['module'],$data['controller'],$data['action'])){
                throw new RequestException('方法不存在');
            }
        }
        PermsService::edit($data);
        return AjaxUtils::success();
    }

    public function del(){
        $ids = $this->request->param('ids/a');
        (new PermsValidate())->idsCheck($ids);
        PermsService::del($ids);
        return AjaxUtils::success($ids);
    }
}
