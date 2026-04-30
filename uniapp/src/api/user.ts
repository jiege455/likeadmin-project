import request from '@/utils/request'

export function getUserCenter(header?: any) {
    return request.get({ url: '/user/center', header }, { ignoreCancel: true })
}

// 个人信息
export function getUserInfo() {
    return request.get({ url: '/user/center' })
}

// 个人编辑
export function userEdit(data: any) {
    return request.post({ url: '/user/setInfo', data }, { isAuth: true })
}

// 绑定手机
export function userBindMobile(data: any, header?: any) {
    return request.post({ url: '/user/bindMobile', data, header }, { isAuth: true })
}

// 绑定邮箱
export function bindUserEmail(data: any) {
    return request.post({ url: '/user/bindEmail', data }, { isAuth: true })
}

// 微信电话
export function userMnpMobile(data: any) {
    return request.post({ url: '/user/getMobileByMnp', data }, { isAuth: true })
}

// 更改手机号
export function userChangePwd(data: any) {
    return request.post({ url: '/user/changePassword', data }, { isAuth: true })
}

//忘记密码
export function forgotPassword(data: Record<string, any>) {
    return request.post({ url: '/user/resetPassword', data })
}

//通过邮箱验证码重置密码
export function forgotPasswordByEmail(data: Record<string, any>) {
    return request.post({ url: '/user/resetPasswordByEmail', data })
}

//余额明细
export function accountLog(data: any) {
    return request.get({ url: '/account_log/lists', data })
}

// 实名信息
export function getRealnameInfo() {
    return request.get({ url: '/user.user_realname/info' }, { isAuth: true })
}

// 提交实名
export function submitRealname(data: any) {
    return request.post({ url: '/user.user_realname/submit', data }, { isAuth: true })
}
