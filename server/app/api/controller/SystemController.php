<?php
/**
 * 系统配置控制器
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
namespace app\api\controller;

use app\api\controller\BaseApiController;
use app\common\service\ConfigService;
use think\response\Json;

class SystemController extends BaseApiController
{
    public array $notNeedLogin = ['switchConfig', 'articleTips'];

    public function switchConfig(): Json
    {
        $result = [
            'register_open' => (int)ConfigService::get('system', 'register_open', 1),
            'register_verify_type' => ConfigService::get('system', 'register_verify_type', 'email'),
            'merchant_apply_open' => (int)ConfigService::get('system', 'merchant_apply_open', 1),
            'merchant_apply_verify_type' => ConfigService::get('system', 'merchant_apply_verify_type', 'email'),
            'distributor_apply_verify_type' => ConfigService::get('system', 'distributor_apply_verify_type', 'email'),
            'withdraw_verify_type' => ConfigService::get('system', 'withdraw_verify_type', 'email'),
            'email_notify_open' => (int)ConfigService::get('system', 'email_notify_open', 0),
            'sms_notify_open' => (int)ConfigService::get('system', 'sms_notify_open', 1),
        ];
        return $this->data($result);
    }

    public function articleTips(): Json
    {
        $result = [
            'top_tips' => ConfigService::get('article_tips', 'top_tips', ''),
            'top_tips_show' => (int)ConfigService::get('article_tips', 'top_tips_show', 1),
            'bottom_tips' => ConfigService::get('article_tips', 'bottom_tips', ''),
            'bottom_tips_show' => (int)ConfigService::get('article_tips', 'bottom_tips_show', 1),
        ];
        return $this->data($result);
    }
}
