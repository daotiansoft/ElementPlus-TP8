<?php

declare(strict_types=1);

namespace app\user\controller;

use app\common\basics\Auth;
use app\common\service\BalanceService;
use app\common\utils\AjaxUtils;

/**
 * 余额（user 模块，用户查看自己的记录）
 */
class Balance extends Auth
{
    /**
     * 当前用户的余额（分）
     */
    public function info()
    {
        $balance = BalanceService::getBalance((int) $this->userItem['id']);
        return AjaxUtils::success('获取成功', ['balance' => $balance]);
    }

    /**
     * 当前用户的交易记录
     */
    public function items()
    {
        $params = $this->request->param();
        unset($params['user_id']); // 用户只能查看自己的记录
        $list = BalanceService::lists($params, $this->userItem, true);
        return AjaxUtils::success($list);
    }
}
