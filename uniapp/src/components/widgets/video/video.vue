<template>
    <view
        class="w-video"
        :style="{
            backgroundColor: styles.bg_color,
            margin: `${styles.margin}px`,
            padding: `${styles.padding}px`,
            borderRadius: `${styles.radius}px`
        }"
    >
        <video
            class="video-player"
            :src="content.url"
            :poster="content.poster"
            :autoplay="content.autoplay == 1"
            controls
            object-fit="contain"
        ></video>
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
.w-video {
    overflow: hidden;
    .video-player {
        width: 100%;
        height: 420rpx;
        border-radius: 10rpx;
    }
}
</style>
