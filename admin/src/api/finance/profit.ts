import request from '@/utils/request'

// 收益统计
export function profitStatistics(params?: any) {
    return request.get({ url: '/finance.profit/statistics', params })
}

// 收益趋势
export function profitTrend(params: any) {
    return request.get({ url: '/finance.profit/trend', params })
}

// 商户收益
export function merchantProfit(params: any) {
    return request.get({ url: '/finance.profit/merchantProfit', params })
}

// 结算列表
export function settleList(params: any) {
    return request.get({ url: '/finance.profit/settleList', params })
}

// 结算
export function settle(params: any) {
    return request.post({ url: '/finance.profit/settle', params })
}
