<?php
namespace app\openapi\controller;

use app\common\basics\Api;
use app\common\utils\AjaxUtils;
use think\response\Json;

class Index extends Api
{
    public function ping(): Json
    {
        return AjaxUtils::success('连接正常');
    }

    public function index(){
        return 'element-ui-admin-tp6';
    }
}
