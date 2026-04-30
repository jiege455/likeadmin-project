import request from '@/utils/request'

// 商家申请列表
export function applyLists(params: any) {
    return request.get({ url: '/merchant.apply/lists', params })
}

// 审核商家申请
export function auditApply(params: any) {
    return request.post({ url: '/merchant.apply/audit', params })
}

// 商户列表
export function merchantLists(params: any) {
    return request.get({ url: '/merchant.merchant/lists', params })
}

// 商户详情
export function merchantDetail(params: any) {
    return request.get({ url: '/merchant.merchant/detail', params })
}

// 商户统计数据
export function merchantStatistics(params: any) {
    return request.get({ url: '/merchant.merchant/statistics', params })
}

// 商户文章列表
export function merchantArticles(params: any) {
    return request.get({ url: '/merchant.merchant/articles', params })
}

// 商户订单列表
export function merchantOrders(params: any) {
    return request.get({ url: '/merchant.merchant/orders', params })
}

// 审核商户
export function merchantAudit(params: any) {
    return request.post({ url: '/merchant.merchant/audit', params })
}

// 设置商户状态
export function merchantSetStatus(params: any) {
    return request.post({ url: '/merchant.merchant/setStatus', params })
}

// 编辑商户
export function merchantEdit(params: any) {
    return request.post({ url: '/merchant.merchant/edit', params })
}

// 修改商户状态
export function merchantStatus(params: any) {
    return request.post({ url: '/merchant.merchant/updateStatus', params })
}

// 删除商户
export function merchantDelete(params: any) {
    return request.post({ url: '/merchant.merchant/delete', params })
}
