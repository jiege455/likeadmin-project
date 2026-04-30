<?php

namespace app\common\logic;

use app\common\logic\BaseLogic;
use app\common\service\EmailService;
use app\common\service\ConfigService;
use think\facade\Db;
use app\common\enum\notice\EmailEnum;

class EmailNotifyLogic extends BaseLogic
{
    private static function checkSwitch(int $sceneId): bool
    {
        $emailNotifyOpen = ConfigService::get('system', 'email_notify_open', 0);
        if (!$emailNotifyOpen) {
            return false;
        }

        $switchKey = EmailEnum::getSwitchKey($sceneId);
        if (empty($switchKey)) {
            return true;
        }

        $switchValue = ConfigService::get('email_switch', $switchKey, 1);
        return $switchValue == 1;
    }

    private static function getEmailTemplate(string $title, string $contentBody): string
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
                        <td style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 25px 40px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 22px; font-weight: 600;">{$title}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 30px 40px;">
                            {$contentBody}
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

    public static function sendOrderNotify(int $merchantId, array $orderInfo): bool
    {
        if (!self::checkSwitch(EmailEnum::ORDER_NOTIFY)) {
            return false;
        }

        $merchant = Db::name('merchant')
            ->where('id', $merchantId)
            ->field('email, email_verify, email_notify')
            ->find();

        if (!$merchant || empty($merchant['email']) || !$merchant['email_verify'] || !$merchant['email_notify']) {
            return false;
        }

        $websiteConfig = ConfigService::get('website');
        $siteName = $websiteConfig['shop_name'] ?? '系统';
        
        $title = '【' . $siteName . '】您有新的订单';
        $articleTitle = $orderInfo['article_title'] ?? '未知文章';
        $orderSn = $orderInfo['order_sn'] ?? '';
        $payPrice = $orderInfo['pay_price'] ?? '0.00';
        $payTime = date('Y-m-d H:i:s', $orderInfo['create_time'] ?? time());
        
        $contentBody = <<<HTML
<p style="margin: 0 0 20px; color: #333; font-size: 16px;">您好，您的文章已被购买。</p>
<div style="background-color: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td style="padding: 10px 0; color: #666; font-size: 14px; width: 100px;">文章标题</td>
            <td style="padding: 10px 0; color: #333; font-size: 14px;">{$articleTitle}</td>
        </tr>
        <tr>
            <td style="padding: 10px 0; color: #666; font-size: 14px; border-top: 1px solid #eee;">订单编号</td>
            <td style="padding: 10px 0; color: #333; font-size: 14px; border-top: 1px solid #eee;">{$orderSn}</td>
        </tr>
        <tr>
            <td style="padding: 10px 0; color: #666; font-size: 14px; border-top: 1px solid #eee;">订单金额</td>
            <td style="padding: 10px 0; color: #e74c3c; font-size: 14px; font-weight: bold; border-top: 1px solid #eee;">￥{$payPrice}</td>
        </tr>
        <tr>
            <td style="padding: 10px 0; color: #666; font-size: 14px; border-top: 1px solid #eee;">购买时间</td>
            <td style="padding: 10px 0; color: #333; font-size: 14px; border-top: 1px solid #eee;">{$payTime}</td>
        </tr>
    </table>
</div>
<p style="margin: 20px 0 0; color: #999; font-size: 13px;">请登录商家后台查看详情。</p>
HTML;

        $content = self::getEmailTemplate('订单通知', $contentBody);

        try {
            $emailService = new EmailService();
            return $emailService->send($merchant['email'], $title, $content);
        } catch (\Exception $e) {
            \think\facade\Log::error('Order Email Notify Error: ' . $e->getMessage());
            return false;
        }
    }

    public static function sendWithdrawNotify(int $merchantId, array $withdrawInfo): bool
    {
        if (!self::checkSwitch(EmailEnum::WITHDRAW_NOTIFY)) {
            return false;
        }

        $merchant = Db::name('merchant')
            ->where('id', $merchantId)
            ->field('email, email_verify, email_notify')
            ->find();

        if (!$merchant || empty($merchant['email']) || !$merchant['email_verify'] || !$merchant['email_notify']) {
            return false;
        }

        $websiteConfig = ConfigService::get('website');
        $siteName = $websiteConfig['shop_name'] ?? '系统';
        
        $statusInt = $withdrawInfo['status'] ?? 1;
        if ($statusInt == 2 || $statusInt == 3) {
            $statusText = '<span style="color: #28a745;">已通过</span>';
        } else {
            $statusText = '<span style="color: #dc3545;">已拒绝</span>';
        }
        
        $title = '【' . $siteName . '】提现申请审核结果通知';
        $amount = $withdrawInfo['amount'] ?? '0.00';
        $processTime = date('Y-m-d H:i:s');
        
        $remarkHtml = '';
        if ($statusInt == 1 && !empty($withdrawInfo['audit_remark'])) {
            $remarkHtml = '<tr><td style="padding: 10px 0; color: #666; font-size: 14px; border-top: 1px solid #eee;">拒绝原因</td><td style="padding: 10px 0; color: #dc3545; font-size: 14px; border-top: 1px solid #eee;">' . htmlspecialchars($withdrawInfo['audit_remark']) . '</td></tr>';
        }
        
        $contentBody = <<<HTML
<p style="margin: 0 0 20px; color: #333; font-size: 16px;">您好，您的提现申请已处理。</p>
<div style="background-color: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td style="padding: 10px 0; color: #666; font-size: 14px; width: 100px;">提现金额</td>
            <td style="padding: 10px 0; color: #e74c3c; font-size: 14px; font-weight: bold;">￥{$amount}</td>
        </tr>
        <tr>
            <td style="padding: 10px 0; color: #666; font-size: 14px; border-top: 1px solid #eee;">审核状态</td>
            <td style="padding: 10px 0; font-size: 14px; border-top: 1px solid #eee;">{$statusText}</td>
        </tr>
        <tr>
            <td style="padding: 10px 0; color: #666; font-size: 14px; border-top: 1px solid #eee;">处理时间</td>
            <td style="padding: 10px 0; color: #333; font-size: 14px; border-top: 1px solid #eee;">{$processTime}</td>
        </tr>
        {$remarkHtml}
    </table>
</div>
<p style="margin: 20px 0 0; color: #999; font-size: 13px;">请登录商家后台查看详情。</p>
HTML;

        $content = self::getEmailTemplate('提现通知', $contentBody);

        try {
            $emailService = new EmailService();
            return $emailService->send($merchant['email'], $title, $content);
        } catch (\Exception $e) {
            \think\facade\Log::error('Withdraw Email Notify Error: ' . $e->getMessage());
            return false;
        }
    }

    public static function sendMerchantAuditNotify(int $applyId, int $status, string $remark = ''): bool
    {
        if (!self::checkSwitch(EmailEnum::MERCHANT_AUDIT)) {
            return false;
        }

        $apply = Db::name('merchant_apply')
            ->where('id', $applyId)
            ->field('user_id, email, name')
            ->find();

        if (!$apply || empty($apply['email'])) {
            return false;
        }

        $websiteConfig = ConfigService::get('website');
        $siteName = $websiteConfig['shop_name'] ?? '系统';
        
        $statusText = $status == 1 ? '<span style="color: #28a745;">已通过</span>' : '<span style="color: #dc3545;">已拒绝</span>';
        $title = '【' . $siteName . '】商家入驻审核结果通知';
        $shopName = $apply['name'];
        $processTime = date('Y-m-d H:i:s');
        
        $remarkHtml = '';
        if ($status == 2 && !empty($remark)) {
            $remarkHtml = '<tr><td style="padding: 10px 0; color: #666; font-size: 14px; border-top: 1px solid #eee;">拒绝原因</td><td style="padding: 10px 0; color: #dc3545; font-size: 14px; border-top: 1px solid #eee;">' . $remark . '</td></tr>';
        }
        
        $successTip = '';
        if ($status == 1) {
            $successTip = '<p style="margin: 20px 0 0; color: #28a745; font-size: 14px; font-weight: bold;">🎉 恭喜您成为我们的商家！请登录商家后台开始经营。</p>';
        }
        
        $contentBody = <<<HTML
<p style="margin: 0 0 20px; color: #333; font-size: 16px;">您好，您的商家入驻申请已审核完成。</p>
<div style="background-color: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td style="padding: 10px 0; color: #666; font-size: 14px; width: 100px;">店铺名称</td>
            <td style="padding: 10px 0; color: #333; font-size: 14px;">{$shopName}</td>
        </tr>
        <tr>
            <td style="padding: 10px 0; color: #666; font-size: 14px; border-top: 1px solid #eee;">审核状态</td>
            <td style="padding: 10px 0; font-size: 14px; border-top: 1px solid #eee;">{$statusText}</td>
        </tr>
        <tr>
            <td style="padding: 10px 0; color: #666; font-size: 14px; border-top: 1px solid #eee;">处理时间</td>
            <td style="padding: 10px 0; color: #333; font-size: 14px; border-top: 1px solid #eee;">{$processTime}</td>
        </tr>
        {$remarkHtml}
    </table>
</div>
{$successTip}
HTML;

        $content = self::getEmailTemplate('审核通知', $contentBody);

        try {
            $emailService = new EmailService();
            return $emailService->send($apply['email'], $title, $content);
        } catch (\Exception $e) {
            \think\facade\Log::error('Merchant Audit Email Notify Error: ' . $e->getMessage());
            return false;
        }
    }

    public static function sendToAdmin(string $title, string $content): bool
    {
        $emailNotifyOpen = ConfigService::get('system', 'email_notify_open', 0);
        if (!$emailNotifyOpen) {
            return false;
        }

        $emailConfig = ConfigService::get('email', 'email_config', []);
        $adminEmail = $emailConfig['admin_email'] ?? '';

        if (empty($adminEmail)) {
            return false;
        }

        $websiteConfig = ConfigService::get('website');
        $siteName = $websiteConfig['shop_name'] ?? '系统';
        
        $fullTitle = '【' . $siteName . '】' . $title;
        $htmlContent = self::getEmailTemplate('系统通知', '<p style="margin: 0; color: #333; font-size: 14px; line-height: 1.8;">' . nl2br($content) . '</p>');

        try {
            $emailService = new EmailService();
            return $emailService->send($adminEmail, $fullTitle, $htmlContent);
        } catch (\Exception $e) {
            \think\facade\Log::error('Admin Email Notify Error: ' . $e->getMessage());
            return false;
        }
    }

    public static function sendMerchantApplyNotifyToAdmin(array $applyInfo): bool
    {
        if (!self::checkSwitch(EmailEnum::MERCHANT_APPLY_ADMIN)) {
            return false;
        }

        $emailConfig = ConfigService::get('email', 'email_config', []);
        $adminEmail = $emailConfig['admin_email'] ?? '';

        if (empty($adminEmail)) {
            return false;
        }

        $websiteConfig = ConfigService::get('website');
        $siteName = $websiteConfig['shop_name'] ?? '系统';
        
        $title = '【' . $siteName . '】新的商家入驻申请';
        $shopName = $applyInfo['name'] ?? '未知';
        $mobile = $applyInfo['mobile'] ?? '未填写';
        $email = $applyInfo['email'] ?? '未填写';
        $wechat = $applyInfo['wechat'] ?? '未填写';
        $desc = $applyInfo['desc'] ?? '无';
        $applyTime = date('Y-m-d H:i:s', $applyInfo['create_time'] ?? time());
        
        $contentBody = <<<HTML
<p style="margin: 0 0 20px; color: #333; font-size: 16px;">您好，有新的商家入驻申请需要审核。</p>
<div style="background-color: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td style="padding: 10px 0; color: #666; font-size: 14px; width: 100px;">店铺名称</td>
            <td style="padding: 10px 0; color: #333; font-size: 14px; font-weight: bold;">{$shopName}</td>
        </tr>
        <tr>
            <td style="padding: 10px 0; color: #666; font-size: 14px; border-top: 1px solid #eee;">联系电话</td>
            <td style="padding: 10px 0; color: #333; font-size: 14px; border-top: 1px solid #eee;">{$mobile}</td>
        </tr>
        <tr>
            <td style="padding: 10px 0; color: #666; font-size: 14px; border-top: 1px solid #eee;">联系邮箱</td>
            <td style="padding: 10px 0; color: #333; font-size: 14px; border-top: 1px solid #eee;">{$email}</td>
        </tr>
        <tr>
            <td style="padding: 10px 0; color: #666; font-size: 14px; border-top: 1px solid #eee;">微信号</td>
            <td style="padding: 10px 0; color: #333; font-size: 14px; border-top: 1px solid #eee;">{$wechat}</td>
        </tr>
        <tr>
            <td style="padding: 10px 0; color: #666; font-size: 14px; border-top: 1px solid #eee;">申请说明</td>
            <td style="padding: 10px 0; color: #333; font-size: 14px; border-top: 1px solid #eee;">{$desc}</td>
        </tr>
        <tr>
            <td style="padding: 10px 0; color: #666; font-size: 14px; border-top: 1px solid #eee;">申请时间</td>
            <td style="padding: 10px 0; color: #333; font-size: 14px; border-top: 1px solid #eee;">{$applyTime}</td>
        </tr>
    </table>
</div>
<p style="margin: 20px 0 0; color: #e74c3c; font-size: 14px; font-weight: bold;">⚠️ 请及时登录后台审核处理！</p>
HTML;

        $content = self::getEmailTemplate('商家入驻申请', $contentBody);

        try {
            $emailService = new EmailService();
            return $emailService->send($adminEmail, $title, $content);
        } catch (\Exception $e) {
            \think\facade\Log::error('Merchant Apply Notify Admin Error: ' . $e->getMessage());
            return false;
        }
    }

    public static function sendDistributionApplyNotifyToAdmin(array $applyInfo): bool
    {
        if (!self::checkSwitch(EmailEnum::DISTRIBUTION_APPLY)) {
            return false;
        }

        $emailConfig = ConfigService::get('email', 'email_config', []);
        $adminEmail = $emailConfig['admin_email'] ?? '';

        if (empty($adminEmail)) {
            return false;
        }

        $websiteConfig = ConfigService::get('website');
        $siteName = $websiteConfig['shop_name'] ?? '系统';
        
        $title = '【' . $siteName . '】新的分销申请';
        $name = $applyInfo['name'] ?? '未知';
        $mobile = $applyInfo['mobile'] ?? '未填写';
        $reason = $applyInfo['reason'] ?? '无';
        $applyTime = date('Y-m-d H:i:s', $applyInfo['create_time'] ?? time());
        
        $userInfo = '';
        if (!empty($applyInfo['user_id'])) {
            $user = Db::name('user')->where('id', $applyInfo['user_id'])->field('nickname, sn')->find();
            if ($user) {
                $userInfo = '<tr><td style="padding: 10px 0; color: #666; font-size: 14px; border-top: 1px solid #eee;">用户昵称</td><td style="padding: 10px 0; color: #333; font-size: 14px; border-top: 1px solid #eee;">' . $user['nickname'] . '</td></tr>';
            }
        }
        
        $contentBody = <<<HTML
<p style="margin: 0 0 20px; color: #333; font-size: 16px;">您好，有新的用户申请成为推广员。</p>
<div style="background-color: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td style="padding: 10px 0; color: #666; font-size: 14px; width: 100px;">真实姓名</td>
            <td style="padding: 10px 0; color: #333; font-size: 14px; font-weight: bold;">{$name}</td>
        </tr>
        <tr>
            <td style="padding: 10px 0; color: #666; font-size: 14px; border-top: 1px solid #eee;">联系电话</td>
            <td style="padding: 10px 0; color: #333; font-size: 14px; border-top: 1px solid #eee;">{$mobile}</td>
        </tr>
        {$userInfo}
        <tr>
            <td style="padding: 10px 0; color: #666; font-size: 14px; border-top: 1px solid #eee;">申请理由</td>
            <td style="padding: 10px 0; color: #333; font-size: 14px; border-top: 1px solid #eee;">{$reason}</td>
        </tr>
        <tr>
            <td style="padding: 10px 0; color: #666; font-size: 14px; border-top: 1px solid #eee;">申请时间</td>
            <td style="padding: 10px 0; color: #333; font-size: 14px; border-top: 1px solid #eee;">{$applyTime}</td>
        </tr>
    </table>
</div>
<p style="margin: 20px 0 0; color: #e74c3c; font-size: 14px; font-weight: bold;">⚠️ 请及时登录后台审核处理！</p>
HTML;

        $content = self::getEmailTemplate('分销申请', $contentBody);

        try {
            $emailService = new EmailService();
            return $emailService->send($adminEmail, $title, $content);
        } catch (\Exception $e) {
            \think\facade\Log::error('Distribution Apply Notify Admin Error: ' . $e->getMessage());
            return false;
        }
    }

    public static function sendDistributionAuditNotify(int $applyId, int $status, string $remark = ''): bool
    {
        if (!self::checkSwitch(EmailEnum::DISTRIBUTION_AUDIT)) {
            return false;
        }

        $apply = Db::name('distribution_apply')
            ->where('id', $applyId)
            ->field('user_id, name')
            ->find();

        if (!$apply) {
            return false;
        }

        $user = Db::name('user')->where('id', $apply['user_id'])->field('email')->find();
        if (!$user || empty($user['email'])) {
            return false;
        }

        $websiteConfig = ConfigService::get('website');
        $siteName = $websiteConfig['shop_name'] ?? '系统';
        
        $statusText = $status == 1 ? '<span style="color: #28a745;">已通过</span>' : '<span style="color: #dc3545;">已拒绝</span>';
        $title = '【' . $siteName . '】推广员申请审核结果通知';
        $name = $apply['name'];
        $processTime = date('Y-m-d H:i:s');
        
        $remarkHtml = '';
        if ($status == 2 && !empty($remark)) {
            $remarkHtml = '<tr><td style="padding: 10px 0; color: #666; font-size: 14px; border-top: 1px solid #eee;">拒绝原因</td><td style="padding: 10px 0; color: #dc3545; font-size: 14px; border-top: 1px solid #eee;">' . $remark . '</td></tr>';
        }
        
        $successTip = '';
        if ($status == 1) {
            $successTip = '<p style="margin: 20px 0 0; color: #28a745; font-size: 14px; font-weight: bold;">🎉 恭喜您成为我们的推广员！请前往推广中心开始推广。</p>';
        }
        
        $contentBody = <<<HTML
<p style="margin: 0 0 20px; color: #333; font-size: 16px;">您好，您的推广员申请已审核完成。</p>
<div style="background-color: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td style="padding: 10px 0; color: #666; font-size: 14px; width: 100px;">申请人</td>
            <td style="padding: 10px 0; color: #333; font-size: 14px;">{$name}</td>
        </tr>
        <tr>
            <td style="padding: 10px 0; color: #666; font-size: 14px; border-top: 1px solid #eee;">审核状态</td>
            <td style="padding: 10px 0; font-size: 14px; border-top: 1px solid #eee;">{$statusText}</td>
        </tr>
        <tr>
            <td style="padding: 10px 0; color: #666; font-size: 14px; border-top: 1px solid #eee;">处理时间</td>
            <td style="padding: 10px 0; color: #333; font-size: 14px; border-top: 1px solid #eee;">{$processTime}</td>
        </tr>
        {$remarkHtml}
    </table>
</div>
{$successTip}
HTML;

        $content = self::getEmailTemplate('审核通知', $contentBody);

        try {
            $emailService = new EmailService();
            return $emailService->send($user['email'], $title, $content);
        } catch (\Exception $e) {
            \think\facade\Log::error('Distribution Audit Email Notify Error: ' . $e->getMessage());
            return false;
        }
    }
}
