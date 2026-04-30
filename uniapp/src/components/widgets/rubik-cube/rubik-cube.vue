<template>
    <view
        class="w-rubik-cube"
        :style="{
            backgroundColor: styles.bg_color,
            margin: `${styles.margin}px`,
            padding: `${styles.padding}px`,
            borderRadius: `${styles.radius}px`
        }"
    >
        <view class="grid" :class="`cols-${content.style}`">
            <view
                v-for="(item, index) in content.data"
                :key="index"
                class="grid-item"
                @click="handleClick(item.link)"
            >
                <image
                    v-if="item.image"
                    :src="item.image"
                    class="w-full h-full"
                    mode="aspectFill"
                />
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
import { useAppStore } from '@/stores/app'
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

const handleClick = (link: any) => {
    if (!link || !link.path) return
    navigateTo(link)
}
</script>

<style lang="scss" scoped>
.w-rubik-cube {
    overflow: hidden;
    .grid {
        display: grid;
        gap: 20rpx;
        &.cols-1 {
            grid-template-columns: repeat(2, 1fr);
        }
        &.cols-2 {
            grid-template-columns: repeat(3, 1fr);
        }
        .grid-item {
            aspect-ratio: 1;
            border-radius: 10rpx;
            overflow: hidden;
            background-color: #f5f5f5;
            image {
                width: 100%;
                height: 100%;
            }
        }
    }
}
</style>
