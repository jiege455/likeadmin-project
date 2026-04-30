import request from '@/utils/request'

// 资金流水
export function accountLog(params: any) {
    return request.get({ url: '/finance.account_log/lists', params })
}

// 资金变动类型
export function getUmChangeType() {
    return request.get({ url: '/finance.account_log/getUmChangeType' })
}

// 退款日志
export function refundLog(params: any) {
    return request.get({ url: '/finance.refund/log', params })
}

// 充值记录
export function rechargeLists(params: any) {
    return request.get({ url: '/recharge.recharge/lists', params })
}

// 充值退款
export function refund(params: any) {
    return request.post({ url: '/recharge.recharge/refund', params })
}

// 重新退款
export function refundAgain(params: any) {
    return request.post({ url: '/finance.refund/refundAgain', params })
}

// 退款记录
export function refundRecord(params: any) {
    return request.get({ url: '/finance.refund/record', params })
}

// 退款统计
export function refundStat() {
    return request.get({ url: '/finance.refund/stat' })
}

// 商户资金记录
export function merchantFinanceLists(params: any) {
    return request.get({ url: '/finance.merchant_finance/lists', params })
}
