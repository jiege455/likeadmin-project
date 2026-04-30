import request from '@/utils/request'

export function merchantApply(data: any) {
    return request.post({ url: '/merchant.apply/add', data })
}

export function getApplyDetail() {
    return request.get({ url: '/merchant.apply/detail' })
}

export function getMerchantDetail() {
    return request.get({ url: '/merchant/detail' })
}

export function getMerchantInfoById(id: number) {
    return request.get({ url: '/merchant/info', data: { id } })
}

export function getMerchantFinance() {
    return request.get({ url: '/finance.merchant_finance/detail' })
}

export function addMerchantComplaint(data: any) {
    return request.post({ url: '/merchant.complaint/add', data })
}

export function getFollowLists(data: any) {
    return request.get({ url: '/merchant.follow/lists', data })
}

export function toggleFollow(data: any) {
    return request.post({ url: '/merchant.follow/toggle', data })
}

export function togglePush(data: any) {
    return request.post({ url: '/merchant.follow/togglePush', data })
}

export function getMerchantLists(data: any) {
    return request.get({ url: '/merchant/lists', data })
}

export function getMerchantStatistics() {
    return request.get({ url: '/merchant.statistics/index' }).then((res) => {
        if (!res) {
            throw new Error('商家统计数据格式错误')
        }
        return res
    })
}

export function getMerchantArticleLists(data: any) {
    return request.get({ url: '/merchant.article/lists', data })
}

export function getMerchantArticleDetail(id: number) {
    return request.get({ url: '/merchant.article/detail', data: { id } })
}

export function saveMerchantArticle(data: any) {
    return request.post({ url: '/merchant.article/save', data })
}

export function deleteMerchantArticle(id: number) {
    return request.post({ url: '/merchant.article/delete', data: { id } })
}

export function getMerchantOrderLists(data: any) {
    return request.get({ url: '/merchant.order/lists', data })
}

export function getMerchantOrderStatistics() {
    return request.get({ url: '/merchant.order/statistics' })
}

export function getMerchantFans(data: any) {
    return request.get({ url: '/merchant.fan/fans', data })
}

export function getMerchantCustomers(data: any) {
    return request.get({ url: '/merchant.fan/customers', data })
}

export function getMerchantFanStatistics() {
    return request.get({ url: '/merchant.fan/statistics' })
}

export function getMerchantInfo() {
    return request.get({ url: '/merchant.info/get' })
}

export function setMerchantInfo(data: any) {
    return request.post({ url: '/merchant.info/set', data })
}

export function getMerchantWithdrawInfo() {
    return request.get({ url: '/merchant.withdraw/info' })
}

export function applyMerchantWithdraw(data: any) {
    return request.post({ url: '/merchant.withdraw/apply', data })
}

export function getMerchantWithdrawLists(data: any) {
    return request.get({ url: '/merchant.withdraw/lists', data })
}

export function getMerchantCouponLists() {
    return request.get({ url: '/merchant.coupon/lists' })
}

export function getMerchantCouponDetail(id: number) {
    return request.get({ url: '/merchant.coupon/detail', data: { id } })
}

export function saveMerchantCoupon(data: any) {
    return request.post({ url: '/merchant.coupon/save', data })
}

export function deleteMerchantCoupon(id: number) {
    return request.post({ url: '/merchant.coupon/del', data: { id } })
}

export function getMerchantDistributionSetting() {
    return request.get({ url: '/merchant.distribution/getSetting' })
}

export function setMerchantDistributionSetting(data: any) {
    return request.post({ url: '/merchant.distribution/setSetting', data })
}

export function getMerchantSeriesLists(data: any) {
    return request.get({ url: '/merchant.series/lists', data })
}

export function getMerchantSeriesDetail(id: number) {
    return request.get({ url: '/merchant.series/detail', data: { id } })
}

export function saveMerchantSeries(data: any) {
    return request.post({ url: '/merchant.series/save', data })
}

export function deleteMerchantSeries(id: number) {
    return request.post({ url: '/merchant.series/delete', data: { id } })
}

export function setMerchantSeriesStatus(data: any) {
    return request.post({ url: '/merchant.series/status', data })
}

export function getMerchantIssueLists(seriesId: number) {
    return request.get({ url: '/merchant.issue/lists', data: { series_id: seriesId } })
}

export function saveMerchantIssue(data: any) {
    return request.post({ url: '/merchant.issue/save', data })
}

export function deleteMerchantIssue(id: number) {
    return request.post({ url: '/merchant.issue/delete', data: { id } })
}

export function getCurrentMerchant() {
    return request.get({ url: '/merchant.follow/current' })
}

export function setCurrentMerchant(merchantId: number) {
    return request.post({ url: '/merchant.follow/setCurrent', data: { merchant_id: merchantId } })
}

export function getFollowedMerchants() {
    return request.get({ url: '/merchant.follow/followed' })
}

export function getPromotionLink() {
    return request.get({ url: '/merchant.info/getPromotionLink' })
}

export function getMerchantEmailInfo() {
    return request.get({ url: '/merchant.email/info' })
}

export function bindMerchantEmail(data: { email: string; code: string }) {
    return request.post({ url: '/merchant.email/bind', data })
}

export function updateMerchantEmailNotify(data: { email_notify: number }) {
    return request.post({ url: '/merchant.email/updateNotify', data })
}
