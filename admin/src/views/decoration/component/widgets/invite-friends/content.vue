<template>
    <div class="invite-friends-content">
        <div class="bg">
            <div class="title">邀请好友</div>
            <div class="code">我的邀请码：ABC1234</div>
            <div class="btn">立即邀请</div>
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
.invite-friends-content {
    background-color: #fff;
    padding: 15px;
    .bg {
        height: 120px;
        background-color: #f8f8f8;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        .title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .code {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
        }
        .btn {
            padding: 5px 20px;
            background-color: #333;
            color: #fff;
            border-radius: 15px;
            font-size: 12px;
        }
    }
}
</style>
