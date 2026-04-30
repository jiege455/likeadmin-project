<template>
    <div
        class="title-text"
        :style="{
            backgroundColor: styles.bg_color,
            textAlign: content.align,
            padding: `${styles.padding}px`
        }"
    >
        <div class="title" :style="{ color: styles.title_color }">{{ content.title }}</div>
        <div class="desc" :style="{ color: styles.desc_color }" v-if="content.desc">{{ content.desc }}</div>
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
.title-text {
    .title {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 4px;
    }
    .desc {
        font-size: 12px;
    }
}
</style>
