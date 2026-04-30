<template>
    <div class="article-publish-content">
        <div class="btn">
            <i class="el-icon-plus"></i>
            <span>发布文章</span>
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
.article-publish-content {
    padding: 15px;
    background-color: #fff;
    .btn {
        height: 44px;
        background-color: #1989fa;
        color: #fff;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        span {
            margin-left: 5px;
        }
    }
}
</style>
