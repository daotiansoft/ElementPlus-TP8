<?php
//下级
namespace app\super\controller;

use app\common\basics\Auth;
use app\common\model\ConfigModel;
use app\common\utils\AjaxUtils;
use app\common\utils\UrlUtils;
use think\App;


class Config extends Auth
{
    public function items(){
        $model = new ConfigModel();
        $data = $model->where('show','=',$model::SHOW_ON)->order('sort')->select();
        $items = [];
        foreach($data as $item){
            if($item['type'] == 'select'){
                $item['params'] = json_decode($item['params'],true);
                $item['content'] = trim($item['content']);
            }
            if($item['type'] == 'image'){
                $item['content'] = UrlUtils::toAbsoluteUrl($item['content']);
            }
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
