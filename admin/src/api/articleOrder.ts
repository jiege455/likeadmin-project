import request from '@/utils/request'

// 文章订单列表
export function articleOrderLists(params: any) {
    return request.get({ url: '/article.articleOrder/lists', params })
}
