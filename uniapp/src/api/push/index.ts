/**
 * 推送关键词相关接口
 * 开发者：杰哥网络科技 qq2711793818 杰哥
 */
import request from '@/utils/request'

export function getPushKeywordLists(data: any) {
    return request.get({ url: '/push_keyword/lists', data })
}

export function addPushKeyword(data: any) {
    return request.post({ url: '/push_keyword/add', data })
}

export function editPushKeyword(data: any) {
    return request.post({ url: '/push_keyword/edit', data })
}

export function deletePushKeyword(data: any) {
    return request.post({ url: '/push_keyword/delete', data })
}

export function togglePushKeyword(data: any) {
    return request.post({ url: '/push_keyword/toggle', data })
}

export function getPushMessageLists(data: any) {
    return request.get({ url: '/push_message/lists', data })
}

export function readPushMessage(data: any) {
    return request.post({ url: '/push_message/read', data })
}

export function readAllPushMessage() {
    return request.post({ url: '/push_message/readAll' })
}

export function getPushMessageUnreadCount() {
    return request.get({ url: '/push_message/unreadCount' })
}

export function deletePushMessage(data: any) {
    return request.post({ url: '/push_message/delete', data })
}
