import request from '@/utils/request'

export function getPayDesc() {
    return request.get({ url: '/setting.pay.pay_desc/lists' })
}

export function setPayDesc(data: Record<string, any>) {
    return request.post({ url: '/setting.pay.pay_desc/set', data })
}
