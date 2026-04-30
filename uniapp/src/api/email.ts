import request from '@/utils/request'

export function emailSendCode(params: { email: string; scene: string }) {
    return request.post({ url: '/email/sendCode', data: params })
}

export function emailVerify(params: { email: string; code: string; scene: string }) {
    return request.post({ url: '/email/verify', data: params })
}
