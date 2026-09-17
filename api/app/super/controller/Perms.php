<?php
//下级
namespace app\super\controller;

use app\common\basics\BaseController;
use app\common\exception\RequestException;
use app\super\service\PermsService;
use app\common\utils\AjaxUtils;
use app\super\validate\PermsValidate;
use think\App;
use think\response\Json;


class Perms extends BaseController
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->service = new PermsService();
        $this->validate = new PermsValidate();
    }
    public function items():Json
    {
        $list = $this->service::getTreeData(0,[]);
        return AjaxUtils::success($list);
    }
    public function add(){
        $data = $this->request->param();
        $this->validate->goCheck('add',$data);
        if(!empty($data['module']) && !empty($data['controller']) && !empty($data['action'])){
            if(!check_action_exists($data['module'],$data['controller'],$data['action'])){
                throw new RequestException('方法不存在');
            }
        }

        $this->service::add($data);
        return AjaxUtils::success();
    }

    public function edit(){
        $data = $this->request->param();
        $this->validate->goCheck('edit',$data);
        if(!empty($data['module']) && !empty($data['controller']) && !empty($data['action'])){
            if(!check_action_exists($data['module'],$data['controller'],$data['action'])){
                throw new RequestException('方法不存在');
            }
        }
        $this->service::edit($data);
        return AjaxUtils::success();
    }
}
