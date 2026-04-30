<template>
    <view class="chat-widget">
        <view class="chat-header">
            <text class="title">公共聊天室</text>
            <text class="count" v-if="content.showOnlineCount">在线: {{ onlineCount }}人</text>
        </view>
        <scroll-view scroll-y class="chat-body" :scroll-top="scrollTop">
            <view
                class="msg-item"
                v-for="(msg, index) in messages"
                :key="index"
                :class="{ self: msg.isSelf }"
            >
                <view class="avatar">
                    <image
                        :src="msg.avatar || '/static/images/default_avatar.png'"
                        mode="aspectFill"
                    />
                </view>
                <view class="content">
                    <view class="name" v-if="!msg.isSelf">{{ msg.name }}</view>
                    <view class="bubble">{{ msg.text }}</view>
                </view>
            </view>
        </scroll-view>
        <view class="chat-footer">
            <input
                class="input"
                v-model="inputText"
                placeholder="说点什么..."
                @confirm="sendMessage"
            />
            <button class="send-btn" @click="sendMessage">发送</button>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'

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

const messages = ref<any[]>([])
const inputText = ref('')
const onlineCount = ref(128)
const scrollTop = ref(0)
let timer: any = null

// 模拟接收消息
const receiveMessage = () => {
    const texts = ['大家好！', '欢迎新人', '今天的活动不错', '有人在吗？']
    const randomText = texts[Math.floor(Math.random() * texts.length)]
    messages.value.push({
        id: Date.now(),
        name: `用户${Math.floor(Math.random() * 1000)}`,
        avatar: '',
        text: randomText,
        isSelf: false
    })
    scrollToBottom()
}

const sendMessage = () => {
    if (!inputText.value.trim()) return
    messages.value.push({
        id: Date.now(),
        name: '我',
        avatar: '',
        text: inputText.value,
        isSelf: true
    })
    inputText.value = ''
    scrollToBottom()
}

const scrollToBottom = () => {
    setTimeout(() => {
        scrollTop.value = messages.value.length * 1000
    }, 100)
}

onMounted(() => {
    // 模拟WebSocket
    messages.value.push({
        id: 1,
        name: '系统',
        avatar: '',
        text: '欢迎来到公共聊天室！',
        isSelf: false
    })

    timer = setInterval(() => {
        if (Math.random() > 0.7) {
            receiveMessage()
        }
    }, 3000)
})

onUnmounted(() => {
    clearInterval(timer)
})
</script>

<style scoped>
.chat-widget {
    background-color: #f5f5f5;
    height: 600rpx;
    display: flex;
    flex-direction: column;
    border-radius: 16rpx;
    overflow: hidden;
    margin: 20rpx;
}
.chat-header {
    height: 80rpx;
    background-color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 20rpx;
    border-bottom: 1rpx solid #eee;
    flex-shrink: 0;
}
.chat-header .title {
    font-weight: bold;
}
.chat-header .count {
    font-size: 24rpx;
    color: #999;
}
.chat-body {
    flex: 1;
    height: 0;
    min-height: 0;
    padding: 20rpx;
    box-sizing: border-box;
}
.msg-item {
    display: flex;
    margin-bottom: 20rpx;
}
.msg-item.self {
    flex-direction: row-reverse;
}
.avatar {
    width: 70rpx;
    height: 70rpx;
    background-color: #ddd;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
}
.avatar image {
    width: 100%;
    height: 100%;
}
.content {
    max-width: 70%;
    margin: 0 20rpx;
}
.content .name {
    font-size: 24rpx;
    color: #999;
    margin-bottom: 6rpx;
}
.msg-item.self .name {
    text-align: right;
}
.content .bubble {
    background-color: #fff;
    padding: 16rpx;
    border-radius: 10rpx;
    font-size: 28rpx;
    line-height: 1.4;
    word-break: break-all;
}
.msg-item.self .bubble {
    background-color: #95ec69;
}
.chat-footer {
    height: 100rpx;
    background-color: #fff;
    display: flex;
    align-items: center;
    padding: 0 20rpx;
    flex-shrink: 0;
}
.input {
    flex: 1;
    height: 70rpx;
    background-color: #f5f5f5;
    border-radius: 8rpx;
    padding: 0 20rpx;
    margin-right: 20rpx;
}
.send-btn {
    width: 120rpx;
    height: 70rpx;
    line-height: 70rpx;
    background-color: #1989fa;
    color: #fff;
    font-size: 28rpx;
    padding: 0;
}
</style>
