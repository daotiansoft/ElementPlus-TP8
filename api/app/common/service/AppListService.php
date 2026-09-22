<?php

declare (strict_types = 1);

namespace app\common\service;


use app\common\basics\Service;
use app\common\enums\StatusEnum;
use app\common\exception\RequestException;
use app\common\model\AppListModel;

class AppListService extends Service
{
    public static function check($params = []){
        ksort($params);
        $appid = isset($params['appid']) ? $params['appid'] : '';
        $sign = isset($params['sign']) ? $params['sign'] : '';
        $time = isset($params['time']) ? intval($params['time']) : 0;

        if(empty($appid) || empty($sign) || $time < time() - 60){
            throw new RequestException('参数错误');
        }

        $appModel = new AppListModel();
        $appItem = $appModel->where('appid','=',$appid)->find();
        if(!$appItem || $appItem['status'] != StatusEnum::STATUS_1){
            throw new RequestException('appid不存在');
        }
		$appItem = $appItem->toArray();

        $sign_str = [];
        foreach($params as $key=>$v){
			if(empty($v)){
                continue;
            }
            if($key == 'sign'){
                continue;
            }
            $sign_str[] = $key . '=' .$v;
        }
        $sign_str = implode('&',$sign_str);

        if($sign != md5($sign_str . '&secret=' .$appItem['secret'])){
            throw new RequestException('sign错误',-1,['sign_str'=>$sign_str . '&secret=****']);
        }
        return $appItem;
    }

    /*
     * 签名
     */
    public static function order_sign($params = [],$appkey=''){
        $data = self::build_sign_param($params);
        return md5($data.'&secret='.$appkey);
    }
    /*
     * 拼接待签名参数
     */
    public static function build_sign_param($params){
        ksort($params);
        $data = '';
        foreach($params as $k=>$v){
            if($k != 'sign' && !empty($v)){
                if(empty($data)){
                    $data = $k.'='.$v;
                }else{
                    $data = $data.'&'.$k.'='.$v;
                }
            }
        }
        return $data;
    }
}