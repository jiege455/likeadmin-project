import request from '@/utils/request'

// 订单列表
export function orderLists(params: any) {
    return request.get({ url: '/article.articleOrder/lists', params })
}

// 订单详情
export function orderDetail(params: any) {
    return request.get({ url: '/article.articleOrder/detail', params })
}

// 订单统计
export function orderStatistics(params?: any) {
    return request.get({ url: '/article.articleOrder/statistics', params })
}

// 订单退款
export function orderRefund(params: any) {
    return request.post({ url: '/article.articleOrder/refund', params })
}
