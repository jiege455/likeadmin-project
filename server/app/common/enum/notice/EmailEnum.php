<?php

namespace app\common\enum\notice;

class EmailEnum
{
    const USER_REGISTER = 201;
    const MERCHANT_APPLY = 202;
    const ORDER_NOTIFY = 203;
    const WITHDRAW_NOTIFY = 204;
    const MERCHANT_BIND_EMAIL = 205;
    const USER_BIND_EMAIL = 206;
    const MERCHANT_AUDIT = 207;
    const DISTRIBUTION_APPLY = 208;
    const DISTRIBUTION_AUDIT = 209;
    const MERCHANT_APPLY_ADMIN = 210;
    const DISTRIBUTOR_APPLY = 211;
    const WITHDRAW = 212;
    const FORGOT_PASSWORD = 213;

    const EMAIL_SCENE = [
        self::USER_REGISTER,
        self::MERCHANT_APPLY,
        self::MERCHANT_BIND_EMAIL,
        self::USER_BIND_EMAIL,
        self::DISTRIBUTOR_APPLY,
        self::WITHDRAW,
        self::FORGOT_PASSWORD,
    ];

    public static function getSceneDesc($sceneId, $flag = false)
    {
        $desc = [
            self::USER_REGISTER => '用户注册邮箱验证',
            self::MERCHANT_APPLY => '商家入驻邮箱验证',
            self::ORDER_NOTIFY => '商家订单通知',
            self::WITHDRAW_NOTIFY => '商家提现通知',
            self::MERCHANT_BIND_EMAIL => '商家绑定邮箱验证',
            self::USER_BIND_EMAIL => '用户绑定邮箱验证',
            self::MERCHANT_AUDIT => '商家入驻审核通知',
            self::DISTRIBUTION_APPLY => '分销申请通知管理员',
            self::DISTRIBUTION_AUDIT => '分销审核结果通知',
            self::MERCHANT_APPLY_ADMIN => '商家入驻申请通知管理员',
            self::DISTRIBUTOR_APPLY => '分销员申请邮箱验证',
            self::WITHDRAW => '提现邮箱验证',
            self::FORGOT_PASSWORD => '找回登录密码邮箱验证',
        ];

        if ($flag) {
            return $desc;
        }

        return $desc[$sceneId] ?? '';
    }

    public static function getSceneByTag($tag)
    {
        $scene = [
            'YHZC' => self::USER_REGISTER,
            'SJRZ' => self::MERCHANT_APPLY,
            'SJBDSJ' => self::MERCHANT_BIND_EMAIL,
            'YHBDSJ' => self::USER_BIND_EMAIL,
            'FXYSQ' => self::DISTRIBUTOR_APPLY,
            'TXSQ' => self::WITHDRAW,
            'ZHDLMM' => self::FORGOT_PASSWORD,
        ];
        return $scene[$tag] ?? '';
    }

    public static function getSwitchKey($sceneId): string
    {
        $switchMap = [
            self::ORDER_NOTIFY => 'order_notify',
            self::WITHDRAW_NOTIFY => 'withdraw_notify',
            self::MERCHANT_AUDIT => 'merchant_audit_notify',
            self::DISTRIBUTION_APPLY => 'distribution_apply_notify',
            self::DISTRIBUTION_AUDIT => 'distribution_audit_notify',
            self::MERCHANT_APPLY_ADMIN => 'merchant_apply_admin_notify',
        ];
        return $switchMap[$sceneId] ?? '';
    }
}
