import { client } from '@/utils/client'
import request from '@/utils/request'

// 登录
export function login(data: Record<string, any>) {
    return request.post({
        url: '/login/account',
        data: { ...data, terminal: client, merchant_id: uni.getStorageSync('merchant_id') || '' }
    })
}

//注册
export function register(data: Record<string, any>) {
    return request.post({
        url: '/login/register',
        data: { ...data, channel: client, merchant_id: uni.getStorageSync('merchant_id') || '' }
    })
}

//向微信请求code的链接
export function getWxCodeUrl(data: Record<string, any>) {
    return request.get({ url: '/login/codeUrl', data })
}

export function OALogin(data: Record<string, any>) {
    return request.post({ url: '/login/oaLogin', data })
}

export function mnpLogin(data: Record<string, any>) {
    return request.post({ url: '/login/mnpLogin', data })
}

//更新微信小程序头像昵称
export function updateUser(data: Record<string, any>, header: any) {
    return request.post({ url: '/login/updateUser', data, header })
}

//小程序绑定微信
export function mnpAuthBind(data: Record<string, any>) {
    return request.post({ url: '/login/mnpAuthBind', data })
}

//公众号绑定微信
export function oaAuthBind(data: Record<string, any>) {
    return request.post({ url: '/login/oaAuthBind', data })
}

//获取邀请人信息
export function getInviterInfo(inviteCode: string) {
    return request.get({ url: '/login/getInviterInfo', data: { invite_code: inviteCode } })
}
