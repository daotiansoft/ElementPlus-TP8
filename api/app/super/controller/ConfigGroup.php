<?php
//下级
namespace app\super\controller;

use app\common\basics\Auth;
use app\super\service\ConfigGroupService;
use app\common\utils\AjaxUtils;
use app\super\validate\ConfigGroupValidate;
use think\App;
use think\response\Json;


class ConfigGroup extends Auth
{
    public function items():Json
    {
        $list = ConfigGroupService::lists($this->request->param());
        return AjaxUtils::success($list);
    }

    public function add(){
        $data = $this->request->param();

        (new ConfigGroupValidate())->addCheck($data);
        ConfigGroupService::add($data);
        return AjaxUtils::success();
    }

    public function edit(){
        $data = $this->request->param();
        (new ConfigGroupValidate())->editCheck($data);
        ConfigGroupService::edit($data);
        return AjaxUtils::success();
    }

    public function del(){
        $ids = $this->request->param('ids/a');
        (new ConfigGroupValidate())->idsCheck($ids);
        ConfigGroupService::del($ids);
        return AjaxUtils::success($ids);
    }
    public function getOptions(){
        return AjaxUtils::success(ConfigGroupService::getOptionsList());
    }
}
