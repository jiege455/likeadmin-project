<!--
    PC端分割线组件
    开发者：杰哥网络科技
    QQ: 2711793818 杰哥
-->
<template>
    <div class="widget-separate-line">
        <ElDivider :style="{ borderColor: content?.line_color || '#eee' }">
            {{ content?.text || '' }}
        </ElDivider>
    </div>
</template>

<script setup lang="ts">
import { ElDivider } from 'element-plus'

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
.widget-separate-line {
    width: 100%;
    max-width: 1200px;
    margin: 10px auto;
}
</style>
