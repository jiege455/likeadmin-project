import request from '@/utils/request'

export function getNoticeList(data: any) {
    return request.get({ url: '/notice/lists', data })
}

export function getNoticeDetail(data: any) {
    return request.get({ url: '/notice/detail', data })
}

export function getNoticeUnreadCount() {
    return request.get({ url: '/notice/unreadCount' })
}

export function getNoticePopup() {
    return request.get({ url: '/notice/popup' })
}

export function markNoticeRead(data: any) {
    return request.post({ url: '/notice/markRead', data })
}

export function markAllNoticeRead() {
    return request.post({ url: '/notice/markAllRead' })
}
