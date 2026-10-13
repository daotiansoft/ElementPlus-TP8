<?php

declare(strict_types=1);

namespace app\admin\controller;

use app\common\basics\BaseController;
use app\common\service\BalanceService;
use app\common\utils\AjaxUtils;
use think\App;
use think\response\Json;

/**
 * 余额管理（admin 模块，管理员）
 */
class Balance extends BaseController
{
    /**
     * 交易记录列表（所有人的记录）
     */
    public function items(): Json
    {
        $params = $this->request->param();
        $list = BalanceService::lists($params, $this->userItem);
        return AjaxUtils::success($list);
    }

    /**
     * 删除交易记录
     */
    public function del()
    {
        $ids = $this->request->param('ids/a');
        if (empty($ids)) {
            return AjaxUtils::error('请选择要删除的记录');
        }
        BalanceService::del($ids);
        return AjaxUtils::success('删除成功');
    }
}
