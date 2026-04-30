<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar :front-color="$theme.navColor" :background-color="$theme.navBgColor" />
        <!-- #endif -->
    </page-meta>
    <view class="custom-page" :style="pageStyle">
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
            <template v-if="item.name == 'invite-friends'">
                <w-invite-friends :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'coupon-center'">
                <w-coupon-center :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'customer-service'">
                <w-customer-service :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'wallet-header'">
                <w-wallet-header :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'recharge-panel'">
                <w-recharge-panel :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'custom-navbar'">
                <w-custom-navbar :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'user-info'">
                <w-user-info
                    :pageMeta="state.meta"
                    :content="item.content"
                    :styles="item.styles"
                    :user="{}"
                    :isLogin="false"
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
        </template>

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
    </view>
</template>

<script setup lang="ts">
import { getDecorate } from '@/api/shop'
import { onLoad, onPageScroll, onShow, onPullDownRefresh } from '@dcloudio/uni-app'
import { computed, reactive, ref } from 'vue'
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
import wMerchantApplyForm from '@/components/widgets/merchant-apply-form/merchant-apply-form.vue'
import wArticlePublishForm from '@/components/widgets/article-publish-form/article-publish-form.vue'
import wPublicChatWindow from '@/components/widgets/public-chat-window/public-chat-window.vue'
import wPromotionApplyForm from '@/components/widgets/promotion-apply-form/promotion-apply-form.vue'
import wInviteFriends from '@/components/widgets/invite-friends/invite-friends.vue'
import wCouponCenter from '@/components/widgets/w-coupon-center/w-coupon-center.vue'
import wCustomerService from '@/components/widgets/customer-service/customer-service.vue'
import wWalletHeader from '@/components/widgets/w-wallet-header/w-wallet-header.vue'
import wRechargePanel from '@/components/widgets/w-recharge-panel/w-recharge-panel.vue'
import wCustomNavbar from '@/components/widgets/w-custom-navbar/w-custom-navbar.vue'
import wFloatBtn from '@/components/widgets/w-float-btn/w-float-btn.vue'
import wMerchantCenter from '@/components/widgets/w-merchant-center/w-merchant-center.vue'

import wUserInfo from '@/components/widgets/user-info/user-info.vue'
import wUserBanner from '@/components/widgets/user-banner/user-banner.vue'

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
const pageId = ref<number>(0)

// 是否联动背景图
const isLinkage = computed(() => {
    return state.pages.find((item: any) => item.name === 'banner')?.content.bg_style === 1
})
// 是否大屏banner
const isLargeScreen = computed(() => {
    return state.pages.find((item: any) => item.name === 'banner')?.content.style === 2
})

// 根页面样式
const pageStyle = computed(() => {
    const { bg_type, bg_color, bg_image } = state.meta[0]?.content ?? {}
    // 优先响应用户明确设置的背景色 (bg_type == 1)
    if (bg_type == 1) {
        return { 'background-color': bg_color }
    }
    if (isLinkage.value) {
        return { 'background-image': `url(${state.bannerImage})` }
    }
    return { 'background-image': `url(${bg_image})` }
})

const handleBanner = (url: string) => {
    state.bannerImage = url
}

const getData = async () => {
    if (!pageId.value) return
    const data = await getDecorate({ id: pageId.value, t: Date.now() })
    try {
        state.pages = data?.data ? JSON.parse(data.data) : []
    } catch (e) {
        console.error('[getData] pages JSON解析失败:', e)
        state.pages = []
    }
    try {
        state.meta = data?.meta ? JSON.parse(data.meta) : []
    } catch (e) {
        console.error('[getData] meta JSON解析失败:', e)
        state.meta = []
    }
    if (state.meta && state.meta.length > 0 && state.meta[0]?.content?.name) {
        uni.setNavigationBarTitle({
            title: state.meta[0].content.name
        })
    }
}

onShow(() => {
    if (pageId.value) {
        getData()
    }
})

onPullDownRefresh(async () => {
    await getData()
    uni.stopPullDownRefresh()
})

onPageScroll((event: any) => {
    scrollTop.value = event.scrollTop
    const top = uni.upx2px(100)
    percent.value = event.scrollTop / top > 1 ? 1 : event.scrollTop / top
})

onLoad((options: any) => {
    if (options.id) {
        pageId.value = parseInt(options.id)
        getData()
    }
})
</script>

<style lang="scss" scoped>
.custom-page {
    position: relative;
    background-repeat: no-repeat;
    background-size: 100% auto;
    overflow: hidden;
    width: 100%;
    transition: all 1s;
    min-height: calc(100vh - env(safe-area-inset-bottom));
}
</style>
