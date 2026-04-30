<template>
    <div class="merchant-apply-content">
        <div class="banner">
            <img src="@/assets/images/default_image.png" alt="" />
            <div class="text">商家入驻申请</div>
        </div>
        <div class="form-preview">
            <div class="item">店铺名称</div>
            <div class="item">联系人</div>
            <div class="item">联系电话</div>
            <div class="btn">立即申请</div>
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
.merchant-apply-content {
    background-color: #fff;
    .banner {
        height: 150px;
        background-color: #f2f2f2;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
        }
        .text {
            position: relative;
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
    }
    .form-preview {
        padding: 15px;
        .item {
            height: 40px;
            line-height: 40px;
            border-bottom: 1px solid #f2f2f2;
            color: #666;
        }
        .btn {
            margin-top: 20px;
            height: 40px;
            line-height: 40px;
            text-align: center;
            background-color: #1989fa;
            color: #fff;
            border-radius: 20px;
        }
    }
}
</style>
