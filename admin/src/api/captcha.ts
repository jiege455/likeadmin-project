/**
 * 滑块验证码 API
 * 开发者：杰哥网络科技
 * QQ: 2711793818
 */
import request from '@/utils/request'

export interface CaptchaData {
    bg_img: string
    slider_img: string
    key: string
    y: number
    max_x?: number
}

export interface CaptchaResult {
    verified: boolean
}

export function captchaGet() {
    return request.get({ url: '/captcha/get' })
}

export function captchaCheck(data: { key: string; x: number }) {
    return request.post({ url: '/captcha/check', params: data })
}
