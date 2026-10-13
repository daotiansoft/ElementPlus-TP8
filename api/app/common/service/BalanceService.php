<?php

declare(strict_types=1);

namespace app\common\service;

use app\common\basics\Service;
use app\common\enums\BalanceTypeEnum;
use app\common\exception\RequestException;
use app\common\model\BalanceLogModel;
use app\common\model\UserModel;
use app\common\utils\TimeUtils;
use think\facade\Db;

/**
 * 余额服务
 */
class BalanceService extends Service
{
    /**
     * 获取用户余额（分）
     */
    public static function getBalance(int $userId): int
    {
        $user = UserModel::where('id', $userId)->field('balance')->find();
        return $user ? (int) $user['balance'] : 0;
    }

    /**
     * 变动余额（分），并写入变动记录
     *
     * @param int $userId 用户ID
     * @param int $changeAmount 变动金额（分，正为增加，负为扣减）
     * @param string $type 交易类型
     * @param string $remark 备注
     * @param string $operator 操作员
     * @return int 变动后余额（分）
     */
    public static function change(int $userId, int $changeAmount, string $type, string $remark = '', string $operator = ''): int
    {
        if ($changeAmount == 0) {
            throw new RequestException('变动金额不能为0');
        }

        return Db::transaction(function () use ($userId, $changeAmount, $type, $remark, $operator) {
            // 行锁读取当前余额，防止并发超扣
            $user = UserModel::where('id', $userId)->lock(true)->field('balance')->find();
            if (!$user) {
                throw new RequestException('用户不存在');
            }

            $before = (int) $user['balance'];
            $after = $before + $changeAmount;
            if ($after < 0) {
                throw new RequestException('余额不足，无法扣减');
            }

            UserModel::where('id', $userId)->update(['balance' => $after]);

            BalanceLogModel::create([
                'user_id' => $userId,
                'change_amount' => $changeAmount,
                'balance_after' => $after,
                'type' => $type,
                'remark' => $remark,
                'operator' => $operator,
                'create_time' => time(),
            ]);

            return $after;
        });
    }

    /**
     * 交易记录列表
     *
     * @param bool $ownOnly 仅查询当前用户自己的记录
     */
    public static function lists(array $get, array $userItem, bool $ownOnly = false): array
    {
        self::setSearch([
            '=' => ['user_id@user_id', 'type@type'],
            'datetime' => ['time@balance_log_model.create_time'],
        ], $get);

        $model = new BalanceLogModel();
        $query = $model->withJoin(['user'], 'LEFT')->where(self::$searchWhere);

        if ($ownOnly) {
            // 仅查看自己的记录（user_id 仅存在于 dt_balance_log，可直接使用）
            $query->where('user_id', (int) $userItem['id']);
        }

        $lists = $query->order('id desc')->paginate([
            'page' => $get['page'] ?? 1,
            'list_rows' => $get['pagesize'] ?? 20,
        ])->toArray();

        foreach ($lists['data'] as &$item) {
            $item['username'] = $item['user']['username'] ?? '';
            $item['type_name'] = BalanceTypeEnum::getName((string) $item['type']);
            $item['create_time'] = TimeUtils::formatTime((int) $item['create_time']);
            unset($item['user']);
        }
        unset($item);

        return ['count' => $lists['total'], 'items' => $lists['data']] ?? [];
    }

    /**
     * 删除交易记录
     *
     * @param array $ids 记录ID
     * @param array $userItem 当前账号（兼容基类签名，此处未使用）
     */
    public static function del(array $ids, array $userItem = []): void
    {
        BalanceLogModel::destroy($ids, true);
    }
}
