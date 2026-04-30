<template>
    <view class="w-separate-line" :style="{ backgroundColor: styles.bg_color }">
        <view
            class="line"
            :style="{
                borderBottomColor: styles.line_color,
                borderBottomStyle: content.style
            }"
        ></view>
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
.w-separate-line {
    padding: 20rpx 0;
    width: 100%;
    .line {
        border-bottom-width: 1px;
        width: 100%;
    }
}
</style>
