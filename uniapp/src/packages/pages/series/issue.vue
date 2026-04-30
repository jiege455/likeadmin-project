<template>
    <uni-nav title="期次阅读" text-color="#ffffff"></uni-nav>

    <view class="issue-read min-h-screen bg-f5">
        <view class="p-3" v-if="!hasPermission">
            <view class="bg-white rounded-xl p-6 text-center">
                <view class="text-gray-400 mb-4">您还没有购买该系列</view>
                <view class="text-sm text-gray-400">请先购买后再阅读</view>
                <view class="mt-4">
                    <u-button
                        text="立即购买"
                        @click="handleBuy"
                        :color="themeStore.primaryColor"
                    ></u-button>
                </view>
            </view>
        </view>

        <view class="p-3" v-else>
            <view class="bg-white rounded-xl p-4" v-if="info">
                <view class="flex items-center justify-between mb-4">
                    <view>
                        <view class="font-bold text-lg">{{ info.title }}</view>
                        <view class="text-sm text-gray-400">期号: {{ info.issue_no }}</view>
                    </view>
                    <view
                        class="text-xs px-2 py-1 rounded"
                        :style="{
                            backgroundColor: themeStore.primaryColor + '20',
                            color: themeStore.primaryColor
                        }"
                    >
                        {{ getLotteryTypeText(info.lottery_type) }}
                    </view>
                </view>

                <view class="border-t border-gray-100 pt-4">
                    <view class="text-sm text-gray-500 mb-2">简介</view>
                    <view>{{ info.desc }}</view>
                </view>

                <view class="border-t border-gray-100 pt-4">
                    <view class="text-sm text-gray-500 mb-2">正文内容</view>
                    <rich-text :nodes="info.content" class="article-content"></rich-text>
                </view>

                <view class="border-t border-gray-100 pt-4" v-if="info.hidden_content">
                    <view class="text-sm text-gray-500 mb-2">隐藏内容 (付费后可见)</view>
                    <view class="bg-yellow-50 rounded-lg p-3">
                        <rich-text :nodes="info.hidden_content"></rich-text>
                    </view>
                </view>

                <view class="border-t border-gray-100 pt-4" v-if="info.is_opened">
                    <view class="text-sm text-gray-500 mb-2">开奖信息</view>
                    <view class="bg-green-50 rounded-lg p-3">
                        <view class="flex items-center justify-between">
                            <view>
                                <view class="font-bold text-green-600">开奖号码</view>
                                <view class="text-2xl font-bold text-green-600 mt-1">{{
                                    info.open_code
                                }}</view>
                            </view>
                            <view class="text-sm text-gray-400">
                                开奖时间:
                                {{
                                    info.open_time
                                        ? new Date(info.open_time * 1000).toLocaleString()
                                        : '-'
                                }}
                            </view>
                        </view>
                    </view>
                </view>
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

const getInfo = async () => {
    try {
        const id = getQueryId()
        const res = await request.get({ url: '/series.issue/read', data: { id } })
        info.value = res.info
        hasPermission.value = res.has_permission || false
    } catch (e) {
        uni.$u.toast(e?.msg || '获取失败')
    }
}

const handleBuy = () => {
    uni.navigateTo({ url: `/packages/pages/series/order?series_id=${info.value?.series_id}` })
}

const goBack = () => {
    safeNavigateBack({ defaultUrl: '/pages/index/index' })
}

const getQueryId = () => {
    const pages = getCurrentPages()
    const currentPage = pages[pages.length - 1] as any
    return currentPage?.options?.id || currentPage?.$page?.options?.id
}

onLoad((options: any) => {
    if (options.id) {
        getInfo()
    }
})

onShow(() => {
    uni.setNavigationBarTitle({ title: '期次阅读' })
    themeStore.getTheme()
})
</script>

<style lang="scss" scoped>
.issue-read {
    background-color: #f5f5f5;
}
.bg-f5 {
    background-color: #f5f5f5;
}
.article-content {
    line-height: 1.8;
}
</style>
