/**
 * 金额格式化工具
 * 开发者公众号：杰哥网络科技。
 * QQ：2711793818 杰哥
 */

/**
 * 格式化金额
 * @param amount 金额（数字或字符串）
 * @param decimals 小数位数，默认 2 位
 * @returns 格式化后的金额字符串
 */
export function formatMoney(amount: number | string = 0, decimals = 2): string {
    const num = typeof amount === 'string' ? parseFloat(amount) : Number(amount)
    if (isNaN(num)) {
        return '0.00'
    }
    return num.toFixed(decimals)
}

/**
 * 格式化金额带符号
 * @param amount 金额
 * @param symbol 货币符号，默认¥
 * @returns 带符号的格式化金额字符串
 */
export function formatMoneyWithSymbol(amount: number | string = 0, symbol = '¥'): string {
    return symbol + formatMoney(amount)
}

/**
 * 计算两个金额的差值
 * @param amount1 金额 1
 * @param amount2 金额 2
 * @returns 差值（保留 2 位小数）
 */
export function calculateMoneyDiff(amount1: number | string, amount2: number | string): number {
    const num1 = typeof amount1 === 'string' ? parseFloat(amount1) : Number(amount1)
    const num2 = typeof amount2 === 'string' ? parseFloat(amount2) : Number(amount2)

    if (isNaN(num1) || isNaN(num2)) {
        return 0
    }

    return Number((num1 - num2).toFixed(2))
}

/**
 * 判断金额是否有效（大于 0 且为数字）
 * @param amount 金额
 * @returns 是否有效
 */
export function isValidMoney(amount: number | string): boolean {
    const num = typeof amount === 'string' ? parseFloat(amount) : Number(amount)
    return !isNaN(num) && num > 0
}
