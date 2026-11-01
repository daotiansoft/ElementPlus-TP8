<?php
namespace app\openapi\controller;

use app\common\basics\Api;
use app\common\utils\AjaxUtils;
use think\response\Json;
use app\BaseController;

class Index extends BaseController
{
    public function ping(): Json
    {
        return AjaxUtils::success('连接正常');
    }

    public function index(){
        return 'ElementPlus-TP8';
    }
    public function test(){
        $params = $this->request->param();
        
    }
}
