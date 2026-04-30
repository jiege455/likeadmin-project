import request from '@/utils/request'

// 待处理审批列表
export function pendingApprovalLists(params: any) {
    return request.get({ url: '/pending_approval/lists', params })
}

// 待处理审批统计
export function pendingApprovalStatistics() {
    return request.get({ url: '/pending_approval/statistics' })
}

// 快捷审批
export function pendingApprovalAudit(params: any) {
    return request.post({ url: '/pending_approval/audit', params })
}

// 审批详情
export function pendingApprovalDetail(params: any) {
    return request.get({ url: '/pending_approval/detail', params })
}
