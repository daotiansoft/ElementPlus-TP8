<?php
//下级
namespace app\super\controller;

use app\common\basics\BaseController;
use app\super\service\ConfigService;
use app\super\validate\ConfigValidate;
use think\App;


class ConfigList extends BaseController
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->service = new ConfigService();
        $this->validate = new ConfigValidate();
    }
}
