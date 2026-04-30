import request from '@/utils/request'

// 业务通知列表
export function getMessageList(data: any) {
    return request.get({ url: '/message/lists', data })
}

// 标记已读
export function readMessage(data: any) {
    return request.post({ url: '/message/read', data })
}
