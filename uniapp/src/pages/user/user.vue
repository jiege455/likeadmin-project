<!--
    开发者公众号：杰哥网络科技
    QQ: 2711793818 杰哥
-->
<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar :front-color="$theme.navColor" :background-color="$theme.navBgColor" />
        <!-- #endif -->
    </page-meta>
    <view class="user pb-20">
        <!-- 动态渲染组件 -->
        <template v-for="(item, index) in state.pages" :key="index">
            <template v-if="item.name == 'user-info'">
                <w-user-info
                    :pageMeta="state.meta"
                    :content="item.content"
                    :styles="item.styles"
                    :user="userInfo"
                    :isLogin="isLogin"
                />
            </template>
            <template v-if="item.name == 'my-service'">
                <w-my-service
                    :content="item.content"
                    :styles="item.styles"
                    :user="userInfo"
                    :isLogin="isLogin"
                />
            </template>
            <template v-if="item.name == 'user-banner'">
                <w-user-banner :content="item.content" :styles="item.styles" />
            </template>
            <!-- 兼容其他组件 -->
            <template v-if="item.name == 'nav'">
                <w-nav :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'merchant-center'">
                <w-merchant-center :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'float-btn'">
                <w-float-btn :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'banner'">
                <w-banner :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'middle-banner'">
                <w-middle-banner :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'search'">
                <w-search :content="item.content" :styles="item.styles" />
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
            <template v-if="item.name == 'title-text'">
                <w-title-text :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'article-list'">
                <w-article-list :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'wallet-card'">
                <w-wallet-card :content="item.content" :styles="item.styles" />
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
            <template v-if="item.name == 'live-broadcast'">
                <w-live-broadcast :content="item.content" :styles="item.styles" />
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
        </template>

        <!-- 底部占位，防止被 TabBar 遮挡 -->
        <view class="h-[100rpx]"></view>

        <tabbar />
    </view>
</template>

<script setup lang="ts">
import { useUserStore } from '@/stores/user'
import { onShow, onLoad, onPullDownRefresh } from '@dcloudio/uni-app'
import { storeToRefs } from 'pinia'
import { getDecorate, getIndex } from '@/api/shop'
import { reactive, ref } from 'vue'
import wMerchantCenter from '@/components/widgets/w-merchant-center/w-merchant-center.vue'
import wFloatBtn from '@/components/widgets/w-float-btn/w-float-btn.vue'
import wNews from '@/components/widgets/w-news/w-news.vue'
import wBanner from '@/components/widgets/banner/banner.vue'
import wMiddleBanner from '@/components/widgets/middle-banner/middle-banner.vue'
import wSearch from '@/components/widgets/search/search.vue'
import wNav from '@/components/widgets/nav/nav.vue'
import wUserInfo from '@/components/widgets/user-info/user-info.vue'
import wMyService from '@/components/widgets/my-service/my-service.vue'
import wUserBanner from '@/components/widgets/user-banner/user-banner.vue'
import wNotice from '@/components/widgets/notice/notice.vue'
import wRubikCube from '@/components/widgets/rubik-cube/rubik-cube.vue'
import wBlank from '@/components/widgets/blank/blank.vue'
import wSeparateLine from '@/components/widgets/separate-line/separate-line.vue'
import wVideo from '@/components/widgets/video/video.vue'
import wTitleText from '@/components/widgets/title-text/title-text.vue'
import wArticleList from '@/components/widgets/article-list/article-list.vue'
import wWalletCard from '@/components/widgets/wallet-card/wallet-card.vue'
import wInviteFriends from '@/components/widgets/invite-friends/invite-friends.vue'
import wCouponCenter from '@/components/widgets/w-coupon-center/w-coupon-center.vue'
import wCustomerService from '@/components/widgets/customer-service/customer-service.vue'
import wLiveBroadcast from '@/components/widgets/live-broadcast/live-broadcast.vue'
import wMerchantApplyForm from '@/components/widgets/merchant-apply-form/merchant-apply-form.vue'
import wArticlePublishForm from '@/components/widgets/article-publish-form/article-publish-form.vue'
import wPublicChatWindow from '@/components/widgets/public-chat-window/public-chat-window.vue'
import wPromotionApplyForm from '@/components/widgets/promotion-apply-form/promotion-apply-form.vue'

const userStore = useUserStore()
const { userInfo, isLogin } = storeToRefs(userStore)

const state = reactive<{
    pages: any[]
    meta: any[]
    article: any[]
    notice: any[]
}>({
    pages: [],
    meta: [],
    article: [],
    notice: []
})

// 标记是否正在加载数据，防止重复请求
const isLoading = ref(false)
// 标记是否已经加载过数据
const hasLoaded = ref(false)

const getData = async () => {
    // 如果正在加载中，直接返回，避免重复请求
    if (isLoading.value) return

    isLoading.value = true
    try {
        // 获取个人中心装修数据 (type=2)
        const data = await getDecorate({ id: 2, t: Date.now() })
        state.pages = data?.data ? JSON.parse(data.data) : []
        state.meta = data?.meta ? JSON.parse(data.meta) : []

        // 获取公共数据（新闻、公告）
        const indexData = await getIndex({ timestamp: Date.now() })
        state.article = indexData?.article || []
        state.notice = indexData?.notice || []

        hasLoaded.value = true
    } catch (e) {
        console.error('获取装修数据失败:', e)
        state.pages = []
        state.meta = []
    } finally {
        isLoading.value = false
        uni.stopPullDownRefresh()
    }
}

onLoad(() => {
    getData()
})

onPullDownRefresh(() => {
    getData()
})

onShow(() => {
    // 只有在未加载过数据时才请求，避免重复请求导致 abort 错误
    if (!hasLoaded.value) {
        getData()
    }
    if (isLogin.value) {
        userStore.getUser()
    }
})
</script>

<style lang="scss" scoped>
.user {
    min-height: 100vh;
    background-color: #f5f5f5;
}
</style>
