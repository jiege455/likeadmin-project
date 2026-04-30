import request from '@/utils/request'

// 期次列表
export function issueLists(params: any) {
    return request.get({ url: '/series.issue/lists', params })
}

// 期次详情
export function issueDetail(params: any) {
    return request.get({ url: '/series.issue/detail', params })
}

// 添加期次
export function issueAdd(params: any) {
    return request.post({ url: '/series.issue/add', params })
}

// 编辑期次
export function issueEdit(params: any) {
    return request.post({ url: '/series.issue/edit', params })
}

// 删除期次
export function issueDelete(params: any) {
    return request.post({ url: '/series.issue/delete', params })
}

// 发布期次
export function issuePublish(params: any) {
    return request.post({ url: '/series.issue/publish', params })
}
