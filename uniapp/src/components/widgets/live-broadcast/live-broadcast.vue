<template>
    <view
        class="w-live-broadcast"
        :style="{
            backgroundColor: styles.bg_color,
            margin: `${styles.margin}px`,
            padding: `${styles.padding}px`,
            borderRadius: `${styles.radius}px`
        }"
        @click="handleClick"
    >
        <view class="flex items-center">
            <view class="mr-2">
                <image
                    v-if="content.image"
                    :src="content.image"
                    class="w-[100rpx] h-[100rpx] rounded"
                    mode="aspectFill"
                />
                <view
                    v-else
                    class="w-[100rpx] h-[100rpx] bg-gray-200 rounded flex items-center justify-center text-gray-400 text-xs"
                >
                    封面
                </view>
            </view>
            <view class="flex-1">
                <view class="text-lg font-bold" :style="{ color: styles.text_color }">{{
                    content.title || '直播标题'
                }}</view>
                <view class="text-xs text-gray-400 mt-1">点击进入直播间</view>
            </view>
            <view class="px-4 py-1 bg-red-500 text-white text-xs rounded-full"> 观看 </view>
        </view>
    </view>
</template>

<script setup lang="ts">
import { navigateTo } from '@/utils/util'

const props = defineProps({
    content: {
        type: Object,
        default: () => ({})
    },
    styles: {
        type: Object,
        default: () => ({})
    }
})

const handleClick = () => {
    if (props.content.link && props.content.link.path) {
        navigateTo(props.content.link)
    }
}
</script>

<style lang="scss" scoped>
.w-live-broadcast {
    overflow: hidden;
}
</style>
