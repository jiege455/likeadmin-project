<template>
    <uni-nav title="推广海报"></uni-nav>

    <view class="distribution-poster page-container">
        <!-- 装修组件渲染 -->
        <block v-for="(item, index) in pageData" :key="index">
            <!-- 推广海报组件 -->
            <w-distribution-poster
                v-if="item.name === 'distribution-poster'"
                :content="item.content"
                :styles="item.styles"
            />
            <w-banner v-if="item.name === 'banner'" :content="item.content" :styles="item.styles" />
            <w-separate-line
                v-if="item.name === 'separate-line'"
                :content="item.content"
                :styles="item.styles"
            />
            <w-blank v-if="item.name === 'blank'" :content="item.content" :styles="item.styles" />
            <w-title-text
                v-if="item.name === 'title-text'"
                :content="item.content"
                :styles="item.styles"
            />
        </block>

        <!-- 如果没有装修数据，显示默认内容 -->
        <view
            v-if="pageData.length === 0"
            class="flex flex-col items-center justify-center min-h-[80vh]"
        >
            <u-loading-icon mode="circle" size="30"></u-loading-icon>
            <view class="mt-2 text-gray-400">加载中...</view>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { getDecorate } from '@/api/shop'
import { getApplyDetail } from '@/api/distribution'
import { onShow } from '@dcloudio/uni-app'
import { useUserStore } from '@/stores/user'
import wDistributionPoster from '@/components/widgets/w-distribution-poster/w-distribution-poster.vue'
import wBanner from '@/components/widgets/banner/banner.vue'
import wSeparateLine from '@/components/widgets/separate-line/separate-line.vue'
import wBlank from '@/components/widgets/blank/blank.vue'
import wTitleText from '@/components/widgets/title-text/title-text.vue'
import { safeNavigateBack } from '@/utils/util'

const userStore = useUserStore()
const pageData = ref<any[]>([])
const isChecked = ref(false)

const goBack = () => {
    safeNavigateBack({ defaultUrl: '/pages/index/index' })
}

const checkDistributorStatus = async () => {
    if (!userStore.isLogin) {
        uni.showToast({ title: '请先登录', icon: 'none' })
        setTimeout(() => {
            uni.navigateTo({ url: '/pages/login/login' })
        }, 1500)
        return false
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
                    } else {
                        safeNavigateBack({ defaultUrl: '/pages/index/index' })
                    }
                }
            })
            return false
        }
        return true
    } catch (e: any) {
        uni.showToast({ title: e.msg || '获取分销员状态失败', icon: 'none' })
        return false
    }
}

const getPageData = async () => {
    try {
        const res = await getDecorate({ id: 6 })
        if (res.data) {
            pageData.value = JSON.parse(res.data)
        } else {
            useDefaultData()
        }
    } catch (e) {
        useDefaultData()
    }
}

const useDefaultData = () => {
    pageData.value = [
        {
            name: 'distribution-poster',
            content: { show_qrcode: 1, show_nickname: 1 },
            styles: {}
        }
    ]
}

onMounted(async () => {
    const isDistributor = await checkDistributorStatus()
    if (isDistributor) {
        isChecked.value = true
        getPageData()
    }
})

onShow(() => {
    uni.setNavigationBarTitle({ title: '推广海报' })
})
</script>

<style lang="scss" scoped>
.page-container {
    min-height: 100vh;
    background-color: #f5f5f5;
}
</style>
