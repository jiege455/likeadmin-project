import { isObject } from '@vue/shared'
import { getToken } from './auth'
import { parseQuery } from 'uniapp-router-next'
import routes from 'uni-router-routes'

const normalizeRoutePath = (path: string) => (path?.startsWith('/') ? path : `/${path}`)

const routePathSet = new Set<string>(
    (Array.isArray(routes) ? routes : [])
        .map((item: any) => item?.path)
        .filter(Boolean)
        .map((path: string) => normalizeRoutePath(path))
)

const normalizeUrlForSubPackages = (url: string) => {
    const [rawPath, rawQuery] = url.split('?')
    const path = normalizeRoutePath(rawPath)

    // 如果已经在 packages 目录下，直接返回（防止重复拼接）
    if (path.startsWith('/packages/')) return url

    if (!path.startsWith('/pages/')) return url

    if (routePathSet.has(path)) return url

    const candidate = normalizeRoutePath(`/packages${path}`)
    if (!routePathSet.has(candidate)) return url

    return rawQuery ? `${candidate}?${rawQuery}` : candidate
}

/**
 * @description 获取元素节点信息（在组件中的元素必须要传ctx）
 * @param  { String } selector 选择器 '.app' | '#app'
 * @param  { Boolean } all 是否多选
 * @param  { ctx } context 当前组件实例
 */
export const getRect = (selector: string, all = false, context?: any) => {
    return new Promise((resolve, reject) => {
        let qurey = uni.createSelectorQuery()
        if (context) {
            qurey = uni.createSelectorQuery().in(context)
        }
        qurey[all ? 'selectAll' : 'select'](selector)
            .boundingClientRect(function (rect) {
                if (all && Array.isArray(rect) && rect.length) {
                    return resolve(rect)
                }
                if (!all && rect) {
                    return resolve(rect)
                }
                reject('找不到元素')
            })
            .exec()
    })
}

/**
 * @description 获取当前页面实例
 */
export function currentPage() {
    const pages = getCurrentPages()
    // 检查数组是否为空，避免越界访问
    if (!pages || pages.length === 0) {
        console.warn('[currentPage] 当前没有页面')
        return {}
    }
    const currentPage = pages[pages.length - 1]
    return currentPage || {}
}

/**
 * @description 后台选择链接专用跳转
 */
interface Link {
    path: string
    name?: string
    type: string
    canTab: boolean
    query?: Record<string, any>
}

export enum LinkTypeEnum {
    'SHOP_PAGES' = 'shop',
    'CUSTOM_LINK' = 'custom',
    'MINI_PROGRAM' = 'mini_program'
}

export function navigateTo(
    link: Link,
    navigateType: 'navigateTo' | 'switchTab' | 'reLaunch' = 'navigateTo'
) {
    // 如果是小程序跳转
    if (link.type === LinkTypeEnum.MINI_PROGRAM) {
        navigateToMiniProgram(link)
        return
    }

    const url = normalizeUrlForSubPackages(
        link?.query ? `${link.path}?${objectToQuery(link?.query)}` : link.path
    )

    // 修复：如果 URL 包含参数（如自定义页面 ?id=xx），或者不是 tabbar 页面，强制使用 navigateTo
    // 防止旧数据中 canTab=true 导致 switchTab 失败
    const isCustomPage = url.includes('?')
    // 强制判断是否为 packages 目录下的页面，如果是，强制使用 navigateTo
    const isPackagePage = url.includes('/packages/')

    if ((navigateType == 'switchTab' || link.canTab) && !isCustomPage && !isPackagePage) {
        uni.switchTab({ url })
        return
    }
    if (navigateType == 'reLaunch') {
        uni.reLaunch({ url })
        return
    }
    uni.navigateTo({ url })
}

/**
 * @description 小程序跳转
 * @param link 跳转信息，由装修数据进行输入
 */
export function navigateToMiniProgram(link: Link) {
    const query = link.query
    // #ifdef H5
    window.open(
        `weixin://dl/business/?appid=${query?.appId}&path=${query?.path}&env_version=${
            query?.env_version
        }&query=${encodeURIComponent(query?.query)}`
    )
    // #endif
    // #ifdef MP
    uni.navigateToMiniProgram({
        appId: query?.appId,
        path: query?.path,
        extraData: parseQuery(query?.query),
        envVersion: query?.env_version
    })
    // #endif
}

/**
 * @description 将一个数组分成几个同等长度的数组
 * @param  { Array } array[分割的原数组]
 * @param  { Number } size[每个子数组的长度]
 */
export const sliceArray = (array: any[], size: number) => {
    const result = []
    for (let x = 0; x < Math.ceil(array.length / size); x++) {
        const start = x * size
        const end = start + size
        result.push(array.slice(start, end))
    }
    return result
}

/**
 * @description 是否为空
 * @param {unknown} value
 * @return {Boolean}
 */
export const isEmpty = (value: unknown) => {
    return value == null && typeof value == 'undefined'
}

/**
 * @description 对象格式化为Query语法
 * @param { Object } params
 * @return {string} Query语法
 */
export function objectToQuery(params: Record<string, any>): string {
    let query = ''
    for (const props of Object.keys(params)) {
        const value = params[props]
        const part = encodeURIComponent(props) + '='
        if (!isEmpty(value)) {
            console.log(encodeURIComponent(props), isObject(value))
            if (isObject(value)) {
                for (const key of Object.keys(value)) {
                    if (!isEmpty(value[key])) {
                        const params = props + '[' + key + ']'
                        const subPart = encodeURIComponent(params) + '='
                        query += subPart + encodeURIComponent(value[key]) + '&'
                    }
                }
            } else {
                query += part + encodeURIComponent(value) + '&'
            }
        }
    }
    return query.slice(0, -1)
}

/**
 * @description 添加单位
 * @param {String | Number} value 值 100
 * @param {String} unit 单位 px em rem
 */
export const addUnit = (value: string | number, unit = 'rpx') => {
    return !Object.is(Number(value), NaN) ? `${value}${unit}` : value
}

/**
 * @description 格式化输出价格
 * @param  { string } price 价格
 * @param  { string } take 小数点操作
 * @param  { string } prec 小数位补
 */
export function formatPrice({ price, take = 'all', prec = undefined }: any) {
    let [integer, decimals = ''] = (price + '').split('.')

    // 小数位补
    if (prec !== undefined) {
        const LEN = decimals.length
        for (let i = prec - LEN; i > 0; --i) decimals += '0'
        decimals = decimals.substr(0, prec)
    }

    switch (take) {
        case 'int':
            return integer
        case 'dec':
            return decimals
        case 'all':
            return integer + '.' + decimals
    }
}

/**
 * @description 组合异步任务
 * @param  { string } task 异步任务
 */

export function series(...task: Array<(_arg: any) => any>) {
    return function (): Promise<any> {
        return new Promise((resolve, reject) => {
            const iteratorTask = task.values()
            const next = (res?: any) => {
                const nextTask = iteratorTask.next()
                if (nextTask.done) {
                    resolve(res)
                } else {
                    Promise.resolve(nextTask.value(res)).then(next).catch(reject)
                }
            }
            next()
        })
    }
}

const TAB_BAR_PAGES = ['/pages/index/index', '/pages/user/user']

export function safeNavigateBack(
    options: {
        defaultUrl?: string
        delta?: number
    } = {}
): void {
    const { defaultUrl = '/pages/index/index', delta = 1 } = options

    try {
        const pages = getCurrentPages()

        if (!pages || pages.length === 0) {
            navigateToDefaultPage(defaultUrl)
            return
        }

        if (pages.length > 1) {
            uni.navigateBack({
                delta,
                fail: () => {
                    navigateToDefaultPage(defaultUrl)
                }
            })
        } else {
            navigateToDefaultPage(defaultUrl)
        }
    } catch (error) {
        navigateToDefaultPage(defaultUrl)
    }
}

function navigateToDefaultPage(url: string): void {
    const targetUrl = url || '/pages/index/index'

    if (TAB_BAR_PAGES.some((page) => targetUrl.includes(page))) {
        uni.switchTab({
            url: targetUrl,
            fail: () => {
                uni.reLaunch({ url: '/pages/index/index' })
            }
        })
    } else {
        uni.reLaunch({
            url: targetUrl,
            fail: () => {
                uni.switchTab({ url: '/pages/index/index' })
            }
        })
    }
}
