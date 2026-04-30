import request from '@/utils/request'

// 支付方式
export function getPayWay(data: any) {
    return request.get({ url: '/pay/payWay', data }, { isAuth: true }).then((res) => {
        if (!res || !Array.isArray(res.lists)) {
            throw new Error('支付方式数据格式错误')
        }
        return res
    })
}

// 预支付
export function prepay(data: any) {
    return request.post({ url: '/pay/prepay', data }, { isAuth: true }).then((res) => {
        if (!res || !res.pay_way || !res.config) {
            throw new Error('预支付返回数据格式错误')
        }
        return res
    })
}

// 查询支付结果
export function getPayResult(data: any) {
    return request.get({ url: '/pay/payStatus', data }, { isAuth: true }).then((res) => {
        if (!res || typeof res.pay_status === 'undefined') {
            throw new Error('支付状态查询失败')
        }
        return res
    })
}
