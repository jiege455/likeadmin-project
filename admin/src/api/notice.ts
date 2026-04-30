import request from '@/utils/request'

export function systemNoticeLists(params: any) {
    return request.get({ url: '/notice.system_notice/lists', params })
}

export function systemNoticeDetail(params: any) {
    return request.get({ url: '/notice.system_notice/detail', params })
}

export function addSystemNotice(params: any) {
    return request.post({ url: '/notice.system_notice/add', params })
}

export function editSystemNotice(params: any) {
    return request.post({ url: '/notice.system_notice/edit', params })
}

export function deleteSystemNotice(params: any) {
    return request.post({ url: '/notice.system_notice/delete', params })
}
