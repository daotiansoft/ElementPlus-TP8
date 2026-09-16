<?php
//下级
namespace app\super\controller;

use app\common\basics\Auth;
use app\common\utils\AjaxUtils;
use app\super\service\ConfigService;
use app\super\validate\ConfigValidate;
use think\App;
use think\response\Json;


class ConfigList extends Auth
{
    public function items():Json
    {
        $list = ConfigService::lists($this->request->param());
        return AjaxUtils::success($list);
    }

    public function add(){
        $data = $this->request->param();

        (new ConfigValidate())->addCheck($data);
        ConfigService::add($data);
        return AjaxUtils::success();
    }

    public function edit(){
        $data = $this->request->param();
        (new ConfigValidate())->editCheck($data);
        ConfigService::edit($data);
        return AjaxUtils::success();
    }

    public function del(){
        $ids = $this->request->param('ids/a');
        (new ConfigValidate())->idsCheck($ids);
        ConfigService::del($ids);
        return AjaxUtils::success($ids);
    }
}
