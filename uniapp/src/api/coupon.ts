import request from '@/utils/request'

/**
 * 获取商家优惠券列表
 * @param data
 * @returns
 */
export function getMerchantCouponList(data: {
    page_no: number
    page_size: number
    merchant_id: number
}) {
    return request.get({ url: '/coupon/merchantList', data })
}

/**
 * 领取优惠券
 * @param data
 * @returns
 */
export function receiveCoupon(data: { coupon_id: number }) {
    return request.post({ url: '/coupon/receive', data })
}

/**
 * 获取我的优惠券列表
 * @param data
 * @returns
 */
export function getMyCouponList(data: { page_no: number; page_size: number; status?: number }) {
    return request.get({ url: '/coupon/myList', data })
}

/**
 * 获取可用优惠券列表（下单时使用）
 * @param data
 * @returns
 */
export function getAvailableCouponList(data: {
    article_id?: number
    merchant_id?: number
    amount: number
}) {
    return request.get({ url: '/coupon/available', data })
}
