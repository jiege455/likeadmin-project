<template>
    <view
        class="w-title-text"
        :style="{
            backgroundColor: styles.bg_color,
            textAlign: content.align,
            padding: `${styles.padding}px 20rpx`
        }"
    >
        <view class="title" :style="{ color: styles.title_color }">{{ content.title }}</view>
        <view class="desc" :style="{ color: styles.desc_color }" v-if="content.desc">{{
            content.desc
        }}</view>
    </view>
</template>

<script setup lang="ts">
defineProps({
    content: {
        type: Object,
        default: () => ({})
    },
    styles: {
        type: Object,
        default: () => ({})
    }
})
</script>

<style lang="scss" scoped>
.w-title-text {
    .title {
        font-size: 32rpx;
        font-weight: bold;
        margin-bottom: 8rpx;
    }
    .desc {
        font-size: 24rpx;
    }
}
</style>
