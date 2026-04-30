<?php
namespace app\adminapi\logic\setting;

use app\common\logic\BaseLogic;
use think\facade\Db;

/**
 * 提现设置逻辑
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
class WithdrawSettingLogic extends BaseLogic
{
    public static function getConfig()
    {
        $config = Db::name('config')
            ->where('type', 'withdraw')
            ->column('value', 'name');

        $withdrawMethods = isset($config['withdraw_methods']) ? json_decode($config['withdraw_methods'], true) : [];
        if (empty($withdrawMethods)) {
            $withdrawMethods = [
                ['type' => 1, 'name' => '微信零钱', 'enabled' => 0],
                ['type' => 2, 'name' => '支付宝', 'enabled' => 1],
                ['type' => 3, 'name' => '银行卡', 'enabled' => 1],
            ];
        }

        return [
            'merchant_min_withdraw' => floatval($config['merchant_min_withdraw'] ?? 100),
            'merchant_withdraw_fee' => floatval($config['merchant_withdraw_fee'] ?? 0),
            'distributor_min_withdraw' => floatval($config['distributor_min_withdraw'] ?? 10),
            'distributor_withdraw_fee' => floatval($config['distributor_withdraw_fee'] ?? 0),
            'withdraw_arrival_days' => intval($config['withdraw_arrival_days'] ?? 3),
            'withdraw_notice' => $config['withdraw_notice'] ?? '提现申请提交后，将在1-3个工作日内审核，审核通过后打款到您的收款账户。',
            'max_distribution_ratio' => floatval($config['max_distribution_ratio'] ?? 50),
            'min_distribution_ratio' => floatval($config['min_distribution_ratio'] ?? 0),
            'withdraw_methods' => $withdrawMethods,
        ];
    }

    public static function setConfig($params)
    {
        $withdrawMethods = $params['withdraw_methods'] ?? null;
        if ($withdrawMethods !== null) {
            $withdrawMethods = is_string($withdrawMethods) ? $withdrawMethods : json_encode($withdrawMethods, JSON_UNESCAPED_UNICODE);
        } else {
            $withdrawMethods = json_encode([
                ['type' => 1, 'name' => '微信零钱', 'enabled' => 0],
                ['type' => 2, 'name' => '支付宝', 'enabled' => 1],
                ['type' => 3, 'name' => '银行卡', 'enabled' => 1],
            ], JSON_UNESCAPED_UNICODE);
        }

        $data = [
            ['type' => 'withdraw', 'name' => 'merchant_min_withdraw', 'value' => $params['merchant_min_withdraw'] ?? 100],
            ['type' => 'withdraw', 'name' => 'merchant_withdraw_fee', 'value' => $params['merchant_withdraw_fee'] ?? 0],
            ['type' => 'withdraw', 'name' => 'distributor_min_withdraw', 'value' => $params['distributor_min_withdraw'] ?? 10],
            ['type' => 'withdraw', 'name' => 'distributor_withdraw_fee', 'value' => $params['distributor_withdraw_fee'] ?? 0],
            ['type' => 'withdraw', 'name' => 'withdraw_arrival_days', 'value' => $params['withdraw_arrival_days'] ?? 3],
            ['type' => 'withdraw', 'name' => 'withdraw_notice', 'value' => $params['withdraw_notice'] ?? ''],
            ['type' => 'withdraw', 'name' => 'max_distribution_ratio', 'value' => $params['max_distribution_ratio'] ?? 50],
            ['type' => 'withdraw', 'name' => 'min_distribution_ratio', 'value' => $params['min_distribution_ratio'] ?? 0],
            ['type' => 'withdraw', 'name' => 'withdraw_methods', 'value' => $withdrawMethods],
        ];

        foreach ($data as $item) {
            $exists = Db::name('config')
                ->where('type', $item['type'])
                ->where('name', $item['name'])
                ->find();

            if ($exists) {
                Db::name('config')
                    ->where('type', $item['type'])
                    ->where('name', $item['name'])
                    ->update(['value' => $item['value']]);
            } else {
                Db::name('config')->insert($item);
            }
        }

        return true;
    }

    public static function getEnabledMethods()
    {
        $config = self::getConfig();
        $enabledMethods = [];
        foreach ($config['withdraw_methods'] as $method) {
            if ($method['enabled']) {
                $enabledMethods[] = $method['type'];
            }
        }
        return $enabledMethods;
    }
}
