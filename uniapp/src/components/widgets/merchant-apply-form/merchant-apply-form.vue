<template>
    <view class="merchant-apply-widget">
        <view class="banner">
            <text class="title">{{ content.title || '商家入驻' }}</text>
        </view>
        <view class="action-btn" @click="handleClick">
            {{ content.btnText || '立即申请' }}
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
    uni.navigateTo({ url: '/pages/business/merchant-apply' })
}
</script>

<style scoped>
.merchant-apply-widget {
    background-color: #fff;
    padding: 20rpx;
    border-radius: 10rpx;
    text-align: center;
}
.banner {
    height: 200rpx;
    background-color: #f8f8f8;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20rpx;
    border-radius: 10rpx;
}
.title {
    font-size: 32rpx;
    font-weight: bold;
    color: #333;
}
.action-btn {
    height: 80rpx;
    line-height: 80rpx;
    background-color: #1989fa;
    color: #fff;
    border-radius: 40rpx;
    font-size: 28rpx;
}
</style>
