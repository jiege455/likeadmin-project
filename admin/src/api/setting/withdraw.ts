import request from '@/utils/request'

// 获取提现设置
export function withdrawGetConfig(params?: any) {
    return request.get({ url: '/setting.withdraw/getConfig', params })
}

// 保存提现设置
export function withdrawSetConfig(params: any) {
    return request.post({ url: '/setting.withdraw/setConfig', params })
}
