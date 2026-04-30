import { Pay } from './pay'
import { Alipay } from './alipay'
import { Wechat } from './wechat'
import { Balance } from './balance'
import { Rainbow } from './rainbow'

/**
 * 支付方式枚举
 * 与后端 PayEnum 保持一致
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 */
enum PayWayEnum {
    BALANCE = 1,  // 余额支付
    WECHAT = 2,   // 微信支付
    ALIPAY = 3,   // 支付宝支付
    RAINBOW = 4   // 彩虹易支付
}

// 支付状态
enum PayStatusEnum {
    SUCCESS = 1,
    FAIL = 0
}

// 注入余额支付
const balance = new Balance()
Pay.inject(PayWayEnum[PayWayEnum.BALANCE], balance)

// 注入微信支付
const wechat = new Wechat()
Pay.inject(PayWayEnum[PayWayEnum.WECHAT], wechat)

// 注入支付宝支付
const alipay = new Alipay()
Pay.inject(PayWayEnum[PayWayEnum.ALIPAY], alipay)

// 注入彩虹易支付
const rainbow = new Rainbow()
Pay.inject(PayWayEnum[PayWayEnum.RAINBOW], rainbow)

const pay = new Pay()
export { pay, PayWayEnum, PayStatusEnum }
