import request from '@/utils/request'

// 获取抽成配置
export function getCommissionConfig() {
    return request.get({ url: '/setting.config/getCommissionConfig' })
}

// 设置抽成配置
export function setCommissionConfig(params: any) {
    return request.post({ url: '/setting.config/setCommissionConfig', params })
}

// 获取邮箱配置
export function getSmtpConfig() {
    return request.get({ url: '/setting.config/getSmtpConfig' })
}

// 设置邮箱配置
export function setSmtpConfig(params: any) {
    return request.post({ url: '/setting.config/setSmtpConfig', params })
}
