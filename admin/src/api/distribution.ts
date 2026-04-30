import request from '@/utils/request'

// 分销记录
export function distributionLists(params: any) {
    return request.get({ url: '/distribution.distribution/lists', params })
}

// 推广员列表
export function promoterLists(params: any) {
    return request.get({ url: '/distribution.promoter/lists', params })
}

// 申请列表
export function applyLists(params: any) {
    return request.get({ url: '/distribution.distributionApply/lists', params })
}

// 审核申请
export function applyAudit(params: any) {
    return request.post({ url: '/distribution.distributionApply/audit', params })
}

// 删除申请
export function applyDelete(params: any) {
    return request.post({ url: '/distribution.distributionApply/delete', params })
}
