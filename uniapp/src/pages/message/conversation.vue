<template>
    <uni-nav title="私聊"></uni-nav>
    <view class="conversation-page">
        <view class="conversation-list" v-if="conversationList.length > 0">
            <view
                class="conversation-item"
                v-for="item in conversationList"
                :key="item.id"
                @click="handleOpenChat(item)"
            >
                <view class="avatar-wrap">
                    <image
                        class="avatar"
                        :src="item.target_info?.logo || defaultAvatar"
                        mode="aspectFill"
                    />
                    <view class="unread-badge" v-if="item.unread_count > 0">
                        {{ item.unread_count > 99 ? '99+' : item.unread_count }}
                    </view>
                </view>
                <view class="content">
                    <view class="top-row">
                        <text class="name">{{ item.target_info?.name || '未知' }}</text>
                        <text class="time">{{ formatTime(item.last_message_time) }}</text>
                    </view>
                    <view class="bottom-row">
                        <text class="last-message">{{ item.last_message || '暂无消息' }}</text>
                    </view>
                </view>
            </view>
        </view>

        <view class="empty" v-else-if="!loading">
            <image class="empty-img" src="/static/images/empty.png" mode="aspectFit" />
            <text class="empty-text">暂无私聊记录</text>
            <text class="empty-tip">去商家主页点击"私聊"开始对话吧</text>
        </view>

        <view class="loading" v-if="loading">
            <text>加载中...</text>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { getConversationList } from '@/api/chat'
import { useUserStore } from '@/stores/user'
import { useThemeStore } from '@/stores/theme'

const themeStore = useThemeStore()
const defaultAvatar = '/static/images/user/default_avatar.png'

interface ConversationItem {
    id: number
    conversation_id: string
    target_id: number
    target_type: number
    target_info: {
        id: number
        name: string
        logo: string
        type: string
    }
    last_message: string
    last_message_time: string
    unread_count: number
}

const conversationList = ref<ConversationItem[]>([])
const loading = ref(false)
const userStore = useUserStore()

onMounted(() => {
    if (userStore.isLogin) {
        loadConversationList()
    }
})

const loadConversationList = async () => {
    loading.value = true
    try {
        const res = await getConversationList()
        conversationList.value = res.lists || []
    } catch (e) {
        console.error('加载会话列表失败:', e)
    } finally {
        loading.value = false
    }
}

const handleOpenChat = (item: ConversationItem) => {
    uni.navigateTo({
        url: `/pages/business/chat-window?conversation_id=${
            item.conversation_id
        }&target_name=${encodeURIComponent(item.target_info?.name || '')}&is_private=1`
    })
}

const formatTime = (time: string): string => {
    if (!time) return ''

    const date = new Date(time)
    const now = new Date()
    const diff = now.getTime() - date.getTime()

    if (diff < 60000) {
        return '刚刚'
    } else if (diff < 3600000) {
        return Math.floor(diff / 60000) + '分钟前'
    } else if (diff < 86400000) {
        return Math.floor(diff / 3600000) + '小时前'
    } else if (diff < 604800000) {
        return Math.floor(diff / 86400000) + '天前'
    } else {
        return time.substring(0, 10)
    }
}

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
.conversation-page {
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

.conversation-list {
    background-color: #fff;
}

.conversation-item {
    display: flex;
    padding: 24rpx 30rpx;
    background-color: #fff;
    border-bottom: 1rpx solid #f5f5f5;

    &:active {
        background-color: #f9f9f9;
    }
}

.avatar-wrap {
    position: relative;
    margin-right: 24rpx;
}

.avatar {
    width: 100rpx;
    height: 100rpx;
    border-radius: 50%;
    background-color: #eee;
}

.unread-badge {
    position: absolute;
    top: -8rpx;
    right: -8rpx;
    min-width: 32rpx;
    height: 32rpx;
    line-height: 32rpx;
    padding: 0 8rpx;
    background-color: #ff4d4f;
    color: #fff;
    font-size: 20rpx;
    border-radius: 16rpx;
    text-align: center;
}

.content {
    flex: 1;
    min-width: 0;
}

.top-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12rpx;
}

.name {
    font-size: 32rpx;
    font-weight: 500;
    color: #333;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 400rpx;
}

.time {
    font-size: 24rpx;
    color: #999;
}

.bottom-row {
    display: flex;
    align-items: center;
}

.last-message {
    font-size: 28rpx;
    color: #999;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
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
    margin-bottom: 16rpx;
}

.empty-tip {
    font-size: 26rpx;
    color: #bbb;
}

.loading {
    display: flex;
    justify-content: center;
    padding: 40rpx;
    color: #999;
    font-size: 28rpx;
}
</style>
