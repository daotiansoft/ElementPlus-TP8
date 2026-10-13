<?php
namespace app\admin\controller;

use app\common\basics\BaseController;
use app\admin\service\ConfigGroupService;
use app\admin\validate\ConfigGroupValidate;
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
