import request from '@/utils/request'

/**
 * @description 获取文章提示配置
 * @return { Promise }
 */
export function getArticleTips() {
    return request.get({ url: '/system/articleTips' })
}

/**
 * @description 获取文章分类
 * @return { Promise }
 */
export function getArticleCate() {
    return request.get({ url: '/article/cate' })
}

/**
 * @description 获取文章列表
 * @return { Promise }
 */
export function getArticleList(data: Record<string, any>) {
    return request.get({ url: '/article/lists', data: data })
}

/**
 * @description 获取文章详情
 * @param { number } id
 * @return { Promise }
 */
export function getArticleDetail(data: { id: number }) {
    return request.get({ url: '/article/detail', data: data })
}

/**
 * @description 加入收藏
 * @param { number } id
 * @return { Promise }
 */
export function addCollect(data: { id: number }) {
    return request.post({ url: '/article/addCollect', data: data }, { isAuth: true })
}

/**
 * @description 取消收藏
 * @param { number } id
 * @return { Promise }
 */
export function cancelCollect(data: { id: number }) {
    return request.post({ url: '/article/cancelCollect', data: data }, { isAuth: true })
}

/**
 * @description 获取收藏列表
 * @return { Promise }
 */
export function getCollect() {
    return request.get({ url: '/article/collect' })
}

/**
 * @description 获取系统通知列表
 * @return { Promise }
 */
export function getNoticeList(data: Record<string, any>) {
    return request.get({ url: '/notice/lists', data: data })
}

/**
 * @description 购买文章
 * @param { number } id
 * @return { Promise }
 */
export function buyArticle(data: { id: number }) {
    return request.post({ url: '/article/buy', data: data }, { isAuth: true })
}

/**
 * @description 获取所有标签
 * @return { Promise }
 */
export function getArticleTags() {
    return request.get({ url: '/articleTag/all' })
}

/**
 * @description 创建标签
 * @param { string } name
 * @return { Promise }
 */
export function createArticleTag(data: { name: string }) {
    return request.post({ url: '/articleTag/create', data: data }, { isAuth: true })
}

/**
 * @description 获取热门标签
 * @param { number } limit
 * @return { Promise }
 */
export function getHotTags(data?: { limit?: number }) {
    return request.get({ url: '/articleTag/hot', data: data })
}

/**
 * @description 获取标签下的文章列表
 * @param { number } tag_id
 * @return { Promise }
 */
export function getArticlesByTag(data: { tag_id: number; page_no?: number; page_size?: number }) {
    return request.get({ url: '/articleTag/articles', data: data })
}

/**
 * @description 删除标签
 * @param { number } id
 * @return { Promise }
 */
export function deleteArticleTag(data: { id: number }) {
    return request.post({ url: '/articleTag/delete', data: data }, { isAuth: true })
}
