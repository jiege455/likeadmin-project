<?php

namespace app\api\logic;

use app\common\logic\BaseLogic;
use app\common\model\notice\EmailLog;
use app\common\enum\notice\EmailEnum;
use app\common\service\EmailService;
use app\common\service\ConfigService;

class EmailLogic extends BaseLogic
{
    private static function getCodeEmailTemplate(string $sceneName, string $code): string
    {
        $websiteConfig = ConfigService::get('website');
        $siteName = $websiteConfig['shop_name'] ?? '系统通知';
        $siteUrl = $websiteConfig['shop_url'] ?? '';
        $year = date('Y');
        
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
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 600;">🔐 {$sceneName}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 40px;">
                            <p style="margin: 0 0 20px; color: #333; font-size: 16px;">您好！</p>
                            <p style="margin: 0 0 20px; color: #666; font-size: 14px; line-height: 1.8;">您正在进行{$sceneName}操作，验证码如下：</p>
                            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 25px; text-align: center; margin: 30px 0; border-radius: 8px;">
                                <span style="font-size: 36px; font-weight: bold; color: #ffffff; letter-spacing: 10px;">{$code}</span>
                            </div>
                            <p style="margin: 0 0 10px; color: #999; font-size: 14px;">验证码有效期5分钟，请勿泄露给他人。</p>
                            <p style="margin: 0; color: #999; font-size: 14px;">如非本人操作，请忽略此邮件。</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 20px 40px; border-top: 1px solid #eee; text-align: center;">
                            <p style="margin: 0 0 10px; color: #999; font-size: 12px;">
                                © {$year} {$siteName} 版权所有
                            </p>
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

    public static function sendCode(array $params): bool|string
    {
        try {
            $email = $params['email'] ?? '';
            $scene = $params['scene'] ?? '';

            if (empty($email)) {
                return '请输入邮箱地址';
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return '邮箱格式不正确';
            }

            $sceneId = EmailEnum::getSceneByTag($scene);
            if (empty($sceneId)) {
                return '场景值异常';
            }

            $emailNotifyOpen = ConfigService::get('system', 'email_notify_open', 0);
            if (!$emailNotifyOpen) {
                return '邮件通知功能未开启';
            }

            $switchKey = EmailEnum::getSwitchKey($sceneId);
            if (!empty($switchKey)) {
                $switchValue = ConfigService::get('email_switch', $switchKey, 1);
                if ($switchValue != 1) {
                    return '该场景的邮件通知已关闭';
                }
            }

            $lastLog = EmailLog::where('email', $email)
                ->where('scene_id', $sceneId)
                ->where('create_time', '>', time() - 60)
                ->findOrEmpty();

            if (!$lastLog->isEmpty()) {
                return '发送太频繁，请1分钟后再试';
            }

            $code = strval(mt_rand(1000, 9999));
            $sceneName = EmailEnum::getSceneDesc($sceneId);
            
            $websiteConfig = ConfigService::get('website');
            $siteName = $websiteConfig['shop_name'] ?? '系统';
            
            $title = "【{$siteName}】{$sceneName}验证码";
            $content = self::getCodeEmailTemplate($sceneName, $code);

            $emailService = new EmailService();
            $result = $emailService->send($email, $title, $content);

            if (!$result) {
                return $emailService->getError() ?: '发送失败';
            }

            $log = new EmailLog();
            $log->save([
                'scene_id' => $sceneId,
                'email' => $email,
                'title' => $title,
                'content' => $content,
                'code' => $code,
                'send_status' => 1,
                'send_time' => time(),
                'create_time' => time(),
                'update_time' => time(),
            ]);

            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public static function verify(array $params): bool|string
    {
        try {
            $email = $params['email'] ?? '';
            $code = $params['code'] ?? '';
            $scene = $params['scene'] ?? '';

            if (empty($email) || empty($code)) {
                return '请输入邮箱和验证码';
            }

            $sceneId = EmailEnum::getSceneByTag($scene);
            if (empty($sceneId)) {
                return '场景值异常';
            }

            $log = EmailLog::where('email', $email)
                ->where('scene_id', $sceneId)
                ->where('send_status', 1)
                ->where('is_verify', 0)
                ->where('send_time', '>', time() - 300)
                ->order('send_time', 'desc')
                ->findOrEmpty();

            if ($log->isEmpty()) {
                return '验证码已过期或不存在';
            }

            if ($log->code !== $code) {
                return '验证码错误';
            }

            $log->is_verify = 1;
            $log->save();

            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
