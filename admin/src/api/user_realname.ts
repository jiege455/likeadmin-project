import request from '@/utils/request'

// 列表
export function realnameLists(params: any) {
    return request.get({ url: '/user.user_realname/lists', params })
}

// 审核
export function realnameAudit(params: any) {
    return request.post({ url: '/user.user_realname/audit', params })
}

// 获取配置
export function getRealnameConfig() {
    return request.get({ url: '/user.user_realname/getConfig' })
}

// 保存配置
export function setRealnameConfig(params: any) {
    return request.post({ url: '/user.user_realname/setConfig', params })
}
