<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar :front-color="$theme.navColor" :background-color="$theme.navBgColor" />
        <!-- #endif -->
    </page-meta>
    <view class="index" :style="pageStyle">
        <!-- 首页系统公告弹窗 -->
        <u-popup :show="showNoticePopup" mode="center" round="16" @close="closeNotice">
            <view class="bg-white rounded-lg overflow-hidden" style="width: 600rpx">
                <!-- 封面图 -->
                <view v-if="currentNotice.cover" class="cover-section">
                    <image :src="currentNotice.cover" mode="widthFix" class="w-full" />
                </view>
                <!-- 标题 -->
                <view class="px-5 pt-5 pb-3">
                    <view class="flex items-center justify-center mb-2">
                        <view
                            v-if="currentNotice.type == 2"
                            class="text-white text-xs px-3 py-1 rounded-full mr-2"
                            :style="{ backgroundColor: themeStore.primaryColor }"
                        >
                            重要公告
                        </view>
                        <view
                            v-else-if="currentNotice.type == 3"
                            class="bg-orange-500 text-white text-xs px-3 py-1 rounded-full mr-2"
                        >
                            活动公告
                        </view>
                    </view>
                    <view class="text-center font-bold text-lg">{{ currentNotice.title }}</view>
                </view>
                <!-- 内容 -->
                <scroll-view scroll-y class="max-h-[50vh] px-5">
                    <view class="text-gray-600 leading-relaxed whitespace-pre-wrap">
                        {{ currentNotice.content }}
                    </view>
                </scroll-view>
                <!-- 底部按钮 -->
                <view class="p-5">
                    <view
                        class="text-white text-center py-3 rounded-full font-medium"
                        :style="{ backgroundColor: themeStore.primaryColor }"
                        @click="closeNotice"
                    >
                        我知道了
                    </view>
                    <view class="text-center text-xs text-gray-400 mt-3">
                        发布时间：{{ currentNotice.create_time }}
                    </view>
                </view>
            </view>
        </u-popup>

        <!-- 组件 -->
        <template v-for="(item, index) in state.pages" :key="index">
            <template v-if="item.name == 'search'">
                <w-search
                    :pageMeta="state.meta"
                    :content="item.content"
                    :styles="item.styles"
                    :percent="percent"
                    :isLargeScreen="isLargeScreen"
                />
            </template>
            <template v-if="item.name == 'banner'">
                <w-banner
                    :content="item.content"
                    :styles="item.styles"
                    :isLargeScreen="isLargeScreen"
                    @change="handleBanner"
                />
            </template>
            <template v-if="item.name == 'nav'">
                <w-nav :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'middle-banner'">
                <w-middle-banner :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'news'">
                <w-news :content="item.content" :styles="item.styles" :article="state.article" />
            </template>
            <template v-if="item.name == 'notice'">
                <w-notice :content="item.content" :styles="item.styles" :list="state.notice" />
            </template>
            <template v-if="item.name == 'rubik-cube'">
                <w-rubik-cube :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'blank'">
                <w-blank :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'separate-line'">
                <w-separate-line :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'video'">
                <w-video :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'live-broadcast'">
                <w-live-broadcast :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'title-text'">
                <w-title-text :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'article-list'">
                <w-article-list :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'wallet-card'">
                <w-wallet-card :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'coupon-center'">
                <w-coupon-center :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'merchant-apply-form'">
                <w-merchant-apply-form :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'article-publish-form'">
                <w-article-publish-form :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'public-chat-window'">
                <w-public-chat-window :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'promotion-apply-form'">
                <w-promotion-apply-form :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'list-menu'">
                <w-list-menu :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'invite-friends'">
                <w-invite-friends :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'customer-service'">
                <w-customer-service :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'user-info'">
                <w-user-info
                    :pageMeta="state.meta"
                    :content="item.content"
                    :styles="item.styles"
                    :user="appStore.userInfo"
                    :isLogin="appStore.isLogin"
                />
            </template>
            <template v-if="item.name == 'user-banner'">
                <w-user-banner :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'float-btn'">
                <w-float-btn :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'merchant-center'">
                <w-merchant-center :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'current-merchant'">
                <w-current-merchant
                    :content="item.content"
                    :styles="item.styles"
                    @change="handleMerchantChange"
                />
            </template>
            <template v-if="item.name == 'pc-banner'">
                <w-pc-banner :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'custom-navbar'">
                <w-custom-navbar :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'distribution-poster'">
                <w-distribution-poster :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'invite-stats'">
                <w-invite-stats :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'merchant-content-list'">
                <w-merchant-content-list :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'merchant-header'">
                <w-merchant-header :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'my-service'">
                <w-my-service :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'recharge-panel'">
                <w-recharge-panel :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'wallet-header'">
                <w-wallet-header :content="item.content" :styles="item.styles" />
            </template>
        </template>

        <!--  #ifdef H5  -->
        <view class="text-center py-4 mb-12">
            <router-navigate
                class="mx-1 text-xs text-[#495770]"
                :to="{
                    path: '/pages/webview/webview',
                    query: {
                        url: item.value
                    }
                }"
                v-for="item in appStore.getCopyrightConfig"
                :key="item.key"
            >
                {{ item.key }}
            </router-navigate>
        </view>
        <!--  #endif  -->

        <!-- 返回顶部按钮 -->
        <u-back-top
            :scroll-top="scrollTop"
            :top="100"
            :customStyle="{
                backgroundColor: '#FFF',
                color: '#000',
                boxShadow: '0px 3px 6px rgba(0, 0, 0, 0.1)'
            }"
        >
        </u-back-top>

        <!--  #ifdef MP  -->
        <!--  微信小程序隐私弹窗  -->
        <MpPrivacyPopup></MpPrivacyPopup>
        <!--  #endif  -->

        <!-- 商家切换弹窗 -->
        <MerchantSwitcher
            v-model="showMerchantSwitcher"
            :currentMerchantId="currentMerchant?.id || 0"
            @change="handleMerchantSwitch"
        />

        <tabbar />
    </view>
</template>

<!-- 开发者：杰哥网络科技 qq2711793818 杰哥 -->
<script setup lang="ts">
import { getIndex } from '@/api/shop'
import { getNoticeList, getNoticePopup, getNoticeUnreadCount, markNoticeRead } from '@/api/notice'
import { onLoad, onPageScroll, onShow, onPullDownRefresh } from '@dcloudio/uni-app'
import { computed, reactive, ref } from 'vue'
import { useAppStore } from '@/stores/app'
import { useUserStore } from '@/stores/user'
import { useThemeStore } from '@/stores/theme'
import { useRoute } from 'uniapp-router-next'
import wNews from '@/components/widgets/w-news/w-news.vue'
import wBanner from '@/components/widgets/banner/banner.vue'
import wMiddleBanner from '@/components/widgets/middle-banner/middle-banner.vue'
import wSearch from '@/components/widgets/search/search.vue'
import wNav from '@/components/widgets/nav/nav.vue'
import wNotice from '@/components/widgets/notice/notice.vue'
import wRubikCube from '@/components/widgets/rubik-cube/rubik-cube.vue'
import wBlank from '@/components/widgets/blank/blank.vue'
import wSeparateLine from '@/components/widgets/separate-line/separate-line.vue'
import wVideo from '@/components/widgets/video/video.vue'
import wLiveBroadcast from '@/components/widgets/live-broadcast/live-broadcast.vue'
import wTitleText from '@/components/widgets/title-text/title-text.vue'
import wArticleList from '@/components/widgets/article-list/article-list.vue'
import wWalletCard from '@/components/widgets/wallet-card/wallet-card.vue'
import wInviteFriends from '@/components/widgets/invite-friends/invite-friends.vue'
import wCouponCenter from '@/components/widgets/w-coupon-center/w-coupon-center.vue'
import wCustomerService from '@/components/widgets/customer-service/customer-service.vue'
import wUserInfo from '@/components/widgets/user-info/user-info.vue'
import wUserBanner from '@/components/widgets/user-banner/user-banner.vue'
import wMerchantApplyForm from '@/components/widgets/merchant-apply-form/merchant-apply-form.vue'
import wArticlePublishForm from '@/components/widgets/article-publish-form/article-publish-form.vue'
import wPublicChatWindow from '@/components/widgets/public-chat-window/public-chat-window.vue'
import wPromotionApplyForm from '@/components/widgets/promotion-apply-form/promotion-apply-form.vue'
import wListMenu from '@/components/widgets/list-menu/list-menu.vue'
import wFloatBtn from '@/components/widgets/w-float-btn/w-float-btn.vue'
import wMerchantCenter from '@/components/widgets/w-merchant-center/w-merchant-center.vue'
import wCurrentMerchant from '@/components/widgets/current-merchant/current-merchant.vue'
import wMerchantContentList from '@/components/widgets/w-merchant-content-list/w-merchant-content-list.vue'
import wPcBanner from '@/components/widgets/pc-banner/pc-banner.vue'
import wCustomNavbar from '@/components/widgets/w-custom-navbar/w-custom-navbar.vue'
import wDistributionPoster from '@/components/widgets/w-distribution-poster/w-distribution-poster.vue'
import wInviteStats from '@/components/widgets/w-invite-stats/w-invite-stats.vue'
import wMerchantHeader from '@/components/widgets/w-merchant-header/w-merchant-header.vue'
import wMyService from '@/components/widgets/my-service/my-service.vue'
import wRechargePanel from '@/components/widgets/w-recharge-panel/w-recharge-panel.vue'
import wWalletHeader from '@/components/widgets/w-wallet-header/w-wallet-header.vue'
import MerchantSwitcher from '@/components/business/MerchantSwitcher.vue'

// #ifdef MP
import MpPrivacyPopup from './component/mp-privacy-popup.vue'
// #endif

const appStore = useAppStore()
const userStore = useUserStore()
const themeStore = useThemeStore()
const route = useRoute()

// 处理邀请码和商家ID参数
const handleInviteCode = () => {
    let inviteCode = ''
    let merchantId = ''

    //#ifdef H5
    try {
        const url = window.location.href
        const hashIndex = url.indexOf('#')
        if (hashIndex !== -1) {
            const hashPart = url.substring(hashIndex + 1)
            const queryIndex = hashPart.indexOf('?')
            if (queryIndex !== -1) {
                const queryString = hashPart.substring(queryIndex + 1)
                const params = new URLSearchParams(queryString)
                inviteCode = params.get('invite_code') || ''
                merchantId = params.get('merchant_id') || ''
            }
        }
    } catch (e) {
        console.error('解析邀请码失败', e)
    }
    //#endif

    // 小程序和其他平台通过 route.query 获取
    if (route.query?.invite_code) {
        inviteCode = route.query.invite_code as string
    }
    if (route.query?.merchant_id) {
        merchantId = route.query.merchant_id as string
    }

    // 保存邀请码和商家ID到本地存储
    if (inviteCode) {
        uni.setStorageSync('invite_code', inviteCode)
        console.log('[推广] 保存邀请码:', inviteCode)
    }
    if (merchantId) {
        uni.setStorageSync('merchant_id', merchantId)
        console.log('[推广] 保存商家ID:', merchantId)
    }

    // 如果用户未登录且有邀请码，提示去注册
    if (!userStore.isLogin && inviteCode) {
        setTimeout(() => {
            uni.showModal({
                title: '欢迎访问',
                content: '您是通过推广链接访问的，是否立即注册/登录以建立推广关系？',
                confirmText: '去注册',
                cancelText: '稍后再说',
                success: (res) => {
                    if (res.confirm) {
                        uni.navigateTo({ url: '/pages/register/register' })
                    }
                }
            })
        }, 2000)
    }
}

// 公告弹窗相关状态
const showNoticePopup = ref(false)
const currentNotice = ref<any>({})
const isForceRead = ref(false) // 是否强制阅读
const isCheckingNotice = ref(false) // 是否正在检查公告

// 检查并显示系统公告弹窗
const checkSystemNotice = async () => {
    // 如果正在检查中，直接返回，避免重复请求
    if (isCheckingNotice.value) return

    isCheckingNotice.value = true
    try {
        const res = await getNoticePopup()
        if (res && res.id) {
            currentNotice.value = res
            // 如果是重要公告或强制阅读类型，使用强制模式
            isForceRead.value = res.is_force_read === 1 || res.type === 2
            if (isForceRead.value) {
                // 强制阅读模式：使用 uni.showModal
                showForceReadNotice(res)
            } else {
                // 普通模式：使用弹窗
                showNoticePopup.value = true
            }

            // 设置 APP 角标
            updateAppBadge()
        }
    } catch (e) {
        console.error('获取弹窗公告失败:', e)
    } finally {
        isCheckingNotice.value = false
    }
}

// 强制阅读公告
const showForceReadNotice = (notice: any) => {
    uni.showModal({
        title: '📢 重要公告',
        content:
            notice.title +
            '\n\n' +
            notice.content.substring(0, 100) +
            (notice.content.length > 100 ? '...' : ''),
        showCancel: false,
        confirmText: '我已阅读',
        confirmColor: '#07c160',
        success: (res) => {
            if (res.confirm) {
                // 标记为已读
                markNoticeReadApi(notice.id)
                // 清除角标
                clearAppBadge()
            }
        }
    })
}

const closeNotice = () => {
    showNoticePopup.value = false
    // 标记为已读
    if (currentNotice.value.id) {
        markNoticeReadApi(currentNotice.value.id)
    }
}

// 更新 APP 角标
const updateAppBadge = async () => {
    try {
        const res = await getNoticeUnreadCount()
        const count = res?.count || 0

        // #ifdef APP-PLUS
        try {
            if (count > 0) {
                plus.runtime.setBadge(count.toString())
            } else {
                plus.runtime.setBadge('')
            }
        } catch (e) {
            console.warn('设置角标失败:', e)
        }
        // #endif

        // #ifdef H5
        if (count > 0) {
            let title = document.title
            const match = title.match(/^\(\d+\)\s*(.*)/)
            if (match) {
                title = match[1]
            }
            document.title = `(${count}) ${title}`
        } else {
            const match = document.title.match(/^\(\d+\)\s*(.*)/)
            if (match) {
                document.title = match[1]
            }
        }
        // #endif
    } catch (e) {
        console.error('获取未读数失败:', e)
    }
}

// 清除 APP 角标
const clearAppBadge = () => {
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

// 标记公告已读
const markNoticeReadApi = async (id: number) => {
    try {
        await markNoticeRead({ id })
        // 重新获取未读数量并更新角标
        updateAppBadge()
    } catch (e) {
        console.error('标记已读失败:', e)
    }
}

const state = reactive<{
    pages: any[]
    meta: any[]
    article: any[]
    notice: any[]
    bannerImage: string
}>({
    pages: [],
    meta: [],
    article: [],
    notice: [],
    bannerImage: ''
})
const scrollTop = ref<number>(0)
const percent = ref<number>(0)

// 标记是否正在加载数据，防止重复请求
const isLoading = ref(false)
// 标记是否已经加载过数据
const hasLoaded = ref(false)

// 商家切换相关
const showMerchantSwitcher = ref(false)
const currentMerchant = ref<any>(null)

const handleMerchantChange = (merchant: any) => {
    currentMerchant.value = merchant
    getData()
}

const handleMerchantSwitch = (merchant: any) => {
    currentMerchant.value = merchant
    showMerchantSwitcher.value = false
    uni.showToast({ title: '已切换商家', icon: 'success' })
    getData()
}

const isLinkage = computed(() => {
    return state.pages.find((item: any) => item.name === 'banner')?.content.bg_style === 1
})
const isLargeScreen = computed(() => {
    return state.pages.find((item: any) => item.name === 'banner')?.content.style === 2
})

const pageStyle = computed(() => {
    // 安全获取 meta[0].content，避免空指针异常
    const metaContent = state.meta && state.meta.length > 0 ? state.meta[0]?.content : {}
    const { bg_type, bg_color, bg_image } = metaContent || {}

    if (bg_type == 1) {
        return { 'background-color': bg_color || '#ffffff' }
    }
    if (isLinkage.value) {
        return { 'background-image': `url(${state.bannerImage || ''})` }
    }
    return { 'background-image': `url(${bg_image || ''})` }
})

const handleBanner = (url: string) => {
    state.bannerImage = url
}

const getData = async () => {
    // 如果正在加载中，直接返回，避免重复请求
    if (isLoading.value) return

    isLoading.value = true
    try {
        const data = await getIndex({ timestamp: new Date().getTime() })
        try {
            state.pages = data?.page?.data ? JSON.parse(data.page.data) : []
        } catch (e) {
            console.error('[getData] pages JSON解析失败:', e)
            state.pages = []
        }
        try {
            state.meta = data?.page?.meta ? JSON.parse(data.page.meta) : []
        } catch (e) {
            console.error('[getData] meta JSON解析失败:', e)
            state.meta = []
        }
        state.article = data.article || []

        // 安全设置标题，避免空指针
        if (state.meta && state.meta.length > 0 && state.meta[0]?.content?.title) {
            uni.setNavigationBarTitle({
                title: state.meta[0].content.title
            })
        }
        checkSystemNotice()
        hasLoaded.value = true
    } catch (e) {
        console.error('[getData] 数据解析失败:', e)
        uni.showToast({
            title: '数据加载失败',
            icon: 'none'
        })
    } finally {
        isLoading.value = false
        uni.stopPullDownRefresh()
    }
}

onPageScroll((event: any) => {
    scrollTop.value = event.scrollTop
    const top = uni.upx2px(100)
    percent.value = event.scrollTop / top > 1 ? 1 : event.scrollTop / top
})

onLoad(() => {
    handleInviteCode()
    getData()
})
onShow(() => {
    handleInviteCode()
    // 只有在未加载过数据时才请求，避免重复请求导致 abort 错误
    if (!hasLoaded.value) {
        getData()
    }
})
onPullDownRefresh(() => {
    getData()
})
</script>

<style lang="scss" scoped>
.index {
    position: relative;
    background-repeat: no-repeat;
    background-size: 100% auto;
    overflow: hidden;
    width: 100%;
    transition: all 1s;
    min-height: calc(100vh - env(safe-area-inset-bottom));
}

.article-title {
    &::before {
        content: '';
        width: 8rpx;
        height: 34rpx;
        display: block;
        margin-right: 10rpx;
        @apply bg-primary;
    }
}

.cover-section {
    image {
        display: block;
    }
}
</style>
