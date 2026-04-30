import request from '@/utils/request'

export function getArticleTips() {
    return request.get({ url: '/setting.article_tips/getConfig' })
}

export function setArticleTips(params: any) {
    return request.post({ url: '/setting.article_tips/setConfig', params })
}
