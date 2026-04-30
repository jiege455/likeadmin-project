<?php

namespace app\adminapi\logic\setting;

use app\common\logic\BaseLogic;
use app\common\service\ConfigService;
use app\common\service\EmailService;

class EmailLogic extends BaseLogic
{
    /**
     * 获取邮箱配置
     */
    public static function getConfig(): array
    {
        $config = ConfigService::get('email', 'email_config', [
            'smtp_server' => '',
            'port' => 465,
            'username' => '',
            'password' => '',
            'from_email' => '',
            'from_name' => '系统通知',
            'admin_email' => '',
            'encrypt' => 'ssl',
        ]);
        return $config;
    }

    /**
     * 保存邮箱配置
     */
    public static function setConfig(array $params): bool|string
    {
        try {
            $config = [
                'smtp_server' => $params['smtp_server'] ?? '',
                'port' => (int)($params['port'] ?? 465),
                'username' => $params['username'] ?? '',
                'password' => $params['password'] ?? '',
                'from_email' => $params['from_email'] ?? '',
                'from_name' => $params['from_name'] ?? '系统通知',
                'admin_email' => $params['admin_email'] ?? '',
                'encrypt' => $params['encrypt'] ?? 'ssl',
            ];
            ConfigService::set('email', 'email_config', $config);
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 发送测试邮件
     */
    public static function sendTest(array $params): bool|string
    {
        try {
            $emailNotifyOpen = ConfigService::get('system', 'email_notify_open', 0);
            if ($emailNotifyOpen != 1) {
                return '邮件通知功能未开启，请先开启邮件通知开关';
            }

            $email = $params['email'] ?? '';
            if (empty($email)) {
                return '请输入测试邮箱地址';
            }

            $config = self::getConfig();
            if (empty($config['smtp_server']) || empty($config['username'])) {
                return '请先配置 SMTP 服务器信息';
            }

            $subject = '测试邮件 - 邮箱配置验证';
            $body = self::getTestEmailTemplate($config);

            $emailService = new EmailService();
            $result = $emailService->send($email, $subject, $body);

            if ($result) {
                return true;
            }
            return $emailService->getError() ?: '发送失败';
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 获取测试邮件模板
     */
    private static function getTestEmailTemplate(array $config): string
    {
        $websiteConfig = ConfigService::get('website');
        $siteName = $websiteConfig['shop_name'] ?? $config['from_name'] ?? '系统通知';
        $siteUrl = $websiteConfig['shop_url'] ?? '';
        $year = date('Y');
        $datetime = date('Y-m-d H:i:s');
        
        $siteLink = '';
        if (!empty($siteUrl)) {
            $siteLink = "<a href=\"{$siteUrl}\" style=\"color: #667eea; text-decoration: none;\">{$siteUrl}</a>";
        }
        
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; background-color: #f5f5f5; font-family: 'Microsoft YaHei', Arial, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden;">
                    <tr>
                        <td style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px 40px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 600;">📧 邮箱配置测试</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 40px;">
                            <p style="margin: 0 0 20px; color: #333; font-size: 16px; line-height: 1.8;">您好！</p>
                            <p style="margin: 0 0 20px; color: #666; font-size: 14px; line-height: 1.8;">这是一封测试邮件，用于验证您的邮箱配置是否正确。如果您收到此邮件，说明邮箱服务配置成功！</p>
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8f9fa; border-radius: 8px; margin: 30px 0;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h3 style="margin: 0 0 15px; color: #333; font-size: 16px;">📋 配置信息</h3>
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding: 8px 0; color: #666; font-size: 14px; width: 100px;">发件人名称</td>
                                                <td style="padding: 8px 0; color: #333; font-size: 14px;">{$siteName}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #666; font-size: 14px; border-top: 1px solid #eee;">发送时间</td>
                                                <td style="padding: 8px 0; color: #333; font-size: 14px; border-top: 1px solid #eee;">{$datetime}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #666; font-size: 14px; border-top: 1px solid #eee;">测试状态</td>
                                                <td style="padding: 8px 0; color: #28a745; font-size: 14px; font-weight: 600; border-top: 1px solid #eee;">✅ 发送成功</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin: 20px 0 0; color: #999; font-size: 13px; line-height: 1.8;">此邮件由系统自动发送，请勿直接回复。</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 20px 40px; border-top: 1px solid #eee; text-align: center;">
                            <p style="margin: 0 0 10px; color: #999; font-size: 12px;">© {$year} {$siteName} 版权所有</p>
                            {$siteLink}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }

    /**
     * 获取开关配置
     */
    public static function getSwitchConfig(): array
    {
        $baseConfig = [
            'email_notify_open' => ConfigService::get('system', 'email_notify_open', '0'),
            'sms_notify_open' => ConfigService::get('system', 'sms_notify_open', '1'),
            'register_open' => ConfigService::get('system', 'register_open', '1'),
            'register_verify_type' => ConfigService::get('system', 'register_verify_type', 'email'),
            'merchant_apply_open' => ConfigService::get('system', 'merchant_apply_open', '1'),
            'merchant_apply_verify_type' => ConfigService::get('system', 'merchant_apply_verify_type', 'email'),
        ];
        
        return array_merge($baseConfig, self::getEmailSwitchConfig());
    }

    /**
     * 保存开关配置
     */
    public static function setSwitchConfig(array $params): bool|string
    {
        try {
            if (isset($params['email_notify_open'])) {
                ConfigService::set('system', 'email_notify_open', $params['email_notify_open']);
            }

            if (isset($params['sms_notify_open'])) {
                ConfigService::set('system', 'sms_notify_open', $params['sms_notify_open']);
            }

            if (isset($params['register_open'])) {
                ConfigService::set('system', 'register_open', $params['register_open']);
            }

            if (isset($params['register_verify_type'])) {
                ConfigService::set('system', 'register_verify_type', $params['register_verify_type']);
            }

            if (isset($params['merchant_apply_open'])) {
                ConfigService::set('system', 'merchant_apply_open', $params['merchant_apply_open']);
            }

            if (isset($params['merchant_apply_verify_type'])) {
                ConfigService::set('system', 'merchant_apply_verify_type', $params['merchant_apply_verify_type']);
            }

            $switchKeys = [
                'merchant_apply_admin_notify',
                'merchant_audit_notify',
                'order_notify',
                'withdraw_notify',
                'distribution_apply_notify',
                'distribution_audit_notify'
            ];

            foreach ($switchKeys as $key) {
                if (isset($params[$key])) {
                    ConfigService::set('email_switch', $key, $params[$key]);
                }
            }

            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public static function getEmailSwitchConfig(): array
    {
        return [
            'merchant_apply_admin_notify' => ConfigService::get('email_switch', 'merchant_apply_admin_notify', '1'),
            'merchant_audit_notify' => ConfigService::get('email_switch', 'merchant_audit_notify', '1'),
            'order_notify' => ConfigService::get('email_switch', 'order_notify', '1'),
            'withdraw_notify' => ConfigService::get('email_switch', 'withdraw_notify', '1'),
            'distribution_apply_notify' => ConfigService::get('email_switch', 'distribution_apply_notify', '1'),
            'distribution_audit_notify' => ConfigService::get('email_switch', 'distribution_audit_notify', '1'),
        ];
    }
}
