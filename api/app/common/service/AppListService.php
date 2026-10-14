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
        $appid = $params['appid'] ?? '';
        $sign = $params['sign'] ?? '';
        $time = intval($params['time'] ?? 0);

        if(empty($appid) || empty($sign) || $time < time() - 60){
            throw new RequestException('参数错误');
        }

        $appModel = new AppListModel();
        $appItem = $appModel->where('appid','=',$appid)->find();
        if(!$appItem || $appItem['status'] != StatusEnum::STATUS_1){
            throw new RequestException('appid不存在');
        }
		$appItem = $appItem->toArray();
        if($sign != self::order_sign($params,$appItem['secret'])){
            throw new RequestException('sign错误',-1,['sign_str'=>self::build_sign_param($params) . '&secret=****']);
        }
        return $appItem;
    }

    /*
     * 签名
     */
    public static function order_sign(array $params,string $secret){
        $data = self::build_sign_param($params);
        return md5($data.'&secret='.$secret);
    }
    /*
     * 拼接待签名参数
     */
    public static function build_sign_param($params){
        ksort($params);
        unset($params['sign']);

        $parts = [];
        foreach ($params as $k => $v) {
            $v = ''.$v;
            if (!empty($v)) {
                $parts[] = $k . '=' . $v;
            }
        }

        return implode('&', $parts);
    }
}