import { PayStatusEnum } from '@/enums/appEnums'

/**
 * 彩虹易支付
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 */
export class Rainbow {
    init(name: string, pay: any) {
        pay[name] = this
    }

    async run(options: any) {
        try {
            // 彩虹易支付返回的是支付链接，直接跳转
            if (typeof options === 'string' && options.startsWith('http')) {
                window.open(options, '_self')
                return PayStatusEnum.PENDING
            }

            // 如果返回的是包含 pay_url 的对象
            if (options && options.pay_url) {
                window.open(options.pay_url, '_self')
                return PayStatusEnum.PENDING
            }

            return PayStatusEnum.FAIL
        } catch (error) {
            return Promise.reject(error)
        }
    }
}
