<?php
namespace app\common\logic;

use think\facade\Db;
use think\facade\Log;

class ProfitDistributionLogic
{
    public static function distribute($orderId)
    {
        // 使用行锁查询订单，防止并发
        $order = Db::name('article_order')->where('id', $orderId)->lock(true)->find();
        if (!$order || $order['pay_status'] != 1) {
            return false;
        }

        // 幂等性检查：如果已经分配过利润，直接返回成功
        if (!empty($order['platform_profit']) && bccomp(strval($order['platform_profit']), '0', 2) >= 0) {
            return true;
        }

        $orderAmount = strval($order['order_amount']);
        $merchantId = intval($order['merchant_id']);
        $distributorId = intval($order['distributor_id']);
        $userId = intval($order['user_id']);
        $articleId = intval($order['article_id']);

        $platformRatio = self::getPlatformRatio();
        $distributionRatio = 0;
        $distributionSwitch = false;

        if ($distributorId > 0 && $distributorId != $userId) {
            $article = Db::name('article')->where('id', $articleId)->find();
            if ($article) {
                if ($article['distribution_switch'] == 1) {
                    $distributionSwitch = true;
                    if ($article['commission_ratio'] > 0) {
                        $distributionRatio = floatval($article['commission_ratio']);
                    } else {
                        $merchant = Db::name('merchant')->where('id', $merchantId)->find();
                        if ($merchant && $merchant['distribution_switch'] == 1) {
                            $distributionRatio = floatval($merchant['distribution_ratio'] ?? 10);
                        }
                    }
                }
            } else {
                $merchant = Db::name('merchant')->where('id', $merchantId)->find();
                if ($merchant && $merchant['distribution_switch'] == 1) {
                    $distributionSwitch = true;
                    $distributionRatio = floatval($merchant['distribution_ratio'] ?? 10);
                }
            }

            $maxRatio = self::getMaxDistributionRatio();
            $minRatio = self::getMinDistributionRatio();
            if ($distributionRatio < $minRatio) $distributionRatio = $minRatio;
            if ($distributionRatio > $maxRatio) $distributionRatio = $maxRatio;
        }

        $platformProfit = bcmul($orderAmount, bcdiv(strval($platformRatio), '100', 4), 2);
        $distributionAmount = '0.00';
        
        if ($distributionSwitch && $distributionRatio > 0) {
            $distributionAmount = bcmul($orderAmount, bcdiv(strval($distributionRatio), '100', 4), 2);
        }

        $merchantProfit = bcsub(bcsub($orderAmount, $platformProfit, 2), $distributionAmount, 2);
        if (bccomp($merchantProfit, '0', 2) < 0) $merchantProfit = '0.00';

        Db::startTrans();
        try {
            Db::name('article_order')->where('id', $orderId)->update([
                'platform_ratio' => $platformRatio,
                'platform_profit' => $platformProfit,
                'distribution_ratio' => $distributionRatio,
                'distribution_amount' => $distributionAmount,
                'merchant_profit' => $merchantProfit,
                'update_time' => time()
            ]);

            if ($merchantId > 0 && bccomp($merchantProfit, '0', 2) > 0) {
                // 使用行锁更新商户余额
                Db::name('merchant')->where('id', $merchantId)->lock(true)->find();
                Db::name('merchant')->where('id', $merchantId)->inc('money', $merchantProfit)->inc('total_income', $merchantProfit)->update();
            }

            if ($distributionSwitch && bccomp($distributionAmount, '0', 2) > 0 && $distributorId != $userId) {
                // 使用行锁更新用户佣金
                Db::name('user')->where('id', $distributorId)->lock(true)->find();
                Db::name('user')->where('id', $distributorId)->inc('commission', $distributionAmount)->inc('total_commission', $distributionAmount)->update();

                Db::name('distribution_log')->insert([
                    'user_id' => $distributorId,
                    'source_user_id' => $userId,
                    'order_id' => $orderId,
                    'order_type' => 1,
                    'amount' => $distributionAmount,
                    'status' => 1,
                    'create_time' => time()
                ]);
            }

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            Log::error('ProfitDistribution Error: ' . $e->getMessage());
            return false;
        }
    }

    public static function getPlatformRatio()
    {
        $config = Db::name('config')
            ->where('type', 'merchant')
            ->where('name', 'platform_ratio')
            ->value('value');
        return floatval($config ?: 10);
    }

    public static function getMaxDistributionRatio()
    {
        $config = Db::name('config')
            ->where('type', 'merchant')
            ->where('name', 'max_distribution_ratio')
            ->value('value');
        return floatval($config ?: 50);
    }

    public static function getMinDistributionRatio()
    {
        $config = Db::name('config')
            ->where('type', 'merchant')
            ->where('name', 'min_distribution_ratio')
            ->value('value');
        return floatval($config ?: 0);
    }
}
