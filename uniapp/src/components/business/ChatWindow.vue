<template>
    <!--
  开发者公众号：杰哥网络科技
  QQ: 2711793818 杰哥
  聊天窗口组件
-->
    <view class="chat-window">
        <!-- 加载中状态 -->
        <view v-if="!initialized" class="loading-container">
            <u-loading mode="circle" size="40"></u-loading>
            <text class="loading-text">加载中...</text>
        </view>
        <template v-else>
            <!-- 聊天公告 -->
            <view class="chat-notice" v-if="chatNotice && !isPrivate">
                <view class="notice-content">
                    <text class="notice-icon">📢</text>
                    <text class="notice-text">{{ chatNotice }}</text>
                </view>
            </view>

            <!-- 顶部状态栏 -->
            <view class="status-bar" v-if="!isPrivate">
                <view class="online-info">
                    <view class="online-dot" :class="{ active: isConnected }"></view>
                    <text class="online-text">{{ isConnected ? '已连接' : '未连接' }}</text>
                    <text class="online-count" v-if="showOnlineCount && onlineCount > 0"
                        >{{ onlineCount }}人在线</text
                    >
                </view>
            </view>

            <!-- 未登录提示 -->
            <view class="login-tip" v-if="!isLoggedIn">
                <view class="login-tip-content">
                    <text class="login-tip-text">登录后才能参与聊天</text>
                    <button class="login-btn" @click="goLogin">立即登录</button>
                </view>
            </view>

            <!-- 聊天功能已关闭提示 -->
            <view class="chat-disabled-tip" v-if="!chatEnabled && !isPrivate">
                <text>聊天功能已关闭</text>
            </view>

            <!-- 消息列表 -->
            <scroll-view
                class="message-list"
                scroll-y
                :scroll-top="scrollTop"
                @scrolltoupper="loadMoreMessages"
            >
                <view class="loading-tip" v-if="isLoading">
                    <text>加载中...</text>
                </view>

                <view
                    class="message-item"
                    v-for="msg in messages"
                    :key="msg.id"
                    :class="{ 'message-mine': String(msg.user_id) === String(currentUserId) }"
                >
                    <view class="avatar">
                        <image
                            class="avatar-img"
                            :src="msg.avatar || '/static/images/user/default_avatar.png'"
                            mode="aspectFill"
                        />
                    </view>
                    <view class="content">
                        <view class="nickname" v-if="!isPrivate">{{ msg.nickname }}</view>
                        <view class="bubble">{{ msg.content }}</view>
                        <view class="time">{{ msg.create_time }}</view>
                    </view>
                </view>

                <view class="empty-tip" v-if="!isLoading && messages.length === 0">
                    <text>暂无消息，快来发送第一条消息吧~</text>
                </view>
            </scroll-view>

            <!-- 底部输入框 -->
            <view class="input-area" :style="{ paddingBottom: safeAreaBottom + 'px' }">
                <view class="input-wrapper">
                    <input
                        class="message-input"
                        v-model="inputValue"
                        :placeholder="inputPlaceholder"
                        :adjust-position="true"
                        confirm-type="send"
                        :maxlength="maxLength"
                        @confirm="handleSend"
                    />
                    <button
                        class="send-btn"
                        :disabled="
                            !inputValue.trim() ||
                            !isConnected ||
                            !isLoggedIn ||
                            (!chatEnabled && !isPrivate)
                        "
                        @click="handleSend"
                    >
                        发送
                    </button>
                </view>
            </view>
        </template>
    </view>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, nextTick, computed, watch } from 'vue'
import { useUserStore } from '@/stores/user'
import {
    getChatMessageList,
    getChatConfig,
    getChatSetting,
    getPrivateMessageList
} from '@/api/chat'
import { TOKEN_KEY } from '@/enums/constantEnums'
import cache from '@/utils/cache'
import WebSocketManager from '@/utils/websocket'

interface Message {
    id: string | number
    user_id: string | number
    nickname: string
    avatar: string
    content: string
    msg_type?: number
    create_time: string
}

const props = defineProps<{
    roomId?: string
    roomName?: string
    conversationId?: string
    targetName?: string
    isPrivate?: boolean
    merchantId?: number
}>()

const userStore = useUserStore()
const currentUserId = computed(() => {
    return userStore.userInfo?.id || 0
})
const isLoggedIn = computed(() => !!userStore.token)

const currentRoomId = computed(() => props.roomId || 'public')

const messages = ref<Message[]>([])
const inputValue = ref('')
const scrollTop = ref(0)
const isLoading = ref(false)
const isConnected = ref(false)
const onlineCount = ref(0)
const safeAreaBottom = ref(0)
const pageNo = ref(1)
const pageSize = ref(20)
const hasMore = ref(true)
const initialized = ref(false)

const chatEnabled = ref(true)
const chatNotice = ref('')
const maxLength = ref(500)
const messageInterval = ref(1)
const showOnlineCount = ref(true)
const lastSendTime = ref(0)

const isPrivate = computed(() => props.isPrivate === true)

const inputPlaceholder = computed(() => {
    if (!isPrivate.value && !chatEnabled.value) return '聊天功能已关闭'
    if (!isLoggedIn.value) return '请先登录'
    return '请输入消息...'
})

let wsManager: WebSocketManager | null = null

onMounted(() => {
    const systemInfo = uni.getSystemInfoSync()
    safeAreaBottom.value = systemInfo.safeAreaInsets?.bottom || 0

    // 确保用户信息已加载
    if (userStore.isLogin && (!userStore.userInfo || !userStore.userInfo.id)) {
        userStore.getUser().catch((e) => {
            console.error('获取用户信息失败:', e)
        })
    }

    if (isPrivate.value) {
        loadPrivateMessages()
        initWebSocket()
        initialized.value = true
    } else {
        loadChatSetting()
        loadHistoryMessages()
    }
})

onUnmounted(() => {
    if (wsManager) {
        wsManager.disconnect()
        wsManager = null
    }
})

watch(
    () => props.roomId,
    (newVal, oldVal) => {
        if (newVal && newVal !== oldVal) {
            messages.value = []
            pageNo.value = 1
            hasMore.value = true

            if (isPrivate.value) {
                loadPrivateMessages()
            } else {
                loadHistoryMessages()
            }

            if (wsManager) {
                wsManager.disconnect()
                wsManager = null
            }
            initWebSocket()
        }
    }
)

watch(
    () => props.isPrivate,
    (newVal) => {
        if (newVal) {
            messages.value = []
            pageNo.value = 1
            hasMore.value = true
            loadPrivateMessages()
        }
    }
)

watch(
    () => props.conversationId,
    (newVal) => {
        if (newVal && isPrivate.value) {
            if (wsManager) {
                wsManager.disconnect()
                wsManager = null
            }
            initWebSocket()
        }
    }
)

const loadChatSetting = async () => {
    try {
        const res = await getChatSetting()
        chatEnabled.value = res.chat_enabled ?? true
        chatNotice.value = res.chat_notice ?? ''
        maxLength.value = res.max_message_length ?? 500
        messageInterval.value = res.message_interval ?? 1
        showOnlineCount.value = res.show_online_count ?? true

        if (chatEnabled.value) {
            initWebSocket()
        }
    } catch (e) {
        console.error('获取聊天设置失败:', e)
        initWebSocket()
    } finally {
        initialized.value = true
    }
}

const goLogin = () => {
    uni.navigateTo({
        url: '/pages/login/login'
    })
}

const loadHistoryMessages = async () => {
    if (isLoading.value || !hasMore.value) return

    isLoading.value = true
    try {
        const res = await getChatMessageList({
            room_id: currentRoomId.value,
            page_no: pageNo.value,
            page_size: pageSize.value
        })

        if (res.lists && res.lists.length > 0) {
            messages.value = [...res.lists, ...messages.value]
            pageNo.value++

            if (res.lists.length < pageSize.value) {
                hasMore.value = false
            }
        } else {
            hasMore.value = false
        }

        nextTick(() => {
            scrollToBottom()
        })
    } catch (e) {
        console.error('加载消息失败:', e)
    } finally {
        isLoading.value = false
    }
}

const loadPrivateMessages = async () => {
    if (isLoading.value || !hasMore.value) return
    if (!props.conversationId) return

    isLoading.value = true
    try {
        const res = await getPrivateMessageList({
            conversation_id: props.conversationId,
            page_no: pageNo.value,
            page_size: pageSize.value
        })

        if (res.lists && res.lists.length > 0) {
            messages.value = [...res.lists, ...messages.value]
            pageNo.value++

            if (res.lists.length < pageSize.value) {
                hasMore.value = false
            }
        } else {
            hasMore.value = false
        }

        nextTick(() => {
            scrollToBottom()
        })
    } catch (e) {
        console.error('加载私聊消息失败:', e)
    } finally {
        isLoading.value = false
    }
}

const loadMoreMessages = () => {
    if (!isLoading.value && hasMore.value) {
        if (isPrivate.value) {
            loadPrivateMessages()
        } else {
            loadHistoryMessages()
        }
    }
}

const initWebSocket = async () => {
    try {
        const configRes = await getChatConfig()
        const wsUrl = configRes.ws_url || 'ws://127.0.0.1:8282'

        wsManager = new WebSocketManager({
            url: wsUrl,
            reconnect: true,
            reconnectInterval: 3000,
            heartbeatInterval: 30000,
            onOpen: () => {
                isConnected.value = true
                const token = cache.get(TOKEN_KEY) || userStore.token
                if (token && wsManager) {
                    if (isPrivate.value && props.conversationId) {
                        wsManager.send({
                            type: 'login_private',
                            token: token,
                            conversation_id: props.conversationId
                        })
                    } else {
                        wsManager.send({
                            type: 'login',
                            token: token,
                            room_id: currentRoomId.value
                        })
                    }
                }
            },
            onMessage: (data: any) => {
                handleWebSocketMessage(data)
            },
            onClose: () => {
                isConnected.value = false
            },
            onError: () => {
                isConnected.value = false
            }
        })

        wsManager.connect()
    } catch (e) {
        console.error('初始化 WebSocket 失败:', e)
    }
}

const handleWebSocketMessage = (data: any) => {
    if (isPrivate.value) {
        if (data.conversation_id && data.conversation_id !== props.conversationId) {
            return
        }
    } else {
        if (data.room_id && data.room_id !== currentRoomId.value) {
            return
        }
    }

    switch (data.type) {
        case 'init':
            console.log('WebSocket初始化成功')
            break
        case 'login_success':
            console.log('登录聊天室成功')
            onlineCount.value = data.online_count || 0
            break
        case 'login_private_success':
            console.log('登录私聊成功')
            break
        case 'message':
        case 'private_message':
            const newMsg: Message = {
                id: data.id,
                user_id: data.user_id,
                nickname: data.nickname,
                avatar: data.avatar,
                content: data.content,
                msg_type: data.msg_type,
                create_time: data.create_time
            }
            messages.value.push(newMsg)
            nextTick(() => {
                scrollToBottom()
            })
            break
        case 'online':
            onlineCount.value = data.count || 0
            if (data.msg) {
                uni.showToast({
                    title: data.msg,
                    icon: 'none',
                    duration: 1500
                })
            }
            break
        case 'error':
            uni.showToast({
                title: data.msg || '发生错误',
                icon: 'none'
            })
            break
    }
}

const handleSend = () => {
    const content = inputValue.value.trim()
    if (!content) return

    if (!isPrivate.value && !chatEnabled.value) {
        uni.showToast({
            title: '聊天功能已关闭',
            icon: 'none'
        })
        return
    }

    if (!isLoggedIn.value) {
        uni.showModal({
            title: '提示',
            content: '请先登录后再发送消息',
            confirmText: '去登录',
            success: (res) => {
                if (res.confirm) {
                    goLogin()
                }
            }
        })
        return
    }

    if (!isConnected.value || !wsManager) {
        uni.showToast({
            title: '未连接到服务器',
            icon: 'none'
        })
        return
    }

    const now = Date.now()
    const interval = messageInterval.value * 1000
    if (now - lastSendTime.value < interval) {
        uni.showToast({
            title: `发送太频繁，请${messageInterval.value}秒后再试`,
            icon: 'none'
        })
        return
    }

    let success = false

    if (isPrivate.value && props.conversationId) {
        success = wsManager.send({
            type: 'send_private',
            content: content,
            msg_type: 1,
            conversation_id: props.conversationId
        })
    } else {
        success = wsManager.send({
            type: 'send',
            content: content,
            msg_type: 1,
            room_id: currentRoomId.value
        })
    }

    if (success) {
        inputValue.value = ''
        lastSendTime.value = now
    }
}

const scrollToBottom = () => {
    const query = uni.createSelectorQuery()
    query.select('.message-list').boundingClientRect()
    query.exec((res) => {
        if (res[0]) {
            scrollTop.value = 999999
        }
    })
}
</script>

<style lang="scss" scoped>
.chat-window {
    display: flex;
    flex-direction: column;
    height: 100%;
    background-color: #f5f5f5;
}

.loading-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;

    .loading-text {
        margin-top: 20rpx;
        font-size: 28rpx;
        color: #999;
    }
}

.chat-notice {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeeba 100%);
    padding: 16rpx 20rpx;
    border-bottom: 1rpx solid #ffc107;
}

.notice-content {
    display: flex;
    align-items: center;
}

.notice-icon {
    font-size: 28rpx;
    margin-right: 10rpx;
}

.notice-text {
    font-size: 24rpx;
    color: #856404;
    flex: 1;
}

.status-bar {
    padding: 10rpx 20rpx;
    background-color: #fff;
    border-bottom: 1rpx solid #eee;
}

.online-info {
    display: flex;
    align-items: center;
    font-size: 24rpx;
    color: #999;
}

.online-dot {
    width: 16rpx;
    height: 16rpx;
    border-radius: 50%;
    background-color: #ccc;
    margin-right: 10rpx;

    &.active {
        background-color: #52c41a;
    }
}

.online-text {
    margin-right: 20rpx;
}

.online-count {
    color: #1890ff;
}

.login-tip {
    background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
    padding: 20rpx;
}

.login-tip-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.login-tip-text {
    color: #fff;
    font-size: 28rpx;
}

.login-btn {
    background-color: #fff;
    color: #ff6b6b;
    font-size: 26rpx;
    padding: 10rpx 30rpx;
    border-radius: 30rpx;
    border: none;
}

.chat-disabled-tip {
    text-align: center;
    padding: 20rpx;
    background-color: #f5f5f5;
    color: #999;
    font-size: 28rpx;
}

.message-list {
    flex: 1;
    padding: 20rpx;
    padding-bottom: calc(120rpx + env(safe-area-inset-bottom));
}

.loading-tip {
    text-align: center;
    padding: 20rpx;
    color: #999;
    font-size: 24rpx;
}

.empty-tip {
    text-align: center;
    padding: 100rpx 0;
    color: #999;
    font-size: 28rpx;
}

.message-item {
    display: flex;
    margin-bottom: 30rpx;

    .avatar {
        flex-shrink: 0;
        margin-right: 20rpx;
    }

    .avatar-img {
        width: 80rpx;
        height: 80rpx;
        border-radius: 50%;
        background-color: #eee;
    }

    .content {
        max-width: 70%;
    }

    .nickname {
        font-size: 24rpx;
        color: #999;
        margin-bottom: 8rpx;
    }

    .bubble {
        background-color: #fff;
        padding: 16rpx 24rpx;
        border-radius: 16rpx;
        font-size: 28rpx;
        line-height: 1.5;
        word-break: break-all;
        box-shadow: 0 2rpx 6rpx rgba(0, 0, 0, 0.05);
    }

    .time {
        font-size: 22rpx;
        color: #bbb;
        margin-top: 8rpx;
        text-align: left;
    }
}

.message-mine {
    flex-direction: row-reverse;

    .avatar {
        margin-right: 0;
        margin-left: 20rpx;
    }

    .content {
        text-align: right;
    }

    .nickname {
        text-align: right;
    }

    .bubble {
        background-color: #1890ff;
        color: #fff;
        text-align: left;
    }

    .time {
        text-align: right;
    }
}

.input-area {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: #fff;
    border-top: 1rpx solid #eee;
    padding: 20rpx;
    padding-bottom: calc(20rpx + env(safe-area-inset-bottom));
    z-index: 100;
}

.input-wrapper {
    display: flex;
    align-items: center;
}

.message-input {
    flex: 1;
    height: 72rpx;
    padding: 0 24rpx;
    background-color: #f5f5f5;
    border-radius: 36rpx;
    font-size: 28rpx;
}

.send-btn {
    margin-left: 20rpx;
    width: 140rpx;
    height: 72rpx;
    line-height: 72rpx;
    background-color: #1890ff;
    color: #fff;
    font-size: 28rpx;
    border-radius: 36rpx;
    border: none;

    &[disabled] {
        background-color: #ccc;
    }
}
</style>
