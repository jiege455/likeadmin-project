import request from '@/utils/request'

// 提现列表
export function merchantWithdrawLists(params: any) {
    return request.get({ url: '/finance.merchant_withdraw/lists', params })
}

// 提现详情
export function merchantWithdrawDetail(params: any) {
    return request.get({ url: '/finance.merchant_withdraw/detail', params })
}

// 提现审核
export function merchantWithdrawAudit(params: any) {
    return request.post({ url: '/finance.merchant_withdraw/audit', params })
}

// 提现统计
export function merchantWithdrawStatistics(params?: any) {
    return request.get({ url: '/finance.merchant_withdraw/statistics', params })
}
