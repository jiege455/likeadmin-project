<template>
    <div class="notice" :style="{ backgroundColor: styles.bg_color }">
        <div class="notice-icon">
             <span :style="{ color: styles.text_color }">公告</span>
        </div>
        <div class="notice-text" :style="{ color: styles.text_color }">
            这里是系统公告的内容演示
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
.notice {
    display: flex;
    align-items: center;
    padding: 10px 12px;
    border-radius: 4px;
    margin: 10px 0;

    .notice-icon {
        margin-right: 10px;
        font-weight: bold;
    }

    .notice-text {
        flex: 1;
        font-size: 13px;
    }
}
</style>
