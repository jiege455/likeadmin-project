// 邮箱验证码相关API
// 开发者：杰哥网络科技 qq2711793818 杰哥

export function emailSendCode(params: { email: string; scene: string }) {
    return $request.post({ url: '/email/sendCode', params })
}

export function emailVerify(params: { email: string; code: string; scene: string }) {
    return $request.post({ url: '/email/verify', params })
}
