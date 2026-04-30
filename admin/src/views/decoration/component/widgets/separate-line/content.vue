<template>
    <div class="separate-line">
        <div
            class="line"
            :style="{
                borderBottomColor: styles.line_color,
                borderBottomStyle: content.style
            }"
        ></div>
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
.separate-line {
    padding: 10px 0;
    .line {
        border-bottom-width: 1px;
    }
}
</style>
