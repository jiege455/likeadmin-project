<template>
    <uni-nav title="系列详情" text-color="#ffffff"></uni-nav>

    <view class="series-detail min-h-screen bg-f5">
        <view class="bg-white" v-if="info">
            <image
                :src="info.image || '/static/images/default.png'"
                mode="aspectFill"
                class="w-full h-48"
            />
            <view class="p-4">
                <view class="text-lg font-bold">{{ info.name }}</view>
                <view class="flex items-center mt-2">
                    <view
                        class="text-xs px-2 py-1 rounded"
                        :style="{
                            backgroundColor: themeStore.primaryColor + '20',
                            color: themeStore.primaryColor
                        }"
                    >
                        {{ getLotteryTypeText(info.lottery_type) }}
                    </view>
                    <view class="text-red-500 font-bold text-xl ml-2"
                        >¥{{ info.series_price }}</view
                    >
                </view>
                <view class="text-sm text-gray-500 mt-2">{{ info.series_desc }}</view>
            </view>
        </view>

        <view class="bg-white mx-3 rounded-xl p-4" v-if="info">
            <view class="font-bold mb-3">期次列表 ({{ issueList.length }}期)</view>
            <view
                class="issue-item py-3 border-b border-gray-100"
                v-for="(item, index) in issueList"
                :key="index"
                @click="toIssue(item)"
            >
                <view class="flex items-center justify-between">
                    <view>
                        <view class="font-medium">{{ item.issue_no }}期</view>
                        <view class="text-sm text-gray-500 mt-1">{{ item.title }}</view>
                    </view>
                    <view class="flex items-center">
                        <view
                            class="text-xs px-2 py-1 rounded mr-2"
                            :class="getStatusClass(item.issue_status)"
                        >
                            {{ getStatusText(item.issue_status) }}
                        </view>
                        <view v-if="item.is_opened" class="text-xs text-green-500">
                            开奖: {{ item.open_code }}
                        </view>
                    </view>
                </view>
            </view>
            <u-empty v-if="issueList.length === 0" mode="list" text="暂无期次"></u-empty>
        </view>

        <view class="fixed bottom-0 left-0 right-0 p-4 bg-white" v-if="info && !hasPermission">
            <view class="flex items-center justify-between">
                <view>
                    <view class="text-sm text-gray-500">系列价格</view>
                    <view class="text-xl font-bold text-red-500">¥{{ info.series_price }}</view>
                </view>
                <view class="flex-1 ml-4">
                    <u-button
                        text="立即购买"
                        @click="handleBuy"
                        :color="themeStore.primaryColor"
                    ></u-button>
                </view>
            </view>
        </view>

        <view class="fixed bottom-0 left-0 right-0 p-4 bg-white" v-if="hasPermission">
            <view class="text-center text-green-500">
                <u-icon name="checkmark-circle" size="20" color="#52C41A"></u-icon>
                <text class="ml-1">已购买，可阅读所有期次</text>
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import request from '@/utils/request'
import { onShow, onLoad } from '@dcloudio/uni-app'
import { useThemeStore } from '@/stores/theme'
import { safeNavigateBack } from '@/utils/util'

const themeStore = useThemeStore()
const themeStyle = computed(() => `--color-primary: ${themeStore.primaryColor};`)

const info = ref<any>(null)
const issueList = ref<any[]>([])
const hasPermission = ref(false)

const getLotteryTypeText = (type: string) => {
    const texts: Record<string, string> = {
        fc3d: '福彩3D',
        pl3: '排列三',
        ssq: '双色球',
        dlt: '大乐透'
    }
    return texts[type] || type
}

const getStatusText = (status: number) => {
    const texts: Record<number, string> = {
        0: '草稿',
        1: '已发布',
        2: '已开奖'
    }
    return texts[status] || '未知'
}

const getStatusClass = (status: number) => {
    const classes: Record<number, string> = {
        0: 'bg-gray-100 text-gray-500',
        1: 'bg-blue-100 text-blue-500',
        2: 'bg-orange-100 text-orange-500'
    }
    return classes[status] || ''
}

const getDetail = async () => {
    const pages = getCurrentPages()
    const currentPage = pages[pages.length - 1] as any
    const id = currentPage?.options?.id || currentPage?.$page?.options?.id

    if (!id) return

    try {
        const res = await request.get({ url: '/series/detail', data: { id } })
        info.value = res.info
        issueList.value = res.issues || []
        hasPermission.value = res.has_permission || false
    } catch (e) {}
}

const toIssue = (item: any) => {
    uni.navigateTo({ url: `/packages/pages/series/issue?id=${item.id}` })
}

const handleBuy = () => {
    uni.navigateTo({ url: `/packages/pages/series/order?series_id=${info.value?.id}` })
}

const goBack = () => {
    safeNavigateBack({ defaultUrl: '/pages/index/index' })
}

declare const getCurrentPages: any

onLoad(() => {
    getDetail()
})

onShow(() => {
    uni.setNavigationBarTitle({ title: '系列详情' })
    getDetail()
    themeStore.getTheme()
})
</script>

<style lang="scss" scoped>
.series-detail {
    background-color: #f5f5f5;
    padding-bottom: 100px;
}
.bg-f5 {
    background-color: #f5f5f5;
}
</style>
