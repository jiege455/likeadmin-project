<template>
    <view class="article-publish-widget">
        <view class="publish-btn" @click="handleClick">
            <text>{{ content.btnText || '发布文章' }}</text>
        </view>
    </view>
</template>

<script setup lang="ts">
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
    uni.navigateTo({ url: '/pages/business/article-publish' })
}
</script>

<style scoped>
.article-publish-widget {
    padding: 20rpx;
    background-color: #fff;
}
.publish-btn {
    height: 88rpx;
    background-color: #1989fa;
    color: #fff;
    border-radius: 8rpx;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32rpx;
}
</style>
