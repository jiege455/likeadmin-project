import request from '@/utils/request'

export function couponLists(params: any) {
    return request.get({ url: '/marketing.coupon/lists', params })
}

export function couponAdd(params: any) {
    return request.post({ url: '/marketing.coupon/add', params })
}

export function couponEdit(params: any) {
    return request.post({ url: '/marketing.coupon/edit', params })
}

export function couponDel(params: any) {
    return request.post({ url: '/marketing.coupon/del', params })
}

export function couponDetail(params: any) {
    return request.get({ url: '/marketing.coupon/detail', params })
}
