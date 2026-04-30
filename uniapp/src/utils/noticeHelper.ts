/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * 系统公告优化 - 增加 APP 角标和强制阅读功能
 */

// ========== 1. APP 角标设置 ==========

/**
 * 设置 APP 角标数字
 * @param {number} count - 未读数量
 */
export function setAppBadge(count: number) {
    // #ifdef APP-PLUS
    if (count > 0) {
        plus.runtime.setBadge(count.toString())
    } else {
        plus.runtime.setBadge('')
    }
    // #endif

    // #ifdef H5
    if (count > 0) {
        document.title = `(${count}) ${document.title}`
    }
    // #endif
}

/**
 * 清除 APP 角标
 */
export function clearAppBadge() {
    // #ifdef APP-PLUS
    plus.runtime.setBadge('')
    // #endif

    // #ifdef H5
    const match = document.title.match(/^\(\d+\)\s*(.*)/)
    if (match) {
        document.title = match[1]
    }
    // #endif
}

// ========== 2. 小程序订阅消息 ==========

/**
 * 请求订阅消息模板
 * @param {string} templateId - 模板 ID
 * @returns {Promise}
 */
export function requestSubscribeMessage(templateId: string) {
    // #ifdef MP-WEIXIN
    return new Promise((resolve, reject) => {
        wx.requestSubscribeMessage({
            tmplIds: [templateId],
            success: (res) => {
                if (res[templateId] === 'accept') {
                    resolve(true)
                } else {
                    reject(new Error('用户拒绝订阅'))
                }
            },
            fail: (err) => {
                reject(err)
            }
        })
    })
    // #endif

    // #ifndef MP-WEIXIN
    return Promise.reject(new Error('非微信小程序环境'))
    // #endif
}

/**
 * 发送系统公告订阅消息
 * @param {Object} notice - 公告信息
 */
export async function sendNoticeSubscribeMessage(notice: any) {
    // #ifdef MP-WEIXIN
    try {
        // 先请求用户订阅
        await requestSubscribeMessage('YOUR_TEMPLATE_ID')

        // 调用后端发送订阅消息
        const res = await uni.request({
            url: '/api/notice/sendSubscribe',
            method: 'POST',
            data: {
                notice_id: notice.id,
                title: notice.title,
                content: notice.content
            }
        })

        return res
    } catch (e) {
        console.error('发送订阅消息失败:', e)
        return null
    }
    // #endif
}

// ========== 3. 强制阅读弹窗 ==========

/**
 * 显示强制阅读弹窗（必须点击"已阅读"才能关闭）
 * @param {Object} notice - 公告信息
 * @param {Function} onConfirm - 确认回调
 */
export function showForceReadNotice(notice: any, onConfirm?: Function) {
    uni.showModal({
        title: '重要公告',
        content: notice.title,
        showCancel: false,
        confirmText: '我已阅读',
        confirmColor: '#07c160',
        success: (res) => {
            if (res.confirm) {
                // 标记为已读
                markNoticeRead(notice.id)

                // 执行确认回调
                if (onConfirm) {
                    onConfirm()
                }
            }
        }
    })
}

/**
 * 标记公告已读
 * @param {number} noticeId - 公告 ID
 */
export async function markNoticeRead(noticeId: number) {
    try {
        await uni.request({
            url: '/notice/markRead',
            method: 'POST',
            data: { id: noticeId }
        })
    } catch (e) {
        console.error('标记已读失败:', e)
    }
}

// ========== 4. 跑马灯滚动公告 ==========

/**
 * 显示顶部滚动公告
 * @param {Array} notices - 公告列表
 */
export function showScrollNotice(notices: any[]) {
    if (!notices || notices.length === 0) return

    // 存储到本地，在首页或其他页面显示
    uni.setStorageSync('scroll_notices', notices)

    // 触发全局事件，通知各页面更新
    uni.$emit('updateScrollNotice', notices)
}

// ========== 5. 综合应用 ==========

/**
 * 检查并处理新公告（综合应用）
 * @param {Boolean} forceRead - 是否强制阅读
 */
export async function checkAndHandleNotices(forceRead = false) {
    try {
        // 获取未读数量
        const unreadRes = await uni.request({
            url: '/notice/unreadCount',
            method: 'GET'
        })

        const unreadCount = unreadRes.data?.data?.count || 0

        // 设置角标
        if (unreadCount > 0) {
            setAppBadge(unreadCount)
        } else {
            clearAppBadge()
        }

        // 获取最新公告
        const latestRes = await uni.request({
            url: '/notice/popup',
            method: 'GET'
        })

        const latestNotice = latestRes.data?.data

        if (latestNotice && !latestNotice.is_read) {
            if (forceRead || latestNotice.is_force_read) {
                // 强制阅读模式
                showForceReadNotice(latestNotice)
            } else {
                // 普通弹窗模式
                uni.$emit('showNoticePopup', latestNotice)
            }
        }

        return {
            unreadCount,
            latestNotice
        }
    } catch (e) {
        console.error('检查公告失败:', e)
        return null
    }
}

export default {
    setAppBadge,
    clearAppBadge,
    requestSubscribeMessage,
    sendNoticeSubscribeMessage,
    showForceReadNotice,
    markNoticeRead,
    showScrollNotice,
    checkAndHandleNotices
}
