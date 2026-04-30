import request from '@/utils/request'

export function getArticleWatermark() {
    return request.get({ url: '/setting.article_watermark/getConfig' })
}

export function setArticleWatermark(params: any) {
    return request.post({ url: '/setting.article_watermark/setConfig', params })
}