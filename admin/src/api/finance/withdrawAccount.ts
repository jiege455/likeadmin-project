import request from '@/utils/request'

// 收款账户列表
export function withdrawAccountLists(params: any) {
    return request.get({ url: '/finance.withdrawAccount/lists', params })
}

// 收款账户详情
export function withdrawAccountDetail(params: any) {
    return request.get({ url: '/finance.withdrawAccount/detail', params })
}

// 设置账户状态
export function withdrawAccountSetStatus(params: any) {
    return request.post({ url: '/finance.withdrawAccount/setStatus', params })
}
