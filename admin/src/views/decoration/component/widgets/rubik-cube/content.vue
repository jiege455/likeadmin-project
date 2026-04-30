<template>
    <div
        class="rubik-cube"
        :style="{
            backgroundColor: styles.bg_color,
            margin: `${styles.margin}px`,
            padding: `${styles.padding}px`,
            borderRadius: `${styles.radius}px`
        }"
    >
        <div class="grid" :class="`cols-${content.style}`">
            <div
                v-for="(item, index) in content.data"
                :key="index"
                class="grid-item"
            >
                <img v-if="item.image" :src="item.image" class="w-full h-full object-cover" />
                <div v-else class="empty-image w-full h-full bg-gray-200 flex items-center justify-center text-gray-400">
                    <span class="text-xs">图片</span>
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
.rubik-cube {
    overflow: hidden;
    .grid {
        display: grid;
        gap: 10px;
        &.cols-1 {
            grid-template-columns: repeat(2, 1fr);
        }
        &.cols-2 {
            grid-template-columns: repeat(3, 1fr);
        }
        .grid-item {
            aspect-ratio: 1; // 正方形
            border-radius: 4px;
            overflow: hidden;
        }
    }
}
</style>
