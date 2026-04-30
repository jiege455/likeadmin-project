<template>
    <div class="promotion-apply-content">
        <div class="header">
            <div class="title">成为推广员</div>
            <div class="desc">分享赚佣金，轻松月入过万</div>
        </div>
        <div class="btn">立即申请</div>
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
.promotion-apply-content {
    background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 99%, #fecfef 100%);
    padding: 20px;
    border-radius: 10px;
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    .header {
        .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .desc {
            font-size: 12px;
            opacity: 0.8;
        }
    }
    .btn {
        background-color: #fff;
        color: #ff9a9e;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 14px;
    }
}
</style>
