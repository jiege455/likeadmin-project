<?php
namespace app\api\logic\merchant;

use app\common\logic\BaseLogic;
use think\facade\Db;

class DistributionLogic extends BaseLogic
{
    public static function getSetting($userId)
    {
        $merchant = Db::name('merchant')->where('user_id', $userId)->find();
        if (!$merchant) {
            return [
                'distribution_switch' => 1,
                'distribution_ratio' => 10.00,
                'min_ratio' => self::getMinDistributionRatio(),
                'max_ratio' => self::getMaxDistributionRatio(),
            ];
        }

        return [
            'distribution_switch' => intval($merchant['distribution_switch'] ?? 1),
            'distribution_ratio' => floatval($merchant['distribution_ratio'] ?? 10.00),
            'min_ratio' => self::getMinDistributionRatio(),
            'max_ratio' => self::getMaxDistributionRatio(),
        ];
    }

    public static function setSetting($userId, $params)
    {
        $merchant = Db::name('merchant')->where('user_id', $userId)->find();
        if (!$merchant) {
            self::$error = '您还不是商户';
            return false;
        }

        $switch = intval($params['distribution_switch'] ?? 1);
        $ratio = floatval($params['distribution_ratio'] ?? 10);

        $maxRatio = self::getMaxDistributionRatio();
        $minRatio = self::getMinDistributionRatio();

        if ($ratio < $minRatio || $ratio > $maxRatio) {
            self::$error = "分销比例需在{$minRatio}%-{$maxRatio}%之间";
            return false;
        }

        Db::name('merchant')->where('id', $merchant['id'])->update([
            'distribution_switch' => $switch,
            'distribution_ratio' => $ratio,
            'update_time' => time()
        ]);

        return true;
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
