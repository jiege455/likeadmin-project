<!--
    PC端标题文字组件
    开发者：杰哥网络科技
    QQ: 2711793818 杰哥
-->
<template>
    <div class="widget-title-text">
        <h3 
            class="title" 
            :style="{ 
                color: content?.text_color || '#333',
                fontSize: (content?.font_size || 18) + 'px',
                textAlign: content?.align || 'left'
            }"
        >
            {{ content?.title || '' }}
        </h3>
    </div>
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
.widget-title-text {
    width: 100%;
    max-width: 1200px;
    margin: 15px auto;
    padding: 0 20px;
    
    .title {
        margin: 0;
        font-weight: 600;
    }
}
</style>
