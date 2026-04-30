<?php
namespace app\adminapi\logic\setting;

use app\common\logic\BaseLogic;
use think\facade\Db;

/**
 * 商户设置逻辑
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
class MerchantSettingLogic extends BaseLogic
{
    public static function getConfig()
    {
        $merchantConfig = Db::name('config')
            ->where('type', 'merchant')
            ->column('value', 'name');

        return [
            'open_audit' => isset($merchantConfig['open_audit']) ? intval($merchantConfig['open_audit']) : 0,
            'platform_ratio' => isset($merchantConfig['platform_ratio']) ? floatval($merchantConfig['platform_ratio']) : 10,
            'min_price' => isset($merchantConfig['min_price']) ? floatval($merchantConfig['min_price']) : 0,
            'max_price' => isset($merchantConfig['max_price']) ? floatval($merchantConfig['max_price']) : 10000,
            'allow_distribution' => isset($merchantConfig['allow_distribution']) ? intval($merchantConfig['allow_distribution']) : 1,
            'default_distribution_ratio' => isset($merchantConfig['default_distribution_ratio']) ? floatval($merchantConfig['default_distribution_ratio']) : 10,
            'min_distribution_ratio' => isset($merchantConfig['min_distribution_ratio']) ? floatval($merchantConfig['min_distribution_ratio']) : 0,
            'max_distribution_ratio' => isset($merchantConfig['max_distribution_ratio']) ? floatval($merchantConfig['max_distribution_ratio']) : 50,
        ];
    }

    public static function setConfig($params)
    {
        $minRatio = floatval($params['min_distribution_ratio'] ?? 0);
        $maxRatio = floatval($params['max_distribution_ratio'] ?? 50);
        
        if ($minRatio > $maxRatio) {
            self::$error = '最小分销比例不能大于最大分销比例';
            return false;
        }

        $merchantData = [
            ['type' => 'merchant', 'name' => 'open_audit', 'value' => $params['open_audit'] ?? 0],
            ['type' => 'merchant', 'name' => 'platform_ratio', 'value' => $params['platform_ratio'] ?? 10],
            ['type' => 'merchant', 'name' => 'min_price', 'value' => $params['min_price'] ?? 0],
            ['type' => 'merchant', 'name' => 'max_price', 'value' => $params['max_price'] ?? 10000],
            ['type' => 'merchant', 'name' => 'allow_distribution', 'value' => $params['allow_distribution'] ?? 1],
            ['type' => 'merchant', 'name' => 'default_distribution_ratio', 'value' => $params['default_distribution_ratio'] ?? 10],
            ['type' => 'merchant', 'name' => 'min_distribution_ratio', 'value' => $params['min_distribution_ratio'] ?? 0],
            ['type' => 'merchant', 'name' => 'max_distribution_ratio', 'value' => $params['max_distribution_ratio'] ?? 50],
        ];

        foreach ($merchantData as $item) {
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
}
