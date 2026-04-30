<template>
    <div class="article-detail-content">
        <div class="title">文章标题占位符</div>
        <div class="info">
            <span>2023-01-01</span>
            <span>阅读 999+</span>
        </div>
        <div class="content">
            这里是文章内容预览区域...
        </div>
    </div>
</template>

<script lang="ts" setup>
import type { PropType } from 'vue'
import type { optionsType } from './options'

defineProps({
    content: {
        type: Object as PropType<optionsType>,
        default: () => ({})
    },
    styles: {
        type: Object as PropType<any>,
        default: () => ({})
    }
})
</script>

<style lang="scss" scoped>
.article-detail-content {
    padding: 15px;
    background-color: #fff;
    .title {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 10px;
    }
    .info {
        font-size: 12px;
        color: #999;
        margin-bottom: 15px;
        span {
            margin-right: 15px;
        }
    }
    .content {
        color: #666;
        line-height: 1.6;
    }
}
</style>
