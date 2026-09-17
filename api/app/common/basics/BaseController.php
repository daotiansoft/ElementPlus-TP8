<?php
namespace app\common\basics;

use app\common\model\CommonModel;
use app\common\utils\AjaxUtils;
use think\App;
use think\response\Json;


class BaseController extends Auth
{
    protected $model;
    protected $validate;
    protected $service;
    public function __construct(App $app)
    {
        parent::__construct($app);

        $this->model = new CommonModel();
        $this->validate = new Validate();
        $this->service = new Service();
    }
    public function items():Json
    {
        $list = $this->service::lists($this->request->param());
        return AjaxUtils::success($list);
    }

    public function add(){
        $data = $this->request->param();
        $this->validate->addCheck($data);
        $this->service::add($data);
        return AjaxUtils::success();
    }

    public function edit(){
        $data = $this->request->param();
        $this->validate->editCheck($data);
        $this->service::edit($data);
        return AjaxUtils::success();
    }

    public function del(){
        $ids = $this->request->param('ids/a');
        $this->validate->idsCheck($ids);
        $this->service::del($ids);
        return AjaxUtils::success($ids);
    }
    public function getOptions(){
        return AjaxUtils::success($this->service::getOptions());
    }

}
