import request from '@/utils/request'

export function getOrderList(data: any) {
    return request.get({ url: '/order/lists', data }).then((res) => {
        if (!res) {
            return { lists: [], count: 0 }
        }
        return {
            lists: Array.isArray(res.lists) ? res.lists : [],
            count: res.count || 0
        }
    })
}

export function getOrderDetail(id: number) {
    return request.get({ url: '/order/detail', data: { id } }).then((res) => {
        return res || {}
    })
}

// 取消订单
export function cancelOrder(id: number) {
    return request.post({ url: '/order/cancel', data: { id } })
}

// 删除订单
export function deleteOrder(id: number) {
    return request.post({ url: '/order/del', data: { id } })
}

// 确认收货
export function confirmOrder(id: number) {
    return request.post({ url: '/order/confirm', data: { id } })
}

// 订单物流
export function orderTraces(id: number) {
    return request.get({ url: '/order/traces', data: { id } })
}

// 获取订单统计数据
export function getOrderStatistics() {
    return request.get({ url: '/order/statistics' }).then((res) => {
        return res || { total_orders: 0, total_amount: '0.00', pending_orders: 0 }
    })
}
