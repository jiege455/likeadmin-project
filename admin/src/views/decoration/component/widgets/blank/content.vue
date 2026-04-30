<template>
    <div
        class="blank"
        :style="{
            backgroundColor: styles.bg_color,
            height: `${content.height}px`
        }"
    >
        <div class="text-xs text-gray-400 text-center pt-2">辅助空白</div>
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
