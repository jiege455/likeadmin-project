<!--
  开发者公众号：杰哥网络科技。
  qq2711793818 杰哥
-->
<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar :front-color="$theme.navColor" :background-color="$theme.navBgColor" />
        <!-- #endif -->
    </page-meta>
    <view class="article-detail-container">
        <view
            class="header-nav"
            :style="{
                paddingTop: 'var(--status-bar-height)',
                backgroundColor: themeStore.primaryColor
            }"
        >
            <view class="nav-content flex justify-between items-center px-[30rpx]" :style="{ height: '44px' }">
                <view
                    class="back-btn flex items-center justify-center w-[72rpx] h-[72rpx] bg-white/20 rounded-full"
                    @click="goBack"
                >
                    <u-icon name="arrow-left" color="#fff" size="40"></u-icon>
                </view>
                <view class="font-bold text-lg text-white truncate max-w-[300rpx]">{{
                    newsData.merchant_name || '资料详情'
                }}</view>
                <view
                    v-if="newsData.can_distribute"
                    class="promote-btn flex items-center bg-white/25 px-[24rpx] py-[10rpx] rounded-full"
                    @click="handlePromote"
                >
                    <text class="text-sm text-white font-medium">推广</text>
                </view>
                <view v-else class="w-[72rpx]"></view>
            </view>
        </view>

        <scroll-view scroll-y class="content-scroll" :show-scrollbar="false">
            <view class="hero-section">
                <view class="title-section">
                    <view class="title-badge">
                        <view class="badge-item badge-hot" v-if="newsData.price > 0">
                            <u-icon name="fire-fill" color="#fff" size="28"></u-icon>
                            <text class="ml-[6rpx]">付费资料</text>
                        </view>
                        <view
                            class="badge-item badge-public"
                            :style="{ backgroundColor: themeStore.primaryColor }"
                        >
                            <u-icon name="eye" color="#fff" size="28"></u-icon>
                            <text class="ml-[6rpx]">{{
                                newsData.is_public ? '公开' : '私密'
                            }}</text>
                        </view>
                        <view class="badge-item badge-series" v-if="newsData.series_id > 0">
                            <u-icon name="list" color="#fff" size="28"></u-icon>
                            <text class="ml-[6rpx]">第{{ newsData.issue_no }}期</text>
                        </view>
                    </view>
                    <view class="article-title">
                        <text>{{ newsData.title || '暂无标题' }}</text>
                    </view>
                    <view class="article-meta">
                        <view class="meta-time">
                            <u-icon name="clock" color="#999" size="28"></u-icon>
                            <text class="time-text">{{ formatTime(newsData.create_time) }}</text>
                        </view>
                        <view class="meta-tags" v-if="newsData.tag_list && newsData.tag_list.length > 0">
                            <view
                                v-for="tag in newsData.tag_list"
                                :key="tag.id"
                                class="detail-tag"
                            >
                                {{ tag.name }}
                            </view>
                        </view>
                    </view>
                </view>
            </view>

            <!-- 顶部注意事项提示 -->
            <view v-if="articleTips.top_tips_show && articleTips.top_tips" class="top-tips-section">
                <rich-text class="rich-content" :nodes="articleTips.top_tips"></rich-text>
            </view>

            <!-- 系列文章：历史内容（免费预览） -->
            <view
                v-if="newsData.series_id > 0 && newsData.prev_issue_content"
                class="content-section prev-issue-section"
            >
                <view class="content-header-center" @click="toggleHistoryContent">
                    <view class="header-line"></view>
                    <view class="header-text">
                        <text class="header-title">以下为历史内容{{ newsData.prev_issue_no ? '（第' + newsData.prev_issue_no + '期）' : '' }}</text>
                    </view>
                    <view class="header-line"></view>
                    <view class="collapse-btn">
                        <u-icon
                            :name="showHistoryContent ? 'arrow-up' : 'arrow-down'"
                            color="#666"
                            size="24"
                        ></u-icon>
                    </view>
                </view>
                <view class="prev-issue-content" v-show="showHistoryContent">
                    <rich-text
                        class="rich-content"
                        :nodes="newsData.prev_issue_content"
                    ></rich-text>
                </view>
            </view>

            <!-- 当前期内容 -->
            <view class="content-section">
                <view class="content-header-center" @click="toggleContentExpand">
                    <view class="header-line"></view>
                    <view class="header-text">
                        <text class="header-title">{{
                            newsData.series_id > 0
                                ? '当前期内容（第' + newsData.issue_no + '期）'
                                : newsData.is_buy
                                ? '完整内容'
                                : '内容预览'
                        }}</text>
                    </view>
                    <view class="header-line"></view>
                    <view class="collapse-icon">
                        <u-icon
                            :name="isContentExpanded ? 'arrow-up' : 'arrow-down'"
                            color="#666"
                            size="24"
                        ></u-icon>
                    </view>
                </view>

                <view v-show="isContentExpanded">

                <view v-if="newsData.need_pay && !newsData.is_buy" class="preview-wrapper">
                    <!-- 系列文章显示不同的预览提示 -->
                    <view v-if="newsData.series_id > 0" class="preview-card">
                        <view class="preview-item">
                            <view
                                class="preview-dot"
                                :style="{ backgroundColor: themeStore.primaryColor }"
                            ></view>
                            <text class="preview-text">当前期预测内容，购买后可见</text>
                        </view>
                        <view class="preview-item">
                            <view
                                class="preview-dot"
                                :style="{ backgroundColor: themeStore.primaryColor }"
                            ></view>
                            <text class="preview-text">综合区资料，购买后可见完整内容</text>
                        </view>
                        <view class="preview-item">
                            <view
                                class="preview-dot"
                                :style="{ backgroundColor: themeStore.primaryColor }"
                            ></view>
                            <text class="preview-text">和值参考，购买后可见完整内容</text>
                        </view>
                    </view>
                    <!-- 普通文章显示原有预览 -->
                    <view v-else class="preview-card">
                        <view class="preview-item">
                            <view
                                class="preview-dot"
                                :style="{ backgroundColor: themeStore.primaryColor }"
                            ></view>
                            <text class="preview-text">综合区资料，购买后可见完整内容</text>
                        </view>
                        <view class="preview-item">
                            <view
                                class="preview-dot"
                                :style="{ backgroundColor: themeStore.primaryColor }"
                            ></view>
                            <text class="preview-text">和值参考，购买后可见完整内容</text>
                        </view>
                        <view class="preview-item">
                            <view
                                class="preview-dot"
                                :style="{ backgroundColor: themeStore.primaryColor }"
                            ></view>
                            <text class="preview-text">断组参考，购买后可见完整内容</text>
                        </view>
                    </view>
                    <view class="preview-lock">
                        <view
                            class="lock-icon-large"
                            :style="{ backgroundColor: themeStore.primaryColor + '20' }"
                        >
                            <u-icon name="lock" :color="themeStore.primaryColor" size="48"></u-icon>
                        </view>
                        <text class="lock-title">内容已加密</text>
                        <text class="lock-desc">支付即可解锁全部内容</text>
                    </view>
                </view>

                <view v-else class="content-list">
                    <!-- 系列文章直接显示内容 -->
                    <view
                        v-if="newsData.series_id > 0 && newsData.hidden_content"
                        class="full-content"
                    >
                        <rich-text
                            class="rich-content"
                            :nodes="newsData.hidden_content"
                        ></rich-text>
                    </view>
                    <!-- HTML 格式内容 -->
                    <view v-else-if="contentData.isHtml && contentData.htmlContent" class="full-content">
                        <rich-text
                            class="rich-content"
                            :nodes="contentData.htmlContent"
                        ></rich-text>
                    </view>
                    <!-- 普通文章显示结构化内容 -->
                    <template v-else>
                        <view v-if="contentData.general?.length > 0" class="list-section">
                            <view class="section-title">
                                <view
                                    class="section-dot"
                                    :style="{ backgroundColor: themeStore.primaryColor }"
                                ></view>
                                <text class="section-name">综合区</text>
                                <view class="section-count"
                                    >{{ contentData.general.length }}条</view
                                >
                            </view>
                            <view class="items-wrapper">
                                <view
                                    v-for="(item, index) in contentData.general"
                                    :key="`general-${index}`"
                                    class="item-card"
                                >
                                    <view
                                        class="item-index"
                                        :style="{ backgroundColor: themeStore.primaryColor }"
                                    >
                                        <text>{{ index + 1 }}</text>
                                    </view>
                                    <text class="item-text">{{ item }}</text>
                                </view>
                            </view>
                        </view>

                        <view v-if="contentData.sum?.length > 0" class="list-section">
                            <view class="section-title">
                                <view
                                    class="section-dot"
                                    :style="{ backgroundColor: themeStore.primaryColor }"
                                ></view>
                                <text class="section-name">和值参考</text>
                                <view class="section-count">{{ contentData.sum.length }}条</view>
                            </view>
                            <view class="items-wrapper">
                                <view
                                    v-for="(item, index) in contentData.sum"
                                    :key="`sum-${index}`"
                                    class="item-card item-blue"
                                >
                                    <view class="item-index index-blue">
                                        <text>{{ index + 1 }}</text>
                                    </view>
                                    <text class="item-text">{{ item }}</text>
                                </view>
                            </view>
                        </view>

                        <view v-if="contentData.group?.length > 0" class="list-section">
                            <view class="section-title">
                                <view
                                    class="section-dot"
                                    :style="{ backgroundColor: themeStore.primaryColor }"
                                ></view>
                                <text class="section-name">参考三断组</text>
                                <view class="section-count">{{ contentData.group.length }}条</view>
                            </view>
                            <view class="items-wrapper">
                                <view
                                    v-for="(item, index) in contentData.group"
                                    :key="`group-${index}`"
                                    class="item-card item-purple"
                                >
                                    <view class="item-index index-purple">
                                        <text>{{ index + 1 }}</text>
                                    </view>
                                    <text class="item-text">{{ item }}</text>
                                </view>
                            </view>
                        </view>
                    </template>
                </view>
                </view>
            </view>

            <!-- 底部购买须知提示 -->
            <view
                v-if="articleTips.bottom_tips_show && articleTips.bottom_tips"
                class="bottom-tips-section"
            >
                <rich-text class="rich-content" :nodes="articleTips.bottom_tips"></rich-text>
            </view>

            <view class="h-[200rpx]"></view>
        </scroll-view>

        <!-- 水印层 -->
        <view v-if="newsData.watermark && newsData.watermark.enable" class="watermark-layer">
            <view class="watermark-content" :style="{ opacity: newsData.watermark.opacity }">
                <view class="watermark-tile">
                    <view v-for="row in 12" :key="row" class="watermark-row">
                        <view v-for="col in 5" :key="col" class="watermark-item">
                            <text class="watermark-text">{{ newsData.watermark.text }}</text>
                            <text class="watermark-contact">{{ newsData.watermark.contact }}</text>
                        </view>
                    </view>
                </view>
            </view>
        </view>

        <!-- 悬浮须知按钮 -->
        <view
            v-if="articleTips.bottom_tips_show && articleTips.bottom_tips"
            class="floating-notice"
            @click="handleBuyNotice"
        >
            <u-icon name="info-circle" color="#fff" size="28"></u-icon>
            <text class="notice-text">须知</text>
        </view>

        <!-- 底部购买栏（仅付费文章显示） -->
        <view v-if="newsData.need_pay" class="bottom-bar">
            <view class="bottom-price-section">
                <text class="bottom-price-label">价格</text>
                <view class="bottom-price-value">
                    <text class="bottom-price-symbol">¥</text>
                    <text class="bottom-price-number">{{ newsData.price || '0.00' }}</text>
                </view>
            </view>
            <button
                v-if="!newsData.is_buy"
                class="buy-btn"
                :style="{ backgroundColor: themeStore.primaryColor }"
                @click="handleBuy"
            >
                立即购买
            </button>
            <button v-else class="bought-btn">
                <u-icon name="checkmark-circle" color="#52C41A" size="28"></u-icon>
                <text>已购买</text>
            </button>
        </view>

        <u-popup
            :show="showNoticePopup"
            mode="center"
            round="20"
            :closeable="true"
            @close="showNoticePopup = false"
        >
            <view class="notice-popup">
                <view class="notice-header">
                    <view class="notice-icon">
                        <u-icon name="info-circle-fill" color="#F97316" size="48"></u-icon>
                    </view>
                    <text class="notice-title">购买须知</text>
                </view>
                <view class="notice-content">
                    <rich-text
                        class="rich-content"
                        :nodes="articleTips.bottom_tips || '暂无购买须知'"
                    ></rich-text>
                </view>
                <view
                    class="notice-btn"
                    :style="{ backgroundColor: themeStore.primaryColor }"
                    @click="showNoticePopup = false"
                >
                    <text class="notice-btn-text">我已阅读并同意</text>
                </view>
            </view>
        </u-popup>

        <u-popup
            :show="showPaymentPopup"
            mode="bottom"
            :closeable="true"
            @close="showPaymentPopup = false"
            :border-radius="32"
        >
            <view class="payment-popup">
                <view class="payment-header">
                    <view class="payment-drag-line"></view>
                    <text class="payment-title">选择支付方式</text>
                </view>

                <view class="payment-price-section">
                    <view class="price-card" :style="{ backgroundColor: themeStore.primaryColor }">
                        <text class="price-label">支付金额</text>
                        <view class="price-value-wrapper">
                            <text class="price-symbol">¥</text>
                            <text class="price-value">{{ finalPrice }}</text>
                        </view>
                        <view v-if="selectedCoupon" class="coupon-discount">
                            <text class="discount-text">已优惠 ¥{{ selectedCoupon.money }}</text>
                        </view>
                        <view class="price-divider"></view>
                        <text class="price-tip">安全支付，保障您的权益</text>
                    </view>
                </view>

                <!-- 优惠券选择 -->
                <view v-if="availableCoupons.length > 0" class="coupon-section">
                    <view class="coupon-header" @click="showCouponPopup = true">
                        <view class="coupon-left">
                            <u-icon
                                name="coupon"
                                :color="themeStore.primaryColor"
                                size="36"
                            ></u-icon>
                            <text class="coupon-title">优惠券</text>
                        </view>
                        <view class="coupon-right">
                            <text v-if="selectedCoupon" class="coupon-selected"
                                >-¥{{ selectedCoupon.money }}</text
                            >
                            <text v-else class="coupon-placeholder"
                                >{{ availableCoupons.length }}张可用</text
                            >
                            <u-icon name="arrow-right" color="#999" size="28"></u-icon>
                        </view>
                    </view>
                </view>

                <view class="payment-list">
                    <view
                        v-for="(item, index) in payWays"
                        :key="index"
                        class="payment-item"
                        :class="{
                            'payment-item-active': selectedPayWay === item.pay_way,
                            'payment-item-disabled': item.pay_way == 1 && userBalance < finalPrice
                        }"
                        :style="
                            selectedPayWay === item.pay_way
                                ? {
                                      borderColor: themeStore.primaryColor,
                                      backgroundColor: themeStore.primaryColor + '10'
                                  }
                                : {}
                        "
                        @click="selectPayWay(item.pay_way, item.user_money)"
                    >
                        <view class="payment-left">
                            <view class="payment-icon" :class="'pay-icon-' + item.pay_way">
                                <text v-if="item.pay_way == 1" class="pay-icon-emoji">💰</text>
                                <text v-else-if="item.pay_way == 2" class="pay-icon-emoji">💚</text>
                                <text v-else class="pay-icon-emoji">💙</text>
                            </view>
                            <view class="payment-info">
                                <text class="payment-name">{{ item.name }}</text>
                                <text v-if="item.extra" class="payment-extra">{{
                                    item.extra
                                }}</text>
                                <text
                                    v-if="item.pay_way == 1 && userBalance < finalPrice"
                                    class="payment-insufficient"
                                >
                                    余额不足
                                </text>
                            </view>
                        </view>
                        <view class="payment-check">
                            <view
                                class="check-circle"
                                :class="{ 'check-circle-active': selectedPayWay === item.pay_way }"
                                :style="
                                    selectedPayWay === item.pay_way
                                        ? { backgroundColor: themeStore.primaryColor }
                                        : {}
                                "
                            >
                                <u-icon
                                    v-if="selectedPayWay === item.pay_way"
                                    name="checkmark"
                                    color="#fff"
                                    size="28"
                                ></u-icon>
                            </view>
                        </view>
                    </view>
                </view>

                <view class="payment-footer">
                    <view class="security-tip">
                        <u-icon name="shield-checkmark" color="#07c160" size="28"></u-icon>
                        <text class="security-text">由支付平台提供安全保障</text>
                    </view>
                    <button
                        class="confirm-pay-btn"
                        :style="{ backgroundColor: themeStore.primaryColor }"
                        @click="confirmPayment"
                    >
                        <text class="confirm-pay-text">确认支付</text>
                        <text class="confirm-pay-price">¥{{ finalPrice }}</text>
                    </button>
                </view>
            </view>
        </u-popup>

        <!-- 优惠券选择弹窗 -->
        <u-popup
            :show="showCouponPopup"
            mode="bottom"
            :closeable="true"
            @close="showCouponPopup = false"
            :border-radius="32"
        >
            <view class="coupon-popup">
                <view class="coupon-popup-header">
                    <text class="coupon-popup-title">选择优惠券</text>
                </view>
                <scroll-view scroll-y class="coupon-list">
                    <view
                        v-for="(item, index) in availableCoupons"
                        :key="index"
                        class="coupon-item"
                        :class="{ 'coupon-item-selected': selectedCoupon?.id === item.id }"
                        @click="selectCoupon(item)"
                    >
                        <view class="coupon-item-left">
                            <text class="coupon-money">¥{{ item.money }}</text>
                            <text class="coupon-condition">满{{ item.condition_money }}可用</text>
                        </view>
                        <view class="coupon-item-right">
                            <text class="coupon-name">{{ item.name }}</text>
                            <text class="coupon-time">{{ item.validity_text }}</text>
                        </view>
                        <view v-if="selectedCoupon?.id === item.id" class="coupon-check">
                            <u-icon
                                name="checkmark-circle-fill"
                                :color="themeStore.primaryColor"
                                size="40"
                            ></u-icon>
                        </view>
                    </view>
                    <view v-if="availableCoupons.length === 0" class="coupon-empty">
                        <text>暂无可用优惠券</text>
                    </view>
                </scroll-view>
                <view class="coupon-popup-footer">
                    <view class="not-use-coupon" @click="clearCoupon">
                        <text>不使用优惠券</text>
                    </view>
                </view>
            </view>
        </u-popup>
    </view>
</template>

<script lang="ts" setup>
import { ref, computed } from 'vue'
import { onLoad, onShareAppMessage, onShareTimeline } from '@dcloudio/uni-app'
import { getArticleDetail, addCollect, cancelCollect, buyArticle, getArticleTips } from '@/api/news'
import { getPayWay, prepay, getPayResult } from '@/api/pay'
import { getAvailableCouponList } from '@/api/coupon'
import { getApplyDetail } from '@/api/distribution'
import { useUserStore } from '@/stores/user'
import { useThemeStore } from '@/stores/theme'
import { pay, PayWayEnum, PayStatusEnum } from '@/utils/pay'
import { client } from '@/utils/client'
import { ClientEnum } from '@/enums/appEnums'
import { safeNavigateBack } from '@/utils/util'

const userStore = useUserStore()
const themeStore = useThemeStore()

const newsData = ref<any>({})
const showNoticePopup = ref(false)
const showPaymentPopup = ref(false)
const showHistoryContent = ref(true)
const isContentExpanded = ref(true)
const showCouponPopup = ref(false)
const payWays = ref<any[]>([])
const currentOrderId = ref<number>(0)
const selectedPayWay = ref<number>(0)
const userBalance = ref<number>(0)
const availableCoupons = ref<any[]>([])
const selectedCoupon = ref<any>(null)
const articleTips = ref({
    top_tips: '',
    top_tips_show: 0,
    bottom_tips: '',
    bottom_tips_show: 0
})
let newsId = ''
let inviteCode = ''
let merchantId = ''

const contentData = ref({
    general: [] as string[],
    sum: [] as string[],
    group: [] as string[],
    isHtml: false,
    htmlContent: ''
})

const formatTime = (time: string) => {
    if (!time) return '2025-01-01 00:00'
    const date = new Date(time)
    if (isNaN(date.getTime())) return '2025-01-01 00:00'
    const month = String(date.getMonth() + 1).padStart(2, '0')
    const day = String(date.getDate()).padStart(2, '0')
    const hours = String(date.getHours()).padStart(2, '0')
    const minutes = String(date.getMinutes()).padStart(2, '0')
    return `${date.getFullYear()}-${month}-${day} ${hours}:${minutes}`
}

const finalPrice = computed(() => {
    const price = parseFloat(newsData.value.price) || 0
    const couponMoney = selectedCoupon.value ? parseFloat(selectedCoupon.value.money) : 0
    return Math.max(0, price - couponMoney).toFixed(2)
})

const parseHiddenContent = (content: string) => {
    if (!content) return
    
    // 检测是否为 HTML 格式（包含 HTML 标签）
    const isHtmlContent = /<[a-zA-Z][^>]*>/.test(content)
    
    if (isHtmlContent) {
        // HTML 格式内容，直接使用 rich-text 显示
        contentData.value = {
            general: [],
            sum: [],
            group: [],
            isHtml: true,
            htmlContent: content
        }
        return
    }
    
    // 尝试解析 JSON 格式
    try {
        const parsed = JSON.parse(content)
        contentData.value = {
            general: parsed.general || [],
            sum: parsed.sum || [],
            group: parsed.group || [],
            isHtml: false,
            htmlContent: ''
        }
    } catch (e) {
        // 纯文本格式，按行分割显示
        const lines = content.split('\n').filter((line: string) => line.trim())
        contentData.value = {
            general: lines,
            sum: [],
            group: [],
            isHtml: false,
            htmlContent: ''
        }
    }
}

const getData = async (id) => {
    const params: any = { id }
    if (inviteCode) {
        params.invite_code = inviteCode
    }
    if (merchantId) {
        params.merchant_id = merchantId
    }
    newsData.value = await getArticleDetail(params)
    // 优先使用 content 字段，如果没有再使用 hidden_content
    const displayContent = newsData.value.content || newsData.value.hidden_content
    if (displayContent) {
        parseHiddenContent(displayContent)
    }
}

const goBack = () => {
    safeNavigateBack({ defaultUrl: '/pages/index/index' })
}

const toggleHistoryContent = () => {
    showHistoryContent.value = !showHistoryContent.value
}

const toggleContentExpand = () => {
    isContentExpanded.value = !isContentExpanded.value
}

const handleBuy = async () => {
    if (!userStore.isLogin) {
        uni.showToast({ title: '请先登录', icon: 'none' })
        setTimeout(() => {
            uni.navigateTo({ url: '/pages/login/login' })
        }, 1500)
        return
    }

    try {
        uni.showLoading({ title: '创建订单中...' })
        const orderRes = await buyArticle({ id: newsData.value.id })
        uni.hideLoading()

        if (orderRes && orderRes.order_id) {
            await openPaymentPopup(orderRes.order_id)
        } else {
            uni.showToast({ title: '购买成功', icon: 'success' })
            getData(newsId)
        }
    } catch (e: any) {
        uni.hideLoading()
        if (e.msg || e.message) {
            uni.showToast({ title: e.msg || e.message, icon: 'none' })
        }
    }
}

const openPaymentPopup = async (orderId: number) => {
    try {
        uni.showLoading({ title: '加载支付方式...' })
        const payData = await getPayWay({
            order_id: orderId,
            from: 'article'
        })
        uni.hideLoading()

        payWays.value = payData.lists || []
        if (payWays.value.length === 0) {
            uni.showToast({ title: '暂无可用支付方式', icon: 'none' })
            return
        }

        currentOrderId.value = orderId

        const balanceItem = payWays.value.find((item) => item.pay_way === 1)
        if (balanceItem) {
            const extra = balanceItem.extra || ''
            const match = extra.match(/可用余额:?([\d.]+)/)
            if (match) {
                userBalance.value = parseFloat(match[1]) || 0
            }
        }

        const defaultPayWay = payWays.value.find((item) => item.is_default)
        if (defaultPayWay) {
            if (defaultPayWay.pay_way === 1 && userBalance.value < newsData.value.price) {
                const otherPayWay = payWays.value.find((item) => item.pay_way !== 1)
                selectedPayWay.value = otherPayWay ? otherPayWay.pay_way : payWays.value[0].pay_way
            } else {
                selectedPayWay.value = defaultPayWay.pay_way
            }
        } else {
            const availablePayWay = payWays.value.find(
                (item) => !(item.pay_way === 1 && userBalance.value < newsData.value.price)
            )
            selectedPayWay.value = availablePayWay
                ? availablePayWay.pay_way
                : payWays.value[0].pay_way
        }

        // 加载可用优惠券
        await loadAvailableCoupons()

        showPaymentPopup.value = true
    } catch (e: any) {
        uni.hideLoading()
        if (e.msg || e.message) {
            uni.showToast({ title: e.msg || e.message, icon: 'none' })
        } else {
            uni.showToast({ title: '加载支付方式失败', icon: 'none' })
        }
    }
}

const loadAvailableCoupons = async () => {
    try {
        const res = await getAvailableCouponList({
            article_id: newsData.value.id,
            merchant_id: newsData.value.merchant_id,
            amount: parseFloat(newsData.value.price) || 0
        })
        availableCoupons.value = res || []
    } catch (e) {
        availableCoupons.value = []
    }
}

const selectCoupon = (coupon: any) => {
    if (selectedCoupon.value?.id === coupon.id) {
        selectedCoupon.value = null
    } else {
        selectedCoupon.value = coupon
    }
    showCouponPopup.value = false
}

const clearCoupon = () => {
    selectedCoupon.value = null
    showCouponPopup.value = false
}

const selectPayWay = (payWay: number, userMoney?: number) => {
    if (payWay === 1 && userBalance.value < parseFloat(finalPrice.value)) {
        uni.showToast({ title: '余额不足，请选择其他支付方式', icon: 'none' })
        return
    }
    selectedPayWay.value = payWay
}

const confirmPayment = async () => {
    if (selectedPayWay.value === 0) {
        uni.showToast({ title: '请选择支付方式', icon: 'none' })
        return
    }
    if (selectedPayWay.value === 1 && userBalance.value < parseFloat(finalPrice.value)) {
        uni.showToast({ title: '余额不足，请选择其他支付方式', icon: 'none' })
        return
    }
    showPaymentPopup.value = false
    await doPayment(currentOrderId.value, selectedPayWay.value)
}

const doPayment = async (orderId: number, payWay: number) => {
    try {
        if (
            userStore.userInfo.is_auth == 0 &&
            [ClientEnum.OA_WEIXIN, ClientEnum.MP_WEIXIN].includes(client) &&
            payWay == PayWayEnum.WECHAT
        ) {
            const res: any = await uni.showModal({
                title: '温馨提示',
                content: '当前账号未绑定微信，无法完成支付',
                confirmText: '去绑定'
            })
            if (res.confirm) {
                uni.navigateTo({ url: '/pages/user_set/user_set' })
            }
            return
        }

        uni.showLoading({ title: '支付中...' })
        const prepayParams: any = {
            order_id: orderId,
            from: 'article',
            pay_way: payWay,
            redirect: `/pages/news_detail/news_detail?id=${newsId}`
        }
        if (selectedCoupon.value) {
            prepayParams.coupon_id = selectedCoupon.value.id
        }
        const prepayData = await prepay(prepayParams)

        if (prepayData && prepayData.config) {
            uni.hideLoading()
            await pay.payment(prepayData.pay_way, prepayData.config)
            handlePayResult(PayStatusEnum.SUCCESS)
        } else {
            uni.hideLoading()
            handlePayResult(PayStatusEnum.SUCCESS)
        }
    } catch (e: any) {
        uni.hideLoading()
        if (e.msg || e.message) {
            uni.showToast({ title: e.msg || e.message, icon: 'none' })
        }
        handlePayResult(PayStatusEnum.FAIL)
    }
}

const handlePayResult = (status: PayStatusEnum) => {
    if (status === PayStatusEnum.SUCCESS) {
        uni.showToast({ title: '支付成功', icon: 'success' })
        userStore.getUser()
        setTimeout(() => {
            getData(newsId)
        }, 1500)
    }
}

const handleCollect = async () => {
    if (!userStore.isLogin) {
        uni.showToast({ title: '请先登录', icon: 'none' })
        setTimeout(() => {
            uni.navigateTo({ url: '/pages/login/login' })
        }, 1500)
        return
    }

    try {
        if (newsData.value.collect) {
            await cancelCollect({ id: newsData.value.id })
            uni.$u.toast('已取消收藏')
        } else {
            await addCollect({ id: newsData.value.id })
            uni.$u.toast('收藏成功')
        }
        getData(newsId)
    } catch (e) {}
}

const handlePromote = async () => {
    if (!userStore.isLogin) {
        uni.showToast({ title: '请先登录', icon: 'none' })
        setTimeout(() => {
            uni.navigateTo({ url: '/pages/login/login' })
        }, 1500)
        return
    }

    try {
        const distributorStatus = await getApplyDetail()
        if (!distributorStatus.is_distributor) {
            uni.showModal({
                title: '提示',
                content: '您还不是分销员，是否立即申请成为分销员？',
                confirmText: '去申请',
                cancelText: '取消',
                success: (res) => {
                    if (res.confirm) {
                        uni.navigateTo({ url: '/pages/business/promotion-apply' })
                    }
                }
            })
            return
        }

        uni.navigateTo({
            url: `/pages/distribution/article_poster?article_id=${newsId}&article_title=${encodeURIComponent(
                newsData.value.title || ''
            )}`
        })
    } catch (e: any) {
        uni.showToast({ title: e.msg || '获取分销员状态失败', icon: 'none' })
    }
}

const copyPromoteLink = () => {
    const inviteCode = userStore.userInfo.sn || ''
    let link = ''

    // #ifdef H5
    link = `${window.location.origin}/mobile/pages/news_detail/news_detail?id=${newsId}&invite_code=${inviteCode}`
    // #endif

    // #ifndef H5
    link = `/pages/news_detail/news_detail?id=${newsId}&invite_code=${inviteCode}`
    // #endif

    uni.setClipboardData({
        data: link,
        success: () => {
            uni.showToast({ title: '推广链接已复制', icon: 'success' })
        }
    })
}

const handleBuyNotice = () => {
    showNoticePopup.value = true
}

const handleComplaint = () => {
    if (!userStore.isLogin) {
        uni.showToast({ title: '请先登录', icon: 'none' })
        setTimeout(() => {
            uni.navigateTo({ url: '/pages/login/login' })
        }, 1500)
        return
    }

    uni.navigateTo({
        url: `/pages/business/complaint?type=2&target_id=${newsId}&title=${encodeURIComponent(
            newsData.value.title || '文章'
        )}`
    })
}

const scrollToContent = () => {
    uni.pageScrollTo({
        scrollTop: 300,
        duration: 300
    })
}

onLoad((options: any) => {
    newsId = options?.id || ''
    inviteCode = options?.invite_code || ''
    merchantId = options?.merchant_id || ''

    if (!newsId) {
        uni.showToast({ title: '文章ID不存在', icon: 'none' })
        setTimeout(() => {
            uni.navigateBack()
        }, 1500)
        return
    }

    // 保存邀请码到本地存储，用于后续注册时绑定推广关系
    if (inviteCode) {
        uni.setStorageSync('invite_code', inviteCode)
    }
    if (merchantId) {
        uni.setStorageSync('merchant_id', merchantId)
    }

    // 如果用户未登录且有邀请码，提示去注册
    if (!userStore.isLogin && inviteCode) {
        setTimeout(() => {
            uni.showModal({
                title: '提示',
                content: '您还未登录，是否立即注册/登录以建立推广关系？',
                confirmText: '去登录',
                cancelText: '稍后再说',
                success: (res) => {
                    if (res.confirm) {
                        uni.navigateTo({ url: '/pages/login/login' })
                    }
                }
            })
        }, 1500)
    }

    getData(newsId)
    getArticleTips()
        .then((res) => {
            articleTips.value = res
        })
        .catch(() => {})
})

onShareAppMessage(() => {
    const inviteCode = userStore.userInfo.sn || ''
    return {
        title: newsData.value.title,
        path: `/pages/news_detail/news_detail?id=${newsId}&invite_code=${inviteCode}`,
        imageUrl: newsData.value.image
    }
})

onShareTimeline(() => {
    const inviteCode = userStore.userInfo.sn || ''
    return {
        title: newsData.value.title,
        query: `id=${newsId}&invite_code=${inviteCode}`,
        imageUrl: newsData.value.image
    }
})
</script>

<style lang="scss">
.article-detail-container {
    height: 100vh;
    display: flex;
    flex-direction: column;
    background-color: #f5f7fa;
}

.header-nav {
    z-index: 100;
    flex-shrink: 0;
}

.nav-content {
    height: 44px;
}

.back-btn,
.promote-btn {
    transition: all 0.3s;
}

.back-btn:active,
.promote-btn:active {
    transform: scale(0.95);
    opacity: 0.8;
}

.content-scroll {
    flex: 1;
    overflow-y: auto;
}

.hero-section {
    background: #fff;
    margin-bottom: 12rpx;
}

.top-tips-section {
    margin: 0 20rpx 12rpx;
    padding: 16rpx 20rpx;
    background: #fff8f0;
    border-radius: 12rpx;
}

.top-tips-section .rich-content {
    font-size: 26rpx;
    color: #666;
    line-height: 1.8;
}

.bottom-tips-section {
    margin: 0 20rpx 12rpx;
    padding: 16rpx 20rpx;
    background: #f8f9fa;
    border-radius: 12rpx;
}

.bottom-tips-section .rich-content {
    font-size: 26rpx;
    color: #666;
    line-height: 1.8;
}

.title-section {
    padding: 30rpx;
}

.title-badge {
    display: flex;
    gap: 16rpx;
    margin-bottom: 24rpx;
    flex-wrap: wrap;
}

.badge-item {
    display: flex;
    align-items: center;
    padding: 8rpx 20rpx;
    border-radius: 100rpx;
    font-size: 24rpx;
    font-weight: 600;
}

.badge-hot {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: #fff;
}

.badge-series {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: #fff;
}

.article-title {
    font-size: 40rpx;
    font-weight: 700;
    color: #1a1a2e;
    line-height: 1.5;
    margin-bottom: 24rpx;
}

.article-meta {
    display: flex;
    align-items: center;
}

.meta-time {
    display: flex;
    align-items: center;
    gap: 8rpx;
}

.time-text {
    font-size: 24rpx;
    color: #999;
}

.meta-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8rpx;
    margin-top: 12rpx;
}

.detail-tag {
    font-size: 20rpx;
    color: #4073fa;
    background: #eef4ff;
    padding: 4rpx 12rpx;
    border-radius: 4rpx;
}

.meta-divider {
    width: 1px;
    height: 32rpx;
    background: #e5e5e5;
    margin: 0 24rpx;
}

.meta-stat {
    display: flex;
    align-items: center;
}

.stat-text {
    font-size: 24rpx;
    color: #999;
    margin-left: 8rpx;
}

.price-section {
    background: #fff;
    margin: 0 20rpx 20rpx;
    border-radius: 24rpx;
    padding: 30rpx;
    box-shadow: 0 4rpx 20rpx rgba(0, 0, 0, 0.08);
    overflow: hidden;
    position: relative;
}

.price-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 6rpx;
    background: v-bind('themeStore.primaryColor');
}

.price-section.price-bought::before {
    background: linear-gradient(90deg, #11998e 0%, #38ef7d 100%);
}

.price-main {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.price-left {
    display: flex;
    flex-direction: column;
}

.price-label {
    font-size: 24rpx;
    color: #666;
    margin-bottom: 8rpx;
}

.price-value {
    display: flex;
    align-items: baseline;
}

.price-symbol {
    font-size: 32rpx;
    font-weight: 700;
    color: #f5576c;
}

.price-number {
    font-size: 56rpx;
    font-weight: 800;
    color: #f5576c;
    line-height: 1;
}

.price-original {
    font-size: 28rpx;
    color: #999;
    text-decoration: line-through;
    margin-left: 16rpx;
}

.price-tag {
    padding: 8rpx 20rpx;
    border-radius: 100rpx;
}

.tag-text {
    font-size: 24rpx;
    color: #fff;
    font-weight: 600;
}

.commission-info {
    display: flex;
    align-items: center;
    margin-top: 20rpx;
    padding-top: 20rpx;
    border-top: 1rpx solid #f0f0f0;
}

.commission-text {
    font-size: 26rpx;
    color: #f59e0b;
    margin-left: 12rpx;
    font-weight: 600;
}

.content-section {
    background: #fff;
    margin: 0 20rpx 20rpx;
    border-radius: 24rpx;
    padding: 30rpx;
}

.prev-issue-section {
    border: none;
    background: transparent;
    padding: 20rpx 30rpx;
    margin: 0 20rpx 20rpx;
}

.content-header {
    display: flex;
    align-items: center;
    margin-bottom: 30rpx;
    padding-bottom: 24rpx;
    border-bottom: 1rpx solid #f0f0f0;
}

.content-header-center {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 24rpx;
    padding: 16rpx 0;
}

.header-line {
    flex: 1;
    height: 1rpx;
    background: linear-gradient(90deg, transparent, #e0e0e0, #e0e0e0, transparent);
}

.header-text {
    padding: 0 24rpx;
}

.header-text .header-title {
    font-size: 28rpx;
    font-weight: 600;
    color: #666;
    white-space: nowrap;
}

.collapse-icon {
    margin-left: 8rpx;
}

.header-icon {
    width: 48rpx;
    height: 48rpx;
    border-radius: 12rpx;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 16rpx;
}

.header-info {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.header-title {
    font-size: 32rpx;
    font-weight: 700;
    color: #1a1a2e;
}

.header-sub {
    font-size: 24rpx;
    color: #999;
    margin-top: 4rpx;
}

.free-badge {
    background: #52c41a;
    color: #fff;
    font-size: 24rpx;
    padding: 6rpx 16rpx;
    border-radius: 100rpx;
    font-weight: 600;
}

.collapse-btn {
    display: flex;
    align-items: center;
    padding: 8rpx 16rpx;
    background: #f5f5f5;
    border-radius: 100rpx;
    margin-left: 16rpx;
}

.collapse-text {
    font-size: 24rpx;
    color: #666;
    margin-left: 8rpx;
}

.prev-issue-content {
    background: #fff;
    border-radius: 16rpx;
    padding: 24rpx;
}

.full-content {
    background: #f8f9fa;
    border-radius: 16rpx;
    padding: 24rpx;
}

.content-text {
    font-size: 28rpx;
    color: #333;
    line-height: 1.8;
    white-space: pre-wrap;
    word-break: break-all;
}

.preview-wrapper {
    position: relative;
}

.preview-card {
    padding: 30rpx;
    background: linear-gradient(135deg, #f5f7fa 0%, #e8e8e8 100%);
    border-radius: 20rpx;
}

.preview-item {
    display: flex;
    align-items: center;
    margin-bottom: 24rpx;
}

.preview-item:last-child {
    margin-bottom: 0;
}

.preview-dot {
    width: 12rpx;
    height: 12rpx;
    border-radius: 50%;
    margin-right: 16rpx;
    flex-shrink: 0;
}

.preview-text {
    font-size: 28rpx;
    color: #666;
}

.preview-lock {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.95);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-radius: 20rpx;
}

.lock-icon-large {
    width: 120rpx;
    height: 120rpx;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20rpx;
}

.lock-title {
    font-size: 32rpx;
    font-weight: 700;
    color: #1a1a2e;
    margin-bottom: 8rpx;
}

.lock-desc {
    font-size: 26rpx;
    color: #999;
}

.list-section {
    margin-bottom: 40rpx;
}

.list-section:last-child {
    margin-bottom: 0;
}

.section-title {
    display: flex;
    align-items: center;
    margin-bottom: 24rpx;
}

.section-dot {
    width: 8rpx;
    height: 32rpx;
    border-radius: 100rpx;
    margin-right: 16rpx;
}

.section-name {
    font-size: 30rpx;
    font-weight: 700;
    color: #1a1a2e;
    flex: 1;
}

.section-count {
    font-size: 24rpx;
    color: #999;
    background: #f0f0f0;
    padding: 6rpx 16rpx;
    border-radius: 100rpx;
}

.items-wrapper {
    display: flex;
    flex-direction: column;
    gap: 20rpx;
}

.item-card {
    background: #f8f9fa;
    border-radius: 16rpx;
    padding: 24rpx;
    display: flex;
    align-items: flex-start;
    border: 1rpx solid #e8e8e8;
}

.item-card.item-blue {
    background: #e0f7fa;
    border-color: #b2ebf2;
}

.item-card.item-purple {
    background: #f3e5f5;
    border-color: #e1bee7;
}

.item-index {
    width: 40rpx;
    height: 40rpx;
    border-radius: 50%;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24rpx;
    font-weight: 700;
    margin-right: 16rpx;
    flex-shrink: 0;
}

.item-index.index-blue {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.item-index.index-purple {
    background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%);
}

.item-text {
    font-size: 28rpx;
    color: #333;
    line-height: 1.6;
    flex: 1;
    word-break: break-all;
}

.floating-notice {
    position: fixed;
    left: 30rpx;
    bottom: calc(180rpx + env(safe-area-inset-bottom));
    display: flex;
    align-items: center;
    gap: 8rpx;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 16rpx 24rpx;
    border-radius: 40rpx;
    box-shadow: 0 8rpx 24rpx rgba(102, 126, 234, 0.4);
    z-index: 99;
}

.floating-notice:active {
    transform: scale(0.95);
    opacity: 0.9;
}

.floating-notice .notice-text {
    color: #fff;
    font-size: 24rpx;
    font-weight: 500;
}

.bottom-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #fff;
    border-top: 1rpx solid #f0f0f0;
    padding: 16rpx 24rpx;
    padding-bottom: calc(16rpx + env(safe-area-inset-bottom));
    display: flex;
    align-items: center;
    justify-content: space-between;
    z-index: 100;
}

.bottom-left {
    display: flex;
    align-items: center;
    gap: 28rpx;
}

.bottom-center {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    padding: 0 24rpx;
}

.price-section {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    padding-left: 10rpx;
}

.bottom-price-section {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    padding-left: 10rpx;
}

.bottom-price-label {
    font-size: 20rpx;
    color: #9ca3af;
    font-weight: 500;
    margin-bottom: 2rpx;
}

.bottom-price-value {
    display: flex;
    align-items: baseline;
}

.bottom-price-symbol {
    font-size: 28rpx;
    font-weight: 700;
    color: #f5576c;
}

.bottom-price-number {
    font-size: 44rpx;
    font-weight: 800;
    color: #f5576c;
    line-height: 1;
}

.price-label {
    font-size: 20rpx;
    color: #9ca3af;
    font-weight: 500;
    margin-bottom: 2rpx;
}

.price-value {
    display: flex;
    align-items: baseline;
}

.price-symbol {
    font-size: 24rpx;
    color: #f43f5e;
    font-weight: 700;
}

.price-number {
    font-size: 40rpx;
    color: #f43f5e;
    font-weight: 900;
    line-height: 1;
}

.bottom-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.bottom-btn:active {
    transform: scale(0.92);
}

.btn-icon {
    width: 76rpx;
    height: 76rpx;
    border-radius: 20rpx;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4rpx 12rpx rgba(0, 0, 0, 0.06), inset 0 1rpx 0 rgba(255, 255, 255, 0.8);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1rpx solid #e2e8f0;
}

.btn-icon-collect {
    background: linear-gradient(135deg, #fef3c7 0%, #fbbf24 100%);
    box-shadow: 0 4rpx 16rpx rgba(251, 191, 36, 0.3), inset 0 1rpx 0 rgba(255, 255, 255, 0.4);
    border: 1rpx solid #f59e0b;
}

.btn-text {
    font-size: 22rpx;
    color: #64748b;
    margin-top: 8rpx;
    font-weight: 600;
}

.bottom-right {
    flex: 1;
    display: flex;
    justify-content: flex-end;
}

.buy-btn,
.bought-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8rpx;
    color: #fff;
    border-radius: 40rpx;
    padding: 20rpx 40rpx;
    font-size: 28rpx;
    font-weight: 600;
    border: none;
    line-height: normal;
    min-width: 180rpx;
}

.buy-btn {
    background: #f43f5e;
    box-shadow: 0 4rpx 16rpx rgba(244, 63, 94, 0.3);
}

.buy-btn:active {
    transform: scale(0.96);
    opacity: 0.9;
}

.bought-btn {
    background: #f0fdf4;
    color: #16a34a;
    border: 1rpx solid #bbf7d0;
}

@keyframes shine {
    0% {
        left: -100%;
    }
    20%,
    100% {
        left: 100%;
    }
}

.bought-btn {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    color: #16a34a;
    border: 2rpx solid #bbf7d0;
    box-shadow: 0 4rpx 12rpx rgba(22, 163, 74, 0.1);
}

.buy-btn::after,
.share-btn::after {
    border: none;
}

.buy-btn:active,
.share-btn:active,
.view-btn:active {
    transform: scale(0.94);
    box-shadow: 0 4rpx 16rpx rgba(0, 0, 0, 0.12);
}

.notice-popup {
    width: 600rpx;
    background: #fff;
    border-radius: 24rpx;
    padding: 40rpx;
}

.notice-header {
    text-align: center;
    margin-bottom: 36rpx;
}

.notice-icon {
    width: 100rpx;
    height: 100rpx;
    border-radius: 50%;
    background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20rpx;
}

.notice-title {
    font-size: 36rpx;
    font-weight: 700;
    color: #1a1a2e;
}

.notice-content {
    max-height: 400rpx;
    overflow-y: auto;
    margin-bottom: 40rpx;
    padding: 20rpx;
    background: #f8f9fa;
    border-radius: 16rpx;
}

.notice-list {
    display: flex;
    flex-direction: column;
    gap: 24rpx;
    margin-bottom: 40rpx;
}

.notice-item {
    display: flex;
    align-items: flex-start;
}

.notice-num {
    width: 48rpx;
    height: 48rpx;
    border-radius: 50%;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24rpx;
    font-weight: 700;
    flex-shrink: 0;
    margin-right: 16rpx;
}

.notice-text {
    font-size: 28rpx;
    color: #666;
    line-height: 1.6;
    flex: 1;
}

.notice-btn {
    border-radius: 100rpx;
    padding: 28rpx;
    text-align: center;
}

.notice-btn-text {
    font-size: 30rpx;
    font-weight: 700;
    color: #fff;
}

.payment-popup {
    background: linear-gradient(180deg, #fafbfc 0%, #ffffff 100%);
    padding: 0 0 40rpx;
    border-radius: 40rpx 40rpx 0 0;
    box-shadow: 0 -8rpx 40rpx rgba(0, 0, 0, 0.12);
}

.payment-header {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 32rpx 30rpx 24rpx;
    position: relative;
}

.payment-drag-line {
    width: 100rpx;
    height: 10rpx;
    background: linear-gradient(90deg, #e0e0e0 0%, #d0d0d0 50%, #e0e0e0 100%);
    border-radius: 10rpx;
    margin-bottom: 24rpx;
}

.payment-title {
    font-size: 36rpx;
    font-weight: 800;
    color: #1a1a2e;
    letter-spacing: 0.5rpx;
}

.payment-price-section {
    padding: 0 30rpx 36rpx;
}

.price-card {
    border-radius: 32rpx;
    padding: 40rpx 30rpx;
    text-align: center;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8rpx 32rpx rgba(0, 0, 0, 0.15);
}

.price-label {
    font-size: 28rpx;
    color: rgba(255, 255, 255, 0.9);
    display: block;
    margin-bottom: 16rpx;
    font-weight: 500;
}

.price-value-wrapper {
    display: flex;
    align-items: baseline;
    justify-content: center;
    margin-bottom: 24rpx;
}

.price-symbol {
    font-size: 40rpx;
    font-weight: 700;
    color: #fff;
    margin-right: 10rpx;
}

.price-value {
    font-size: 80rpx;
    font-weight: 900;
    color: #fff;
    line-height: 1;
    text-shadow: 0 4rpx 12rpx rgba(0, 0, 0, 0.2);
}

.price-divider {
    width: 80rpx;
    height: 3rpx;
    background: rgba(255, 255, 255, 0.4);
    margin: 0 auto 20rpx;
    border-radius: 3rpx;
}

.price-tip {
    font-size: 26rpx;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 500;
}

.payment-list {
    padding: 0 30rpx 24rpx;
}

.payment-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 32rpx 28rpx;
    background: #fff;
    border-radius: 24rpx;
    margin-bottom: 20rpx;
    border: 3rpx solid #f0f0f0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4rpx 16rpx rgba(0, 0, 0, 0.04);
}

.payment-item:last-child {
    margin-bottom: 0;
}

.payment-item.payment-item-active {
    transform: translateY(-2rpx);
    box-shadow: 0 8rpx 24rpx rgba(0, 0, 0, 0.1);
}

.payment-left {
    display: flex;
    align-items: center;
    flex: 1;
}

.payment-icon {
    width: 96rpx;
    height: 96rpx;
    border-radius: 24rpx;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 24rpx;
    box-shadow: 0 6rpx 20rpx rgba(0, 0, 0, 0.08);
    border: 2rpx solid #f0f0f0;
}

.pay-icon-emoji {
    font-size: 48rpx;
}

.pay-icon-1 {
    background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
    border-color: #a5d6a7;
}

.pay-icon-2 {
    background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
    border-color: #81c784;
}

.pay-icon-3 {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    border-color: #90caf9;
}

.payment-info {
    display: flex;
    flex-direction: column;
}

.payment-name {
    font-size: 32rpx;
    font-weight: 700;
    color: #1a1a2e;
    margin-bottom: 8rpx;
}

.payment-extra {
    font-size: 26rpx;
    color: #888;
    font-weight: 500;
}

.payment-insufficient {
    font-size: 24rpx;
    color: #ff4d4f;
    font-weight: 600;
    margin-top: 4rpx;
}

.payment-item-disabled {
    opacity: 0.6;
}

.payment-check {
    display: flex;
    align-items: center;
    justify-content: center;
}

.check-circle {
    width: 52rpx;
    height: 52rpx;
    border-radius: 50%;
    border: 4rpx solid #e0e0e0;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: #fff;
}

.check-circle.check-circle-active {
    border-color: transparent;
    box-shadow: 0 4rpx 12rpx rgba(0, 0, 0, 0.15);
    transform: scale(1.1);
}

.payment-footer {
    padding: 24rpx 30rpx 0;
}

.security-tip {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 24rpx;
    padding: 16rpx 24rpx;
    background: #f0fdf4;
    border-radius: 100rpx;
}

.security-text {
    font-size: 26rpx;
    color: #166534;
    margin-left: 10rpx;
    font-weight: 600;
}

.confirm-pay-btn {
    width: 100%;
    color: #fff;
    border-radius: 100rpx;
    padding: 34rpx 40rpx;
    font-size: 34rpx;
    font-weight: 800;
    border: none;
    line-height: normal;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 16rpx;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 8rpx 24rpx rgba(0, 0, 0, 0.2);
    position: relative;
    overflow: hidden;
}

.confirm-pay-btn::after {
    border: none;
}

.confirm-pay-btn:active {
    transform: scale(0.96);
    box-shadow: 0 4rpx 12rpx rgba(0, 0, 0, 0.15);
}

.confirm-pay-text {
    font-size: 34rpx;
    letter-spacing: 1rpx;
}

.confirm-pay-price {
    font-size: 40rpx;
    font-weight: 900;
}

.rich-content {
    font-size: 28rpx;
    color: #333;
    line-height: 1.8;
    word-break: break-all;
}

.rich-content img {
    max-width: 100% !important;
    height: auto !important;
    display: block;
    margin: 20rpx auto;
    border-radius: 12rpx;
}

.rich-content p {
    margin: 16rpx 0;
}

.rich-content h1,
.rich-content h2,
.rich-content h3,
.rich-content h4,
.rich-content h5,
.rich-content h6 {
    font-weight: 700;
    margin: 24rpx 0 16rpx;
    color: #1a1a2e;
}

.rich-content ul,
.rich-content ol {
    padding-left: 40rpx;
    margin: 16rpx 0;
}

.rich-content li {
    margin: 8rpx 0;
}

.rich-content blockquote {
    border-left: 6rpx solid #ddd;
    padding-left: 24rpx;
    margin: 20rpx 0;
    color: #666;
}

.rich-content table {
    width: 100%;
    border-collapse: collapse;
    margin: 20rpx 0;
}

.rich-content th,
.rich-content td {
    border: 1rpx solid #ddd;
    padding: 12rpx 16rpx;
    text-align: left;
}

.rich-content th {
    background: #f5f5f5;
    font-weight: 700;
}

.coupon-section {
    padding: 0 30rpx 24rpx;
}

.coupon-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 24rpx;
    background: #fff;
    border-radius: 20rpx;
    border: 2rpx solid #f0f0f0;
}

.coupon-left {
    display: flex;
    align-items: center;
    gap: 16rpx;
}

.coupon-title {
    font-size: 30rpx;
    font-weight: 600;
    color: #333;
}

.coupon-right {
    display: flex;
    align-items: center;
    gap: 8rpx;
}

.coupon-selected {
    font-size: 28rpx;
    font-weight: 600;
    color: #f5576c;
}

.coupon-placeholder {
    font-size: 26rpx;
    color: #999;
}

.coupon-discount {
    margin-top: 12rpx;
}

.discount-text {
    font-size: 24rpx;
    color: rgba(255, 255, 255, 0.9);
}

.coupon-popup {
    background: #f8f9fa;
    border-radius: 40rpx 40rpx 0 0;
    max-height: 70vh;
}

.coupon-popup-header {
    padding: 32rpx 30rpx;
    text-align: center;
    background: #fff;
    border-radius: 40rpx 40rpx 0 0;
}

.coupon-popup-title {
    font-size: 34rpx;
    font-weight: 700;
    color: #1a1a2e;
}

.coupon-list {
    max-height: 50vh;
    padding: 20rpx;
}

.coupon-item {
    display: flex;
    align-items: center;
    background: #fff;
    border-radius: 20rpx;
    margin-bottom: 20rpx;
    overflow: hidden;
    border: 3rpx solid #f0f0f0;
    position: relative;
}

.coupon-item-selected {
    border-color: #f5576c;
    background: #fff5f7;
}

.coupon-item-left {
    background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%);
    padding: 30rpx 24rpx;
    text-align: center;
    min-width: 160rpx;
}

.coupon-money {
    font-size: 40rpx;
    font-weight: 800;
    color: #fff;
    display: block;
}

.coupon-condition {
    font-size: 22rpx;
    color: rgba(255, 255, 255, 0.8);
    margin-top: 8rpx;
    display: block;
}

.coupon-item-right {
    flex: 1;
    padding: 20rpx 24rpx;
}

.coupon-name {
    font-size: 28rpx;
    font-weight: 600;
    color: #333;
    display: block;
    margin-bottom: 8rpx;
}

.coupon-time {
    font-size: 24rpx;
    color: #999;
    display: block;
}

.coupon-check {
    position: absolute;
    right: 20rpx;
    top: 50%;
    transform: translateY(-50%);
}

.coupon-empty {
    text-align: center;
    padding: 60rpx;
    color: #999;
}

.coupon-popup-footer {
    padding: 20rpx 30rpx 40rpx;
    background: #fff;
}

.not-use-coupon {
    text-align: center;
    padding: 24rpx;
    background: #f5f5f5;
    border-radius: 100rpx;
    font-size: 28rpx;
    color: #666;
}

.watermark-layer {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 9;
    overflow: visible;
}

.watermark-content {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.watermark-fixed {
    position: absolute;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20rpx;
}

.watermark-right-bottom {
    right: 40rpx;
    bottom: 200rpx;
}

.watermark-left-top {
    left: 40rpx;
    top: 200rpx;
}

.watermark-tile {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 300%;
    height: 300%;
    margin-left: -150%;
    margin-top: -150%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    transform: rotate(-45deg);
}

.watermark-row {
    display: flex;
    margin-bottom: 60rpx;
}

.watermark-item {
    width: 180rpx;
    margin-right: 80rpx;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.watermark-text {
    font-size: 22rpx;
    color: #999;
    white-space: nowrap;
}

.watermark-contact {
    font-size: 18rpx;
    color: #bbb;
    margin-top: 6rpx;
}
</style>
