import request from '@/utils/request'

// 获取商户设置
export function merchantGetConfig(params?: any) {
    return request.get({ url: '/setting.merchant/getConfig', params })
}

// 保存商户设置
export function merchantSetConfig(params: any) {
    return request.post({ url: '/setting.merchant/setConfig', params })
}
