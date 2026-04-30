<template>
    <view
        class="w-custom-navbar z-50"
        :class="{ 'fixed top-0 left-0 right-0': content.fixed }"
        :style="{
            backgroundColor: content.bg_color,
            color: content.text_color,
            paddingTop: `${statusBarHeight}px`
        }"
    >
        <view class="flex items-center justify-between px-3 h-10 relative">
            <!-- 返回按钮 -->
            <view class="w-20 flex items-center h-full" @click="goBack" v-if="content.show_back">
                <u-icon name="arrow-left" :size="20" :color="content.text_color" />
            </view>
            <view v-else class="w-20"></view>

            <!-- 标题 -->
            <view
                class="absolute left-0 right-0 text-center font-bold text-base truncate pointer-events-none px-20"
            >
                {{ content.title }}
            </view>

            <!-- 占位 -->
            <view class="w-20"></view>
        </view>
    </view>
    <!-- 占位元素，防止内容被遮挡 -->
    <view v-if="content.fixed" :style="{ height: `${40 + statusBarHeight}px` }"></view>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps({
    content: {
        type: Object,
        default: () => ({
            title: '',
            bg_color: '#ffffff',
            text_color: '#000000',
            show_back: 1,
            fixed: 1
        })
    },
    styles: {
        type: Object,
        default: () => ({})
    }
})

// 获取状态栏高度
const statusBarHeight = ref(uni.getSystemInfoSync().statusBarHeight || 20)

const goBack = () => {
    // 优先尝试返回上一页
    const pages = getCurrentPages()
    if (pages.length > 1) {
        uni.navigateBack()
    } else {
        // 如果没有上一页，则跳转回首页
        uni.reLaunch({
            url: '/pages/index/index'
        })
    }
}
</script>

<style lang="scss" scoped>
.w-custom-navbar {
    box-sizing: content-box;
}
</style>
