<?php
//下级
namespace app\super\controller;

use app\common\basics\BaseController;
use app\super\service\UserService;
use app\common\utils\AjaxUtils;
use app\super\validate\UserValidate;
use think\App;


class Member extends BaseController
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->service = new UserService();
        $this->validate = new UserValidate();
    }
    public function add(){
        $data = $this->request->param();
        if(isset($data['username']) && !empty($data['username'])){
            $userItem = $this->service::getItemByUsername($data['username']);
            if($userItem){
                return AjaxUtils::error("账号已存在");
            }
        }
        $this->validate->addCheck($data);

        $data['rand_code'] = get_rand_str(4);
        $data['password']=get_password($data['password'],$data['rand_code']);
        $this->service::add($data,$this->userItem);
        return AjaxUtils::success();
    }

    public function edit(){
        $data = $this->request->param();
        if(!empty($data['password'])){
            $data['rand_code'] = get_rand_str(4);
            $data['password']=get_password($data['password'],$data['rand_code']);
        }else{
            unset($data['password']);
        }
        $this->validate->goCheck('edit',$data);

        $this->service::edit($data,$this->userItem);
        return AjaxUtils::success();
    }
}
