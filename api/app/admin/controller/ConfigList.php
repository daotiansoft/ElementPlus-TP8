<?php
//下级
namespace app\admin\controller;

use app\common\basics\BaseController;
use app\admin\service\ConfigService;
use app\admin\validate\ConfigValidate;
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
