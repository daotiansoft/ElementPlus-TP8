<?php
namespace app\super\controller;

use app\common\basics\BaseController;
use app\super\service\ConfigGroupService;
use app\super\validate\ConfigGroupValidate;
use think\App;


class ConfigGroup extends BaseController
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->service = new ConfigGroupService();
        $this->validate = new ConfigGroupValidate();
    }
}
