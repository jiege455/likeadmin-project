import request from '@/utils/request'

export function getSwitchConfig() {
    return request.get({ url: '/system/switchConfig' })
}
