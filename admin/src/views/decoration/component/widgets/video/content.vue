<template>
    <div
        class="video"
        :style="{
            backgroundColor: styles.bg_color,
            margin: `${styles.margin}px`,
            padding: `${styles.padding}px`,
            borderRadius: `${styles.radius}px`
        }"
    >
        <div class="video-content">
             <img v-if="content.poster" :src="content.poster" class="w-full h-full object-cover" />
             <div v-else class="empty-video bg-gray-200 flex items-center justify-center text-gray-400">
                 <div class="flex flex-col items-center">
                     <span class="text-3xl">▶</span>
                     <span class="text-xs mt-2">视频组件</span>
                 </div>
             </div>
        </div>
    </div>
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
.video {
    overflow: hidden;
    .video-content {
        aspect-ratio: 16/9;
        background-color: #000;
        position: relative;
        .empty-video {
            width: 100%;
            height: 100%;
        }
    }
}
</style>
