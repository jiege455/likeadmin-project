import request from '@/utils/request'

// 商户文章列表
export function merchantArticleLists(params: any) {
    return request.get({ url: '/article.merchant_article/lists', params })
}

// 审核商户文章
export function auditArticle(params: any) {
    return request.post({ url: '/article.merchant_article/audit', params })
}

// 删除商户文章
export function deleteArticle(params: any) {
    return request.post({ url: '/article.merchant_article/delete', params })
}

// 商户文章详情
export function articleDetail(params: any) {
    return request.get({ url: '/article.merchant_article/detail', params })
}
