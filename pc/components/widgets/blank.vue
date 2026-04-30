<!--
    PC端空白占位组件
    开发者：杰哥网络科技
    QQ: 2711793818 杰哥
-->
<template>
    <div class="widget-blank" :style="{ height: content?.height || '20px' }"></div>
</template>

<script setup lang="ts">
const props = defineProps({
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
.widget-blank {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
}
</style>
