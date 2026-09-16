<?php
//下级
namespace app\super\controller;

use app\common\basics\Auth;
use app\common\enums\StatusEnum;
use app\common\model\ConfigGroupModel;
use app\common\model\ConfigModel;
use app\common\utils\AjaxUtils;
use app\common\utils\UrlUtils;
use think\App;


class Config extends Auth
{
    public function items(){
        $configModel = new ConfigModel();
        $configGroupModel = new ConfigGroupModel();

        $items = [];
        $data = $configGroupModel
            ->where('status','=',StatusEnum::STATUS_1)
            ->order('sort desc')
            ->field('id,name')
            ->select();
        foreach($data as $item){
            $configItems = $configModel
                ->where('group_id','=',$item['id'])
                ->where('show','=',ConfigModel::SHOW_ON)
                ->order('sort desc')->select();

            $child = [];
            foreach($configItems as $configItem){
                if($configItem['type'] == 'select'){
                    $configItem['params'] = json_decode($configItem['params'],true);
                    $configItem['content'] = trim($configItem['content']);
                }
                if($configItem['type'] == 'image'){
                    $configItem['content'] = UrlUtils::toAbsoluteUrl($configItem['content']);
                }
                $child[]=$configItem;
            }
            $item['child'] = $child;

            $items[]=$item;
        }

        return AjaxUtils::success("获取成功",$items);
    }

    public function save(){
        $params = $this->request->param();
        $model = new ConfigModel();
        foreach($params as $item){
            if(isset($item['key']) && !empty($item['key'])){
                if($item['type'] == 'image'){
                    $item['content'] = UrlUtils::toRelativeUrl($item['content']);
                }
                $model->where('key','=',$item['key'])->update(array('content'=>$item['content']));
            }
        }
        return AjaxUtils::success("保存成功");
    }



}
