<template>
    <view
        class="w-blank"
        :style="{
            backgroundColor: styles.bg_color,
            height: `${content.height}px`
        }"
    >
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
.w-blank {
    width: 100%;
}
</style>
