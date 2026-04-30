<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar :front-color="$theme.navColor" :background-color="$theme.navBgColor" />
        <!-- #endif -->
    </page-meta>
    <view class="merchant-home" :style="pageStyle">
        <!-- 动态渲染组件 -->
        <template v-for="(item, index) in state.pages" :key="index">
            <template v-if="item.name == 'search'">
                <w-search
                    :pageMeta="state.meta"
                    :content="item.content"
                    :styles="item.styles"
                    :isLargeScreen="false"
                />
            </template>
            <template v-if="item.name == 'banner'">
                <w-banner :content="item.content" :styles="item.styles" :isLargeScreen="false" />
            </template>
            <template v-if="item.name == 'nav'">
                <w-nav :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'merchant-header'">
                <w-merchant-header
                    :content="item.content"
                    :styles="item.styles"
                    :merchant="merchantInfo"
                />
            </template>
            <template v-if="item.name == 'merchant-content-list'">
                <w-merchant-content-list
                    :content="item.content"
                    :styles="item.styles"
                    :merchant-id="merchantId"
                />
            </template>
            <template v-if="item.name == 'notice'">
                <w-notice :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'separate-line'">
                <w-separate-line :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'blank'">
                <w-blank :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'title-text'">
                <w-title-text :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'rubik-cube'">
                <w-rubik-cube :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'video'">
                <w-video :content="item.content" :styles="item.styles" />
            </template>
            <template v-if="item.name == 'live-broadcast'">
                <w-live-broadcast :content="item.content" :styles="item.styles" />
            </template>
        </template>

        <tabbar />
    </view>
</template>

<script setup lang="ts">
import { onLoad, onShow } from '@dcloudio/uni-app'
import { reactive, computed, ref } from 'vue'
import { getDecorate } from '@/api/shop'
import { getMerchantDetail, getMerchantInfoById } from '@/api/merchant'
import { useUserStore } from '@/stores/user'
import wSearch from '@/components/widgets/search/search.vue'
import wBanner from '@/components/widgets/banner/banner.vue'
import wNav from '@/components/widgets/nav/nav.vue'
import wMerchantHeader from '@/components/widgets/w-merchant-header/w-merchant-header.vue'
import wMerchantContentList from '@/components/widgets/w-merchant-content-list/w-merchant-content-list.vue'
import wNotice from '@/components/widgets/notice/notice.vue'
import wSeparateLine from '@/components/widgets/separate-line/separate-line.vue'
import wBlank from '@/components/widgets/blank/blank.vue'
import wTitleText from '@/components/widgets/title-text/title-text.vue'
import wRubikCube from '@/components/widgets/rubik-cube/rubik-cube.vue'
import wVideo from '@/components/widgets/video/video.vue'
import wLiveBroadcast from '@/components/widgets/live-broadcast/live-broadcast.vue'

const userStore = useUserStore()
const merchantId = ref(0)
const merchantInfo = ref({})
const inviteCode = ref('')
const fromMerchantId = ref('')

const state = reactive<{
    pages: any[]
    meta: any[]
}>({
    pages: [],
    meta: []
})

// 根页面样式
const pageStyle = computed(() => {
    const { bg_type, bg_color, bg_image } = state.meta[0]?.content ?? {}
    return bg_type == 1
        ? { 'background-color': bg_color }
        : { 'background-image': `url(${bg_image})` }
})

const getData = async () => {
    // 获取商家主页装修数据 (type=5)
    const data = await getDecorate({ id: 5 })
    state.pages = JSON.parse(data.data)
    state.meta = JSON.parse(data.meta)

    // 设置页面标题
    if (state.meta[0]?.content?.title) {
        uni.setNavigationBarTitle({
            title: state.meta[0].content.title
        })
    }
}

const getMerchantInfo = async () => {
    // 这里假设通过参数传入商户ID，或者获取当前商户信息
    // 实际业务中可能需要根据路由参数获取指定商户信息
    // 暂时先获取自己的商户信息作为演示
    try {
        const res = await getMerchantDetail()
        merchantInfo.value = res
        merchantId.value = res.id
    } catch (e) {
        // 不是商户或未登录
    }
}

// 处理推广参数
const handlePromotionParams = () => {
    // 保存邀请码到本地存储，用于后续注册时绑定推广关系
    if (inviteCode.value) {
        uni.setStorageSync('invite_code', inviteCode.value)
    }

    // 如果用户未登录，提示去注册
    if (!userStore.isLogin && inviteCode.value) {
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
    }
}

onLoad((options: any) => {
    // 获取推广参数
    if (options.invite_code) {
        inviteCode.value = options.invite_code
    }
    if (options.merchant_id) {
        fromMerchantId.value = options.merchant_id
    }

    if (options.id) {
        merchantId.value = parseInt(options.id)
        getOtherMerchantInfo(options.id)
    } else {
        getMerchantInfo()
    }
    getData()

    // 处理推广参数
    handlePromotionParams()
})

onShow(() => {
    // 每次显示页面时检查推广参数
    handlePromotionParams()
})

const getOtherMerchantInfo = async (id: number) => {
    try {
        const res = await getMerchantInfoById(id)
        merchantInfo.value = res
    } catch (e) {
        //
    }
}
</script>

<style lang="scss" scoped>
.merchant-home {
    min-height: 100vh;
    background-repeat: no-repeat;
    background-size: 100% auto;
}
</style>
