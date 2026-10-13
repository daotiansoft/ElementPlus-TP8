<?php
//下级
namespace app\admin\controller;

use app\common\basics\BaseController;
use app\common\enums\StatusEnum;
use app\common\exception\RequestException;
use app\common\model\PermsModel;
use app\admin\service\PermsService;
use app\common\utils\AjaxUtils;
use app\admin\validate\PermsValidate;
use think\App;
use think\response\Json;


class Perms extends BaseController
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->service = new PermsService();
        $this->validate = new PermsValidate();
        $this->model = new PermsModel();
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
                return AjaxUtils::error('方法不存在');
            }
        }

        $this->service::add($data,$this->userItem);
        return AjaxUtils::success();
    }

    public function edit(){
        $data = $this->request->param();
        $this->validate->goCheck('edit',$data);
        if(!empty($data['module']) && !empty($data['controller']) && !empty($data['action'])){
            if(!check_action_exists($data['module'],$data['controller'],$data['action'])){
                return AjaxUtils::error('方法不存在');
            }
        }
        $this->service::edit($data,$this->userItem);
        return AjaxUtils::success();
    }

    public function createCurd(){
        $id = $this->request->param('id',0,'intval');
        if($id <= 0){
            return AjaxUtils::error('参数错误');
        }
        $item = $this->model->getItemById($id);
        if(!$item){
            return AjaxUtils::error('节点不存在');
        }
        if(empty($item['module']) || empty($item['controller']) || !empty($item['action'])){
            return AjaxUtils::error('必须满足：所属模块、控制器不为空且操作方法为空');
        }

        $msg_list = [];
        $fields = [
            ['name'=>'列表','action'=>'items'],
            ['name'=>'新增','action'=>'add'],
            ['name'=>'编辑','action'=>'edit'],
            ['name'=>'删除','action'=>'del'],
        ];
        foreach($fields as $field){
            $data = [
                'parent_id'=>$id,
                'module'=>$item['module'],
                'controller'=>$item['controller'],
                'status'=>StatusEnum::STATUS_1,
                'route_status'=>StatusEnum::STATUS_0,
                'create_time'=>time(),
                'update_time'=>time(),
            ];
            $data = array_merge($data,$field);

            $res = $this->model
                ->where('parent_id','=',$data['parent_id'])
                ->where('module','=',$data['module'])
                ->where('controller','=',$data['controller'])
                ->where('action','=',$data['action'])
                ->field('id')
                ->find();
            if(!$res){
                $this->model->insert($data);
                $msg_list[]=$field['name'].'添加成功';
            }else{
                $msg_list[]=$field['name'].'已存在';
            }
        }
        return AjaxUtils::success($msg_list);
    }
}
