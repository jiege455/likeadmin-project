import request from '@/utils/request'

// 提现申请列表
export function withdrawLists(params: any) {
    return request.get({ url: '/merchant.withdraw/lists', params })
}

// 提现详情
export function withdrawDetail(params: any) {
    return request.get({ url: '/merchant.withdraw/detail', params })
}

// 提现统计
export function withdrawStatistics(params?: any) {
    return request.get({ url: '/merchant.withdraw/statistics', params })
}

// 提现审核
export function withdrawAudit(params: any) {
    return request.post({ url: '/merchant.withdraw/audit', params })
}
