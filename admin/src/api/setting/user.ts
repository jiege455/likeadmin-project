/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 */
import request from '@/utils/request'

/**
 * @return { Promise }
 * @description 获取用户设置
 */
export function getUserSetup() {
    return request.get({ url: '/setting.user.user/getConfig' })
}

/**
 * @return { Promise }
 * @param { string } default_avatar 默认用户头像
 * @description 设置用户设置
 */
export function setUserSetup(params: { default_avatar: string }) {
    return request.post({ url: '/setting.user.user/setConfig', params })
}

/**
 * @return { Promise }
 * @description 设置登录注册规则
 */
export function getLogin() {
    return request.get({ url: '/setting.user.user/getRegisterConfig' })
}

export interface LoginSetup {
    login_way: number[] | any // 登录方式, 逗号隔开
    coerce_mobile: number // 强制绑定手机 0/1
    login_agreement: number // 是否开启协议 0/1
    third_auth: number // 第三方登录 0/1
    wechat_auth: number // 微信授权登录 0-关闭 1-开启
    qq_auth: number // qq授权登录 0-关闭 1-开启
    social_login: number // 聚合登录开关 0-关闭 1-开启
    social_login_appid: string // 聚合登录AppId
    social_login_appkey: string // 聚合登录AppKey
    social_login_qq_enable: number // QQ登录开关 0-关闭 1-开启
    social_login_wx_enable: number // 微信登录开关 0-关闭 1-开启
    social_login_alipay_enable: number // 支付宝登录开关 0-关闭 1-开启
    social_login_baidu_enable: number // 百度登录开关 0-关闭 1-开启
    social_login_microsoft_enable: number // 微软登录开关 0-关闭 1-开启
    register_open: number // 用户注册开关 0-关闭 1-开启
    register_verify_type: string // 注册验证方式
    merchant_apply_open: number // 商家入驻开关 0-关闭 1-开启
    merchant_apply_verify_type: string // 商家入驻验证方式
    email_notify_open: number // 邮件通知总开关 0-关闭 1-开启
    sms_notify_open: number // 短信通知总开关 0-关闭 1-开启
}
/**
 * @return { Promise }
 * @param { LoginSetup } LoginSetup
 * @description 设置登录注册规则
 */
export function setLogin(params: LoginSetup) {
    return request.post({ url: '/setting.user.user/setRegisterConfig', params })
}
