<template>
    <view class="w-news" :style="{ backgroundColor: styles.bg_color }">
        <view
            class="flex items-center article-title mx-[20rpx] my-[30rpx] text-lg font-medium"
            :style="{ color: styles.title_color }"
        >
            最新资料
        </view>
        <news-card v-for="item in articleList" :key="item.id" :news-id="item.id" :item="item" />
    </view>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps({
    content: {
        type: Object,
        default: () => ({})
    },
    styles: {
        type: Object,
        default: () => ({})
    },
    article: {
        type: Array,
        default: () => []
    }
})

// 优先使用传入的文章列表
const articleList = computed(() => {
    return props.article || []
})
</script>

<style lang="scss" scoped>
.article-title {
    &::before {
        content: '';
        width: 8rpx;
        height: 34rpx;
        display: block;
        margin-right: 10rpx;
        @apply bg-primary;
    }
}
</style>
