<template>
    <view class="promotion-apply-widget">
        <view class="card">
            <view class="info">
                <text class="title">{{ content.title || '成为推广员' }}</text>
                <text class="desc">{{ content.desc || '分享赚佣金' }}</text>
            </view>
            <view class="btn" @click="handleClick">{{ content.btnText || '立即申请' }}</view>
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
    // 优先使用后台配置的链接，如果没有则使用默认链接
    if (props.content.link && props.content.link.path) {
        navigateTo(props.content.link)
    } else {
        uni.navigateTo({ url: '/pages/business/promotion-apply' })
    }
}
</script>

<style scoped>
.promotion-apply-widget {
    padding: 20rpx;
}
.card {
    background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
    padding: 30rpx;
    border-radius: 16rpx;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.info {
    display: flex;
    flex-direction: column;
}
.title {
    font-size: 32rpx;
    font-weight: bold;
    color: #fff;
    margin-bottom: 10rpx;
}
.desc {
    font-size: 24rpx;
    color: rgba(255, 255, 255, 0.9);
}
.btn {
    background-color: #fff;
    color: #ff9a9e;
    padding: 10rpx 30rpx;
    border-radius: 30rpx;
    font-size: 26rpx;
    font-weight: bold;
}
</style>
