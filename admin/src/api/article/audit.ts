import request from '@/utils/request'

// 审核文章列表
export function auditLists(params: any) {
    return request.get({ url: '/article.audit/lists', params })
}

// 文章详情
export function auditDetail(params: any) {
    return request.get({ url: '/article.audit/detail', params })
}

// 审核统计
export function auditStatistics(params?: any) {
    return request.get({ url: '/article.audit/statistics', params })
}

// 审核文章
export function auditArticle(params: any) {
    return request.post({ url: '/article.audit/audit', params })
}

// 批量审核
export function batchAuditArticle(params: any) {
    return request.post({ url: '/article.audit/batchAudit', params })
}
