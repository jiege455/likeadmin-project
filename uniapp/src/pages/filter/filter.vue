<template>
    <view class="filter-page">
        <!-- 头部红色背景 -->
        <view class="header bg-red-600 h-12 flex items-center justify-center">
            <text class="text-white text-lg font-bold">过滤器</text>
        </view>

        <!-- 卡片区域 -->
        <view class="p-4 grid grid-cols-2 gap-4 -mt-6">
            <view
                class="card bg-white rounded-xl shadow-lg p-6 flex flex-col items-center justify-center h-32"
                @click="toFilter('3d')"
            >
                <view class="text-red-600 text-4xl font-bold mb-2">3</view>
                <view class="text-gray-800 font-bold">3D</view>
            </view>

            <view
                class="card bg-white rounded-xl shadow-lg p-6 flex flex-col items-center justify-center h-32"
                @click="toFilter('p3')"
            >
                <view class="text-blue-500 text-4xl font-bold mb-2">3</view>
                <view class="text-gray-800 font-bold">排3</view>
            </view>
        </view>

        <tabbar />
    </view>
</template>

<script setup lang="ts">
const toFilter = (type: string) => {
    uni.showToast({ title: `进入${type}过滤器`, icon: 'none' })
}
</script>

<style lang="scss" scoped>
.filter-page {
    min-height: 100vh;
    background-color: #f5f5f5;
}
.header {
    height: 120rpx;
    padding-bottom: 40rpx;
}
</style>
