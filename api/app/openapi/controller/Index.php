<?php
namespace app\openapi\controller;

use app\BaseController;
use app\common\http\server\Websocket;
use app\common\service\PermsService;
use app\common\utils\AjaxUtils;
use think\App;


class Index extends BaseController
{

    public function index(){
        return 'element-ui-admin-tp6';
    }
}
