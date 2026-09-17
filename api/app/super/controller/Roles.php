<?php
//下级
namespace app\super\controller;

use app\common\basics\Auth;
use app\common\basics\BaseController;
use app\super\service\RolesService;
use app\common\utils\AjaxUtils;
use app\super\validate\RolesValidate;
use think\App;
use think\response\Json;


class Roles extends BaseController
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->service = new RolesService();
        $this->validate = new RolesValidate();
    }

    public function getOptions():Json
    {
        $list = RolesService::getOptions();
        return AjaxUtils::success($list);
    }
}
