import request from '@/utils/request'

// 登录
export function login(params: any) {
    return request.post({ url: '/login/account', params })
}

// 退出登录
export function logout() {
    return request.post({ url: '/login/logout' })
}

// 获取用户信息
export function getUserInfo() {
    return request.get({ url: '/auth.admin/mySelf' })
}

// 设置用户信息
export function setUserInfo(params: any) {
    return request.post({ url: '/auth.admin/editSelf', params })
}

// 邀请记录
export function inviteLists(params: any) {
    return request.get({ url: '/user.invite/lists', params })
}
