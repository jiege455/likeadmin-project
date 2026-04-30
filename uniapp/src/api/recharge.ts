import request from '@/utils/request'

//充值
export function recharge(data: any) {
    return request.post({ url: '/recharge/recharge', data }, { isAuth: true })
}

//充值记录
export function rechargeRecord(data: any) {
    return request.get({ url: '/recharge/lists', data }, { isAuth: true })
}

// 充值配置 - 添加防重复请求机制
let isLoadingConfig = false
let configPromise: Promise<any> | null = null

export function rechargeConfig() {
    // 如果正在加载中，返回之前的 Promise
    if (isLoadingConfig && configPromise) {
        return configPromise
    }

    isLoadingConfig = true
    configPromise = request.get({ url: '/recharge/config' }, { isAuth: true })

    configPromise
        .then(() => {
            isLoadingConfig = false
        })
        .catch(() => {
            isLoadingConfig = false
        })

    return configPromise
}
