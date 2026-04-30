import request from '@/utils/request'

// 获取分销中心数据
export function getDistributionIndex() {
    return request.get({ url: '/distribution/index/info' })
}

// 获取分销记录
export function getDistributionLog(data: any) {
    return request.get({ url: '/distribution/lists', data })
}

// 获取推广海报信息
export function getDistributionPoster() {
    return request.get({ url: '/distribution/poster' })
}

// 申请成为分销员
export function applyDistribution(data: any) {
    return request.post({ url: '/distribution.apply/submit', data })
}

// 获取分销员申请详情
export function getApplyDetail() {
    return request.get({ url: '/distribution.apply/info' })
}

// 获取分销佣金日志
export function getDistributionCommissionLog(data: any) {
    return request.get({ url: '/distribution/commissionLog', data })
}
