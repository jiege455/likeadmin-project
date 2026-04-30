import request from '@/utils/request'

// 页面装修详情
export function getDecoratePages(params: any) {
    return request.get({ url: '/decorate.page/detail', params }, { ignoreCancelToken: true })
}

// 页面装修保存
export function setDecoratePages(params: any) {
    return request.post({ url: '/decorate.page/save', params })
}

// 获取首页文章数据
export function getDecorateArticle(params?: any) {
    return request.get({ url: '/decorate.data/article', params })
}

// 底部导航详情
export function getDecorateTabbar(params?: any) {
    return request.get({ url: '/decorate.tabbar/detail', params })
}

// 底部导航保存
export function setDecorateTabbar(params: any) {
    return request.post({ url: '/decorate.tabbar/save', params })
}

// pc装修数据
export function getDecoratePc() {
    return request.get({ url: '/decorate.data/pc' })
}

// 设置PC端访问开关
export function setDecoratePcOpen(params: { pc_open: number; pc_close_tips?: string }) {
    return request.post({ url: '/decorate.data/setPcOpen', params })
}

// 获取微页面列表
export function getDecoratePageList(params?: any) {
    return request.get({ url: '/decorate.page/lists', params })
}

// 新增装修页面
export function addDecoratePage(params: any) {
    return request.post({ url: '/decorate.page/add', params })
}

// 删除装修页面
export function delDecoratePage(params: any) {
    return request.post({ url: '/decorate.page/del', params })
}
