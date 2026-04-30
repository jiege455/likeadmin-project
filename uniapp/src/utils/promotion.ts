/**
 * 推广链接处理工具
 * 开发者：杰哥网络科技 qq2711793818 杰哥
 */

import { useUserStore } from '@/stores/user'

/**
 * 推广参数接口
 */
export interface PromotionParams {
    invite_code?: string
    merchant_id?: string
}

/**
 * 解析URL中的推广参数
 * @param url 完整URL
 * @returns 推广参数对象
 */
export function parsePromotionParams(url: string): PromotionParams {
    const params: PromotionParams = {}

    try {
        // 处理H5 hash路由
        const hashIndex = url.indexOf('#')
        if (hashIndex !== -1) {
            const hashPart = url.substring(hashIndex + 1)
            const queryIndex = hashPart.indexOf('?')
            if (queryIndex !== -1) {
                const queryString = hashPart.substring(queryIndex + 1)
                const urlParams = new URLSearchParams(queryString)

                const inviteCode = urlParams.get('invite_code')
                const merchantId = urlParams.get('merchant_id')

                if (inviteCode) {
                    params.invite_code = inviteCode
                }
                if (merchantId) {
                    params.merchant_id = merchantId
                }
            }
        }
    } catch (e) {
        console.error('[推广] 解析推广参数失败:', e)
    }

    return params
}

/**
 * 保存推广参数到本地存储
 * @param params 推广参数
 */
export function savePromotionParams(params: PromotionParams): void {
    try {
        if (params.invite_code) {
            uni.setStorageSync('invite_code', params.invite_code)
            console.log('[推广] 保存邀请码:', params.invite_code)
        }
        if (params.merchant_id) {
            uni.setStorageSync('merchant_id', params.merchant_id)
            console.log('[推广] 保存商家ID:', params.merchant_id)
        }
    } catch (e) {
        console.error('[推广] 保存推广参数失败:', e)
    }
}

/**
 * 获取本地存储的推广参数
 * @returns 推广参数对象
 */
export function getStoredPromotionParams(): PromotionParams {
    const params: PromotionParams = {}

    try {
        const inviteCode = uni.getStorageSync('invite_code')
        const merchantId = uni.getStorageSync('merchant_id')

        if (inviteCode) {
            params.invite_code = inviteCode
        }
        if (merchantId) {
            params.merchant_id = merchantId
        }
    } catch (e) {
        console.error('[推广] 获取推广参数失败:', e)
    }

    return params
}

/**
 * 清除本地存储的推广参数
 */
export function clearPromotionParams(): void {
    try {
        uni.removeStorageSync('invite_code')
        uni.removeStorageSync('merchant_id')
        console.log('[推广] 清除推广参数')
    } catch (e) {
        console.error('[推广] 清除推广参数失败:', e)
    }
}

/**
 * 检查是否需要引导用户注册
 * @param params 推广参数
 * @returns 是否需要引导注册
 */
export function shouldGuideToRegister(params: PromotionParams): boolean {
    const userStore = useUserStore()
    return !userStore.isLogin && !!params.invite_code
}

/**
 * 显示引导注册弹窗
 * @param params 推广参数
 * @param targetPage 目标页面（register 或 login）
 */
export function showGuideToRegister(
    params: PromotionParams,
    targetPage: 'register' | 'login' = 'register'
): void {
    if (!shouldGuideToRegister(params)) {
        return
    }

    setTimeout(() => {
        uni.showModal({
            title: '欢迎访问',
            content: '您是通过推广链接访问的，是否立即注册/登录以建立推广关系？',
            confirmText: targetPage === 'register' ? '去注册' : '去登录',
            cancelText: '稍后再说',
            success: (res) => {
                if (res.confirm) {
                    uni.navigateTo({
                        url: `/pages/${targetPage}/${targetPage}`
                    })
                }
            }
        })
    }, 2000)
}

/**
 * 生成推广链接
 * @param baseUrl 基础URL
 * @param params 推广参数
 * @returns 完整的推广链接
 */
export function generatePromotionLink(baseUrl: string, params: PromotionParams): string {
    const url = new URL(baseUrl)

    if (params.invite_code) {
        url.searchParams.set('invite_code', params.invite_code)
    }
    if (params.merchant_id) {
        url.searchParams.set('merchant_id', params.merchant_id)
    }

    return url.toString()
}

/**
 * 生成H5推广链接（hash路由）
 * @param path 页面路径
 * @param params 推广参数
 * @returns 完整的推广链接
 */
export function generateH5PromotionLink(path: string, params: PromotionParams): string {
    // #ifdef H5
    const baseUrl = window.location.origin
    let link = `${baseUrl}/#${path}`

    const queryParams: string[] = []
    if (params.invite_code) {
        queryParams.push(`invite_code=${params.invite_code}`)
    }
    if (params.merchant_id) {
        queryParams.push(`merchant_id=${params.merchant_id}`)
    }

    if (queryParams.length > 0) {
        link += '?' + queryParams.join('&')
    }

    return link
    // #endif

    // #ifndef H5
    return path
    // #endif
}

/**
 * 处理推广链接访问
 * 统一处理推广参数的解析、保存和引导注册
 * @param url 当前页面URL
 */
export function handlePromotionVisit(url: string): void {
    // 解析推广参数
    const params = parsePromotionParams(url)

    // 保存推广参数
    savePromotionParams(params)

    // 引导注册
    showGuideToRegister(params)
}

export default {
    parsePromotionParams,
    savePromotionParams,
    getStoredPromotionParams,
    clearPromotionParams,
    shouldGuideToRegister,
    showGuideToRegister,
    generatePromotionLink,
    generateH5PromotionLink,
    handlePromotionVisit
}
