<?php
namespace app\adminapi\logic\setting;

use app\common\logic\BaseLogic;
use app\common\service\ConfigService;

/**
 * 系统配置逻辑
 */
class ConfigLogic extends BaseLogic
{
    /**
     * @notes 获取分销/抽成配置
     */
    public static function getCommissionConfig()
    {
        return [
            'platform_ratio' => ConfigService::get('commission', 'platform_ratio', 10), // 平台抽成%
        ];
    }

    /**
     * @notes 设置分销/抽成配置
     */
    public static function setCommissionConfig($params)
    {
        ConfigService::set('commission', 'platform_ratio', $params['platform_ratio']);
    }

    /**
     * @notes 获取邮箱配置
     */
    public static function getSmtpConfig()
    {
        return [
            'smtp_host' => ConfigService::get('smtp', 'smtp_host', ''),
            'smtp_port' => ConfigService::get('smtp', 'smtp_port', 465),
            'smtp_user' => ConfigService::get('smtp', 'smtp_user', ''),
            'smtp_pass' => ConfigService::get('smtp', 'smtp_pass', ''),
            'smtp_from' => ConfigService::get('smtp', 'smtp_from', ''),
        ];
    }

    /**
     * @notes 设置邮箱配置
     */
    public static function setSmtpConfig($params)
    {
        ConfigService::set('smtp', 'smtp_host', $params['smtp_host']);
        ConfigService::set('smtp', 'smtp_port', $params['smtp_port']);
        ConfigService::set('smtp', 'smtp_user', $params['smtp_user']);
        ConfigService::set('smtp', 'smtp_pass', $params['smtp_pass']);
        ConfigService::set('smtp', 'smtp_from', $params['smtp_from']);
    }
}
