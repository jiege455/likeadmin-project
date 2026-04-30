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
namespace app\adminapi\logic\setting\user;

use app\common\service\{ConfigService, FileService};

/**
 * 设置-用户设置逻辑层
 * Class UserLogic
 * @package app\adminapi\logic\config
 */
class UserLogic
{

    /**
     * @notes 获取用户设置
     * @return array
     * @author 段誉
     * @date 2022/3/29 10:09
     */
    public static function getConfig(): array
    {
        $defaultAvatar = config('project.default_image.user_avatar');
        $config = [
            //默认头像
            'default_avatar' => FileService::getFileUrl(ConfigService::get('default_image', 'user_avatar', $defaultAvatar)),
        ];
        return $config;
    }


    /**
     * @notes 设置用户设置
     * @param array $params
     * @return bool
     * @author 段誉
     * @date 2022/3/29 10:09
     */
    public function setConfig(array $params): bool
    {
        $avatar = FileService::setFileUrl($params['default_avatar']);
        ConfigService::set('default_image', 'user_avatar', $avatar);
        return true;
    }


    /**
     * @notes 获取注册配置
     * @return array
     * @author 段誉
     * @date 2022/3/29 10:10
     */
    public function getRegisterConfig(): array
    {
        $config = [
            // 登录方式
            'login_way' => ConfigService::get('login', 'login_way', config('project.login.login_way')),
            // 注册强制绑定手机
            'coerce_mobile' => ConfigService::get('login', 'coerce_mobile', config('project.login.coerce_mobile')),
            // 政策协议
            'login_agreement' => ConfigService::get('login', 'login_agreement', config('project.login.login_agreement')),
            // 第三方登录 开关
            'third_auth' => ConfigService::get('login', 'third_auth', config('project.login.third_auth')),
            // 微信授权登录
            'wechat_auth' => ConfigService::get('login', 'wechat_auth', config('project.login.wechat_auth')),
            // qq授权登录
            'qq_auth' => ConfigService::get('login', 'qq_auth', config('project.login.qq_auth')),
            // 聚合登录开关
            'social_login' => ConfigService::get('login', 'social_login', 0),
            // 聚合登录AppId
            'social_login_appid' => ConfigService::get('social_login', 'appid', ''),
            // 聚合登录AppKey
            'social_login_appkey' => ConfigService::get('social_login', 'appkey', ''),
            // QQ登录开关
            'social_login_qq_enable' => ConfigService::get('social_login', 'qq_enable', 0),
            // 微信登录开关
            'social_login_wx_enable' => ConfigService::get('social_login', 'wx_enable', 0),
            // 支付宝登录开关
            'social_login_alipay_enable' => ConfigService::get('social_login', 'alipay_enable', 0),
            // 百度登录开关
            'social_login_baidu_enable' => ConfigService::get('social_login', 'baidu_enable', 0),
            // 微软登录开关
            'social_login_microsoft_enable' => ConfigService::get('social_login', 'microsoft_enable', 0),
            // 用户注册开关
            'register_open' => ConfigService::get('system', 'register_open', 1),
            // 注册验证方式（默认邮箱验证）
            'register_verify_type' => ConfigService::get('system', 'register_verify_type', 'email'),
            // 商家入驻开关
            'merchant_apply_open' => ConfigService::get('system', 'merchant_apply_open', 1),
            // 商家入驻验证方式（默认邮箱验证）
            'merchant_apply_verify_type' => ConfigService::get('system', 'merchant_apply_verify_type', 'email'),
            // 分销员申请验证方式（默认邮箱验证）
            'distributor_apply_verify_type' => ConfigService::get('system', 'distributor_apply_verify_type', 'email'),
            // 提现验证方式（默认邮箱验证）
            'withdraw_verify_type' => ConfigService::get('system', 'withdraw_verify_type', 'email'),
            // 邮件通知总开关
            'email_notify_open' => ConfigService::get('system', 'email_notify_open', 0),
            // 短信通知总开关
            'sms_notify_open' => ConfigService::get('system', 'sms_notify_open', 1),
        ];
        return $config;
    }


    /**
     * @notes 设置登录注册
     * @param array $params
     * @return bool
     * @author 段誉
     * @date 2022/3/29 10:10
     */
    public static function setRegisterConfig(array $params): bool
    {
        // 登录方式：1-账号密码登录；2-手机短信验证码登录
        if (isset($params['login_way'])) {
            ConfigService::set('login', 'login_way', $params['login_way']);
        }
        // 注册强制绑定手机
        if (isset($params['coerce_mobile'])) {
            ConfigService::set('login', 'coerce_mobile', $params['coerce_mobile']);
        }
        // 政策协议
        if (isset($params['login_agreement'])) {
            ConfigService::set('login', 'login_agreement', $params['login_agreement']);
        }
        // 第三方授权登录
        if (isset($params['third_auth'])) {
            ConfigService::set('login', 'third_auth', $params['third_auth']);
        }
        // 微信授权登录
        if (isset($params['wechat_auth'])) {
            ConfigService::set('login', 'wechat_auth', $params['wechat_auth']);
        }
        // qq登录
        if (isset($params['qq_auth'])) {
            ConfigService::set('login', 'qq_auth', $params['qq_auth']);
        }
        // 聚合登录开关
        if (isset($params['social_login'])) {
            ConfigService::set('login', 'social_login', $params['social_login']);
        }
        // 聚合登录AppId
        if (isset($params['social_login_appid'])) {
            ConfigService::set('social_login', 'appid', $params['social_login_appid']);
        }
        // 聚合登录AppKey
        if (isset($params['social_login_appkey'])) {
            ConfigService::set('social_login', 'appkey', $params['social_login_appkey']);
        }
        // QQ登录开关
        if (isset($params['social_login_qq_enable'])) {
            ConfigService::set('social_login', 'qq_enable', $params['social_login_qq_enable']);
        }
        // 微信登录开关
        if (isset($params['social_login_wx_enable'])) {
            ConfigService::set('social_login', 'wx_enable', $params['social_login_wx_enable']);
        }
        // 支付宝登录开关
        if (isset($params['social_login_alipay_enable'])) {
            ConfigService::set('social_login', 'alipay_enable', $params['social_login_alipay_enable']);
        }
        // 百度登录开关
        if (isset($params['social_login_baidu_enable'])) {
            ConfigService::set('social_login', 'baidu_enable', $params['social_login_baidu_enable']);
        }
        // 微软登录开关
        if (isset($params['social_login_microsoft_enable'])) {
            ConfigService::set('social_login', 'microsoft_enable', $params['social_login_microsoft_enable']);
        }
        
        // 用户注册开关
        if (isset($params['register_open'])) {
            ConfigService::set('system', 'register_open', $params['register_open']);
        }
        // 注册验证方式
        if (isset($params['register_verify_type'])) {
            ConfigService::set('system', 'register_verify_type', $params['register_verify_type']);
        }
        // 商家入驻开关
        if (isset($params['merchant_apply_open'])) {
            ConfigService::set('system', 'merchant_apply_open', $params['merchant_apply_open']);
        }
        // 商家入驻验证方式
        if (isset($params['merchant_apply_verify_type'])) {
            ConfigService::set('system', 'merchant_apply_verify_type', $params['merchant_apply_verify_type']);
        }
        // 分销员申请验证方式
        if (isset($params['distributor_apply_verify_type'])) {
            ConfigService::set('system', 'distributor_apply_verify_type', $params['distributor_apply_verify_type']);
        }
        // 提现验证方式
        if (isset($params['withdraw_verify_type'])) {
            ConfigService::set('system', 'withdraw_verify_type', $params['withdraw_verify_type']);
        }
        // 邮件通知总开关
        if (isset($params['email_notify_open'])) {
            ConfigService::set('system', 'email_notify_open', $params['email_notify_open']);
        }
        // 短信通知总开关
        if (isset($params['sms_notify_open'])) {
            ConfigService::set('system', 'sms_notify_open', $params['sms_notify_open']);
        }
        
        return true;
    }

}