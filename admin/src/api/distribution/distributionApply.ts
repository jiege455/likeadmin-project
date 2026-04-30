import request from '@/utils/request'

// 推广员申请列表
export function distributionApplyLists(params: any) {
    return request.get({ url: '/distribution.distributionApply/lists', params })
}

// 审核推广员申请
export function distributionApplyAudit(params: any) {
    return request.post({ url: '/distribution.distributionApply/audit', params })
}

// 删除申请
export function distributionApplyDelete(params: any) {
    return request.post({ url: '/distribution.distributionApply/delete', params })
}
