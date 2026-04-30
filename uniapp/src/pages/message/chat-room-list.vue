<template>
    <uni-nav title="公共聊天室"></uni-nav>
    <view class="chat-room-list">
        <view class="room-list" v-if="chatRooms.length > 0">
            <view
                class="room-item"
                v-for="room in chatRooms"
                :key="room.id"
                :style="{
                    background: `linear-gradient(135deg, ${primaryColor} 0%, ${
                        minorColor || primaryColor
                    } 100%)`
                }"
                @click="goChatRoom(room)"
            >
                <view class="room-left">
                    <view class="room-icon">
                        <text class="icon-text">💬</text>
                    </view>
                    <view class="room-info">
                        <view class="room-name">{{ room.name }}</view>
                        <view class="room-desc">{{
                            room.description || '和大家一起聊天交流'
                        }}</view>
                    </view>
                </view>
                <view class="room-right">
                    <view class="enter-icon">›</view>
                </view>
            </view>
        </view>

        <view class="empty" v-else-if="!loading">
            <image class="empty-img" src="/static/images/empty.png" mode="aspectFit" />
            <text class="empty-text">暂无聊天室</text>
        </view>

        <view class="loading" v-if="loading">
            <text>加载中...</text>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { getChatRoomList } from '@/api/chat'
import { useThemeStore } from '@/stores/theme'

const themeStore = useThemeStore()
const primaryColor = ref('#007AFF')
const minorColor = ref('')

interface ChatRoom {
    id: number
    name: string
    room_id: string
    description: string
}

const chatRooms = ref<ChatRoom[]>([])
const loading = ref(false)

const loadChatRooms = async () => {
    loading.value = true
    try {
        const res = await getChatRoomList()
        chatRooms.value = res || []
    } catch (e) {
        console.error('获取聊天室列表失败:', e)
    } finally {
        loading.value = false
    }
}

const goChatRoom = (room: ChatRoom) => {
    uni.navigateTo({
        url: `/pages/business/chat-window?room_id=${room.room_id}&room_name=${encodeURIComponent(
            room.name
        )}`
    })
}

onMounted(() => {
    primaryColor.value = themeStore.primaryColor || '#007AFF'
    minorColor.value = themeStore.minorColor || ''
    loadChatRooms()
})

const goBack = () => {
    uni.navigateBack({
        fail: () => {
            uni.switchTab({
                url: '/pages/index/index'
            })
        }
    })
}
</script>

<style lang="scss" scoped>
.chat-room-list {
    min-height: 100vh;
    background-color: #f5f5f5;
}

.header {
    padding: 30rpx;
    background-color: #fff;
    border-bottom: 1rpx solid #eee;
}

.title {
    font-size: 36rpx;
    font-weight: bold;
    color: #333;
}

.room-list {
    padding: 20rpx;
}

.room-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24rpx;
    border-radius: 16rpx;
    margin-bottom: 16rpx;
    box-shadow: 0 4rpx 12rpx rgba(0, 0, 0, 0.1);

    &:active {
        transform: scale(0.98);
    }
}

.room-left {
    display: flex;
    align-items: center;
}

.room-icon {
    width: 80rpx;
    height: 80rpx;
    background-color: rgba(255, 255, 255, 0.25);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 20rpx;
}

.icon-text {
    font-size: 40rpx;
}

.room-info {
    color: #fff;
}

.room-name {
    font-size: 32rpx;
    font-weight: bold;
    margin-bottom: 8rpx;
}

.room-desc {
    font-size: 24rpx;
    opacity: 0.85;
}

.room-right {
    display: flex;
    align-items: center;
}

.enter-icon {
    font-size: 48rpx;
    color: rgba(255, 255, 255, 0.85);
    font-weight: 300;
}

.empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 200rpx 0;
}

.empty-img {
    width: 200rpx;
    height: 200rpx;
    margin-bottom: 30rpx;
}

.empty-text {
    font-size: 32rpx;
    color: #999;
}

.loading {
    display: flex;
    justify-content: center;
    padding: 40rpx;
    color: #999;
    font-size: 28rpx;
}
</style>
