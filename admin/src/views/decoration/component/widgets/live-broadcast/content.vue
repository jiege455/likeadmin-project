<template>
    <div
        class="live-broadcast"
        :style="{
            backgroundColor: styles.bg_color,
            margin: `${styles.margin}px`,
            padding: `${styles.padding}px`,
            borderRadius: `${styles.radius}px`
        }"
    >
        <div class="live-content flex items-center">
            <div class="image-wrapper mr-3">
                 <img v-if="content.image" :src="content.image" class="w-12 h-12 rounded object-cover" />
                 <div v-else class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center text-gray-400 text-xs">
                     封面
                 </div>
            </div>
            <div class="flex-1">
                <div class="title font-bold" :style="{ color: styles.text_color }">{{ content.title || '直播标题' }}</div>
                <div class="text-xs text-gray-400 mt-1">点击进入直播间</div>
            </div>
            <div class="btn px-3 py-1 bg-red-500 text-white text-xs rounded-full">
                观看
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
