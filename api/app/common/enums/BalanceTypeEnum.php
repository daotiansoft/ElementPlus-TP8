<?php

declare(strict_types=1);

namespace app\common\enums;

/**
 * 余额交易类型
 */
class BalanceTypeEnum
{
    const RECHARGE = 'recharge'; // 充值
    const DEDUCT   = 'deduct';   // 扣减
    const REFUND   = 'refund';   // 退款
    const ADJUST   = 'adjust';   // 调整
    const CONSUME  = 'consume';  // 消费

    /**
     * 类型 => 中文描述
     */
    public static function getMap(): array
    {
        return [
            self::RECHARGE => '充值',
            self::DEDUCT   => '扣减',
            self::REFUND   => '退款',
            self::ADJUST   => '调整',
            self::CONSUME  => '消费',
        ];
    }

    /**
     * 类型中文描述
     */
    public static function getName(string $type): string
    {
        return self::getMap()[$type] ?? $type;
    }
}
