<template>
    <uni-nav title="公告详情"></uni-nav>
    <view class="notice-detail-container bg-gray-50 min-h-screen" :style="themeStyle">
        <!-- 内容区域 -->
        <view class="content-area" v-if="noticeData.id">
            <!-- 封面图 -->
            <view class="cover-section" v-if="noticeData.cover">
                <image :src="noticeData.cover" mode="widthFix" class="w-full" />
            </view>

            <!-- 标题区 -->
            <view class="title-section bg-white px-[30rpx] py-[30rpx]">
                <view class="flex items-center mb-[20rpx]">
                    <view
                        v-if="noticeData.type == 2"
                        class="text-white text-xs px-[16rpx] py-[6rpx] rounded mr-[16rpx]"
                        :style="{ backgroundColor: themeStore.primaryColor }"
                    >
                        重要
                    </view>
                    <view
                        v-else-if="noticeData.type == 3"
                        class="bg-orange-500 text-white text-xs px-[16rpx] py-[6rpx] rounded mr-[16rpx]"
                    >
                        活动
                    </view>
                    <view
                        v-if="noticeData.is_top == 1"
                        class="bg-red-500 text-white text-xs px-[16rpx] py-[6rpx] rounded"
                    >
                        置顶
                    </view>
                </view>
                <view class="text-2xl font-bold text-gray-800 leading-relaxed">{{
                    noticeData.title
                }}</view>
                <view class="flex items-center mt-[20rpx] text-sm text-gray-500">
                    <view class="flex items-center">
                        <u-icon name="clock" size="24" class="mr-[8rpx]"></u-icon>
                        <text>{{ noticeData.create_time }}</text>
                    </view>
                </view>
            </view>

            <!-- 内容区 -->
            <view class="content-section bg-white px-[30rpx] py-[30rpx] mt-[20rpx]">
                <view class="text-base text-gray-700 leading-relaxed whitespace-pre-wrap">
                    {{ noticeData.content }}
                </view>
            </view>
        </view>

        <!-- 加载中 -->
        <view class="loading-section flex items-center justify-center py-[100rpx]" v-if="loading">
            <u-loading-icon size="40"></u-loading-icon>
            <text class="ml-[20rpx] text-gray-500">加载中...</text>
        </view>

        <!-- 空状态 -->
        <view
            class="empty-section flex flex-col items-center justify-center py-[200rpx]"
            v-if="!loading && !noticeData.id"
        >
            <u-icon name="info-circle" size="80" color="#ccc"></u-icon>
            <text class="text-gray-400 mt-[20rpx]">公告不存在或已删除</text>
        </view>
    </view>
</template>

<script lang="ts" setup>
import { ref, computed } from 'vue'
import { onLoad } from '@dcloudio/uni-app'
import { getNoticeDetail } from '@/api/notice'
import { useThemeStore } from '@/stores/theme'
import { safeNavigateBack } from '@/utils/util'

const themeStore = useThemeStore()
const themeStyle = computed(() => themeStore.vars)

const noticeData = ref<any>({})
const loading = ref(true)
let noticeId = ''

const getData = async () => {
    if (!noticeId) return
    loading.value = true
    try {
        const res = await getNoticeDetail({ id: noticeId })
        noticeData.value = res || {}
    } catch (e) {
        console.error('获取公告详情失败:', e)
    } finally {
        loading.value = false
    }
}

const goBack = () => {
    safeNavigateBack({ defaultUrl: '/pages/index/index' })
}

onLoad((options: any) => {
    noticeId = options.id
    getData()
})
</script>

<style lang="scss" scoped>
.notice-detail-container {
}

.cover-section {
    image {
        display: block;
    }
}

.title-section {
    border-bottom: 1rpx solid #f0f0f0;
}

.content-section {
    min-height: 300rpx;
}
</style>
