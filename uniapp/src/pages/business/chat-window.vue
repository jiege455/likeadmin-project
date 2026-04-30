<template>
    <view class="chat-window-page">
        <uni-nav :title="pageTitle">
            <template #right>
                <view class="w-[60px]"></view>
            </template>
        </uni-nav>

        <ChatWindow
            :room-id="roomId"
            :room-name="roomName"
            :conversation-id="conversationId"
            :target-name="targetName"
            :is-private="isPrivate"
            :merchant-id="merchantId"
        />
    </view>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { onShow, onLoad } from '@dcloudio/uni-app'
import { useThemeStore } from '@/stores/theme'
import { useUserStore } from '@/stores/user'
import { createConversation, markConversationRead } from '@/api/chat'
import ChatWindow from '@/components/business/ChatWindow.vue'

// Props 类型定义
interface ChatWindowPageProps {
    roomId?: string
    roomName?: string
    conversationId?: string
    targetName?: string
    isPrivate?: boolean
    merchantId?: number
}

const props = withDefaults(defineProps<ChatWindowPageProps>(), {
    roomId: 'public',
    roomName: '公共频道',
    conversationId: '',
    targetName: '',
    isPrivate: false,
    merchantId: 0
})

const themeStore = useThemeStore()
const userStore = useUserStore()

const roomId = ref(props.roomId)
const roomName = ref(props.roomName)
const conversationId = ref(props.conversationId)
const targetName = ref(props.targetName)
const isPrivate = ref(props.isPrivate)
const merchantId = ref(props.merchantId)
const isInitialized = ref(false)

const pageTitle = computed(() => {
    if (isPrivate.value && targetName.value) {
        return targetName.value
    }
    return roomName.value
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

const initPrivateConversation = async () => {
    if (!merchantId.value || conversationId.value || !userStore.isLogin) {
        return
    }

    try {
        const res = await createConversation({
            target_id: merchantId.value,
            target_type: 1
        })
        isPrivate.value = true
        conversationId.value = res.conversation_id
        roomId.value = res.conversation_id
        if (res.target_info?.name) {
            targetName.value = res.target_info.name
        }
    } catch (e: any) {
        console.error('创建会话失败:', e)
        uni.showToast({
            title: e.message || '创建会话失败',
            icon: 'none'
        })
    }
}

watch(
    () => userStore.isLogin,
    (isLogin) => {
        if (isLogin && merchantId.value && !conversationId.value) {
            initPrivateConversation()
        }
    }
)

onLoad(async (options: any) => {
    if (options.room_id) {
        roomId.value = options.room_id
    }
    if (options.room_name) {
        roomName.value = decodeURIComponent(options.room_name)
    }
    if (options.is_private === '1') {
        isPrivate.value = true
        if (options.conversation_id) {
            conversationId.value = options.conversation_id
            roomId.value = options.conversation_id
        }
        if (options.target_name) {
            targetName.value = decodeURIComponent(options.target_name)
        }
    }
    if (options.merchant_id && !conversationId.value) {
        merchantId.value = parseInt(options.merchant_id)
        if (userStore.isLogin) {
            await initPrivateConversation()
        }
    }
    isInitialized.value = true
})

onShow(() => {
    themeStore.getTheme()

    if (isPrivate.value && conversationId.value) {
        markConversationRead({
            conversation_id: conversationId.value
        }).catch(() => {})
    }
})
</script>

<style lang="scss" scoped>
.chat-window-page {
    height: 100vh;
    display: flex;
    flex-direction: column;
    background-color: #f5f5f5;
}
</style>
