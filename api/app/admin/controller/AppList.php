<?php
//下级
namespace app\admin\controller;

use app\common\basics\BaseController;
use app\admin\service\AppListService;
use app\admin\service\UserService;
use app\common\utils\AjaxUtils;
use app\admin\validate\AppListValidate;
use think\App;


class AppList extends BaseController
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->service = new AppListService();
        $this->validate = new AppListValidate();
    }

    public function add(){
        $data = $this->request->param();
        if(isset($data['username']) && !empty($data['username'])){
            $userItem = UserService::getItemByUsername($data['username']);
            if(!$userItem){
                return AjaxUtils::error("账号不存在");
            }
            $data['uid'] = $userItem['id'];
        }
        $this->validate->addCheck($data);
        $this->service::add($data,$this->userItem);
        return AjaxUtils::success();
    }

    public function edit(){
        $data = $this->request->param();
        if(isset($data['username']) && !empty($data['username'])){
            $userItem = UserService::getItemByUsername($data['username']);
            if(!$userItem){
                return AjaxUtils::error("账号不存在");
            }
            $data['uid'] = $userItem['id'];
        }
        $this->validate->editCheck($data);
        $this->service::edit($data,$this->userItem);
        return AjaxUtils::success();
    }
}
