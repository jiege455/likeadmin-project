import request from '@/utils/request'

// 系列列表
export function seriesLists(params: any) {
    return request.get({ url: '/series.series/lists', params })
}

// 系列详情
export function seriesDetail(params: any) {
    return request.get({ url: '/series.series/detail', params })
}

// 添加系列
export function seriesAdd(params: any) {
    return request.post({ url: '/series.series/add', params })
}

// 编辑系列
export function seriesEdit(params: any) {
    return request.post({ url: '/series.series/edit', params })
}

// 删除系列
export function seriesDelete(params: any) {
    return request.post({ url: '/series.series/delete', params })
}

// 系列状态
export function seriesStatus(params: any) {
    return request.post({ url: '/series.series/status', params })
}
