<template>
    <div class="chat-window-content">
        <div class="header">公共频道</div>
        <div class="body">
            <div class="msg left">
                <div class="avatar"></div>
                <div class="bubble">大家好！</div>
            </div>
            <div class="msg right">
                <div class="bubble">你好呀~</div>
                <div class="avatar"></div>
            </div>
        </div>
        <div class="footer">
            <div class="input">请输入消息...</div>
            <div class="btn">发送</div>
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
.chat-window-content {
    background-color: #f5f5f5;
    height: 300px;
    display: flex;
    flex-direction: column;
    .header {
        height: 40px;
        line-height: 40px;
        text-align: center;
        background-color: #fff;
        border-bottom: 1px solid #eee;
    }
    .body {
        flex: 1;
        padding: 15px;
        .msg {
            display: flex;
            margin-bottom: 10px;
            align-items: flex-start;
            .avatar {
                width: 30px;
                height: 30px;
                background-color: #ccc;
                border-radius: 50%;
            }
            .bubble {
                padding: 8px 12px;
                background-color: #fff;
                border-radius: 4px;
                font-size: 14px;
                max-width: 70%;
            }
            &.left {
                .bubble {
                    margin-left: 10px;
                }
            }
            &.right {
                flex-direction: row-reverse;
                .bubble {
                    margin-right: 10px;
                    background-color: #1989fa;
                    color: #fff;
                }
            }
        }
    }
    .footer {
        height: 50px;
        background-color: #fff;
        display: flex;
        align-items: center;
        padding: 0 10px;
        .input {
            flex: 1;
            height: 36px;
            line-height: 36px;
            background-color: #f5f5f5;
            padding: 0 10px;
            border-radius: 18px;
            color: #999;
            font-size: 14px;
        }
        .btn {
            width: 60px;
            height: 36px;
            line-height: 36px;
            text-align: center;
            background-color: #1989fa;
            color: #fff;
            border-radius: 18px;
            margin-left: 10px;
            font-size: 14px;
        }
    }
}
</style>
