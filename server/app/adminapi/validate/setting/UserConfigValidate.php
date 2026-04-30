<?php
// +----------------------------------------------------------------------
// | likeadmin快速开发前后端分离管理后台（PHP版）
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | gitee下载：https://gitee.com/likeshop_gitee/likeadmin
// | github下载：https://github.com/likeshop-github/likeadmin
// | 访问官网：https://www.likeadmin.cn
// | likeadmin团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeadminTeam
// +----------------------------------------------------------------------
namespace app\adminapi\validate\setting;

use app\common\validate\BaseValidate;

/**
 * 用户设置验证
 * Class UserConfigValidate
 * @package app\adminapi\validate\setting
 */
class UserConfigValidate extends BaseValidate
{

    protected $rule = [
        'login_way' => 'array',
        'coerce_mobile' => 'in:0,1',
        'login_agreement' => 'in:0,1',
        'third_auth' => 'in:0,1',
        'wechat_auth' => 'in:0,1',
        'qq_auth' => 'in:0,1',
        'social_login' => 'in:0,1',
        'social_login_appid' => 'max:100',
        'social_login_appkey' => 'max:200',
        'social_login_qq_enable' => 'in:0,1',
        'social_login_wx_enable' => 'in:0,1',
        'social_login_alipay_enable' => 'in:0,1',
        'social_login_baidu_enable' => 'in:0,1',
        'social_login_microsoft_enable' => 'in:0,1',
        'default_avatar' => 'require',
        'register_open' => 'in:0,1',
        'register_verify_type' => 'in:mobile,email',
        'merchant_apply_open' => 'in:0,1',
        'merchant_apply_verify_type' => 'in:mobile,email',
        'distributor_apply_verify_type' => 'in:mobile,email',
        'withdraw_verify_type' => 'in:mobile,email',
        'email_notify_open' => 'in:0,1',
        'sms_notify_open' => 'in:0,1',
    ];


    protected $message = [
        'default_avatar.require' => '请上传用户默认头像',
        'login_way.requireIf' => '请选择登录方式',
        'login_way.array' => '登录方式值错误',
        'coerce_mobile.requireIf' => '请选择注册强制绑定手机',
        'coerce_mobile.in' => '注册强制绑定手机值错误',
        'wechat_auth.in' => '公众号微信授权登录值错误',
        'third_auth.in' => '第三方登录值错误',
        'login_agreement.in' => '政策协议值错误',
        'qq_auth.in' => 'QQ授权登录值错误',
        'social_login.in' => '聚合登录开关值错误',
        'social_login_appid.max' => '聚合登录AppId长度不能超过100',
        'social_login_appkey.max' => '聚合登录AppKey长度不能超过200',
        'social_login_qq_enable.in' => 'QQ登录开关值错误',
        'social_login_wx_enable.in' => '微信登录开关值错误',
        'social_login_alipay_enable.in' => '支付宝登录开关值错误',
        'social_login_baidu_enable.in' => '百度登录开关值错误',
        'social_login_microsoft_enable.in' => '微软登录开关值错误',
        'register_open.in' => '用户注册开关值错误',
        'register_verify_type.in' => '注册验证方式值错误',
        'merchant_apply_open.in' => '商家入驻开关值错误',
        'merchant_apply_verify_type.in' => '商家入驻验证方式值错误',
        'distributor_apply_verify_type.in' => '分销员申请验证方式值错误',
        'withdraw_verify_type.in' => '提现验证方式值错误',
        'email_notify_open.in' => '邮件通知开关值错误',
        'sms_notify_open.in' => '短信通知开关值错误',
    ];

    //用户设置验证
    public function sceneUser()
    {
        return $this->only(['default_avatar']);
    }

    //注册验证
    public function sceneRegister()
    {
        return $this->only([
            'login_way',
            'coerce_mobile',
            'login_agreement',
            'third_auth',
            'wechat_auth',
            'qq_auth',
            'social_login',
            'social_login_appid',
            'social_login_appkey',
            'social_login_qq_enable',
            'social_login_wx_enable',
            'social_login_alipay_enable',
            'social_login_baidu_enable',
            'social_login_microsoft_enable',
            'register_open',
            'register_verify_type',
            'merchant_apply_open',
            'merchant_apply_verify_type',
            'distributor_apply_verify_type',
            'withdraw_verify_type',
            'email_notify_open',
            'sms_notify_open',
        ]);
    }
}