<template>
    <view class="message-center flex flex-col h-screen bg-f5" :style="themeStyle">
        <!-- 消息入口区域 -->
        <view class="message-entries">
            <!-- 私聊消息入口 -->
            <view class="entry-item" @click="goPrivateChat">
                <view class="entry-icon private-icon">
                    <text class="icon-emoji">💬</text>
                </view>
                <view class="entry-content">
                    <view class="entry-title">私聊消息</view>
                    <view class="entry-desc">{{
                        privateUnread > 0 ? `有${privateUnread}条未读消息` : '暂无新消息'
                    }}</view>
                </view>
                <view class="entry-right">
                    <view class="unread-badge" v-if="privateUnread > 0">
                        {{ privateUnread > 99 ? '99+' : privateUnread }}
                    </view>
                    <text class="arrow">›</text>
                </view>
            </view>

            <!-- 聊天室入口 -->
            <view class="entry-item" @click="goChatRoomList">
                <view class="entry-icon chat-icon">
                    <text class="icon-emoji">👥</text>
                </view>
                <view class="entry-content">
                    <view class="entry-title">公共聊天室</view>
                    <view class="entry-desc">和大家一起聊天交流</view>
                </view>
                <view class="entry-right">
                    <text class="arrow">›</text>
                </view>
            </view>
        </view>

        <!-- 系统公告 -->
        <view class="flex-1 min-h-0">
            <z-paging ref="paging" v-model="dataList" @query="queryList" :fixed="false">
                <view class="p-3">
                    <view class="flex justify-between items-center mb-3">
                        <view class="section-title">系统公告</view>
                        <view
                            class="text-sm px-3 py-1 rounded-full"
                            :style="{
                                color: themeStore.primaryColor,
                                backgroundColor: themeStore.primaryColor + '15'
                            }"
                            @click="handleMarkAllRead"
                            v-if="noticeUnread > 0"
                        >
                            全部已读
                        </view>
                    </view>
                    <view
                        v-for="(item, index) in dataList"
                        :key="index"
                        class="bg-white rounded-lg p-4 mb-3 shadow-sm"
                        @click="goDetail(item)"
                    >
                        <view class="flex justify-between items-center mb-2">
                            <view class="flex items-center">
                                <view
                                    class="w-2 h-2 rounded-full bg-red-500 mr-2"
                                    v-if="!item.is_read"
                                ></view>
                                <view class="w-2 h-2 rounded-full bg-gray-300 mr-2" v-else></view>
                                <text class="font-bold text-base text-gray-800">{{
                                    item.title
                                }}</text>
                            </view>
                            <view class="flex items-center">
                                <view
                                    v-if="item.type == 2"
                                    class="text-xs px-2 py-0.5 rounded mr-2 text-white"
                                    :style="{ backgroundColor: themeStore.primaryColor }"
                                >
                                    重要
                                </view>
                                <view
                                    v-else-if="item.type == 3"
                                    class="text-xs px-2 py-0.5 rounded mr-2 bg-orange-500 text-white"
                                >
                                    活动
                                </view>
                                <text class="text-xs text-gray-400">{{ item.create_time }}</text>
                            </view>
                        </view>
                        <view class="text-sm text-gray-600 leading-normal line-clamp-2">
                            {{ item.content }}
                        </view>
                        <view class="flex justify-end items-center mt-2">
                            <view class="text-xs" :style="{ color: themeStore.primaryColor }">
                                查看详情 >
                            </view>
                        </view>
                    </view>
                </view>
            </z-paging>
        </view>

        <tabbar />
    </view>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { getNoticeList, getNoticeUnreadCount, markAllNoticeRead } from '@/api/notice'
import { getUnreadTotal, getChatRoomList } from '@/api/chat'
import { useThemeStore } from '@/stores/theme'
import { useUserStore } from '@/stores/user'
import { onShow } from '@dcloudio/uni-app'

const themeStore = useThemeStore()
const userStore = useUserStore()
const themeStyle = computed(() => themeStore.vars)

const paging = ref(null)
const dataList = ref([])
const isLoading = ref(false)
const privateUnread = ref(0)
const noticeUnread = ref(0)
const chatRooms = ref([])

const loadUnreadCount = async () => {
    if (!userStore.isLogin) return

    try {
        const [privateRes, noticeRes] = await Promise.all([
            getUnreadTotal().catch(() => ({ total: 0 })),
            getNoticeUnreadCount().catch(() => ({ count: 0 }))
        ])
        privateUnread.value = privateRes?.total || 0
        noticeUnread.value = noticeRes?.count || 0
    } catch (e) {
        console.error('获取未读数量失败:', e)
    }
}

const queryList = async (pageNo: number, pageSize: number) => {
    try {
        const res = await getNoticeList({ page_no: pageNo, page_size: pageSize })
        paging.value.complete(res.lists || [])
    } catch (e) {
        paging.value.complete(false)
    }
}

const goDetail = (item: any) => {
    uni.navigateTo({
        url: `/pages/notice_detail/notice_detail?id=${item.id}`
    })
}

const handleMarkAllRead = async () => {
    try {
        await markAllNoticeRead()
        noticeUnread.value = 0
        paging.value?.reload()
        uni.showToast({ title: '已全部标记已读', icon: 'success' })
    } catch (e) {
        console.error('标记已读失败:', e)
    }
}

const goPrivateChat = () => {
    if (!userStore.isLogin) {
        uni.showToast({ title: '请先登录', icon: 'none' })
        setTimeout(() => {
            uni.navigateTo({ url: '/pages/login/login' })
        }, 1000)
        return
    }
    uni.navigateTo({
        url: '/pages/message/conversation'
    })
}

const goChatRoomList = () => {
    uni.navigateTo({
        url: '/pages/message/chat-room-list'
    })
}

onMounted(() => {
    loadUnreadCount()
})

onShow(() => {
    themeStore.getTheme()
    loadUnreadCount()
})
</script>

<style lang="scss" scoped>
.bg-f5 {
    background-color: #f5f5f5;
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    overflow: hidden;
}

.message-entries {
    padding: 20rpx;
    background-color: #fff;
}

.entry-item {
    display: flex;
    align-items: center;
    padding: 24rpx 20rpx;
    background-color: #f8f8f8;
    border-radius: 16rpx;
    margin-bottom: 16rpx;

    &:last-child {
        margin-bottom: 0;
    }

    &:active {
        background-color: #f0f0f0;
    }
}

.entry-icon {
    width: 88rpx;
    height: 88rpx;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 24rpx;

    &.private-icon {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    &.chat-icon {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }
}

.icon-emoji {
    font-size: 44rpx;
}

.entry-content {
    flex: 1;
    min-width: 0;
}

.entry-title {
    font-size: 32rpx;
    font-weight: bold;
    color: #333;
    margin-bottom: 8rpx;
}

.entry-desc {
    font-size: 26rpx;
    color: #999;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.entry-right {
    display: flex;
    align-items: center;
}

.unread-badge {
    min-width: 36rpx;
    height: 36rpx;
    line-height: 36rpx;
    padding: 0 12rpx;
    background-color: #ff4d4f;
    color: #fff;
    font-size: 22rpx;
    border-radius: 18rpx;
    text-align: center;
    margin-right: 16rpx;
}

.arrow {
    font-size: 40rpx;
    color: #ccc;
    font-weight: 300;
}

.section-title {
    font-size: 28rpx;
    color: #999;
    padding-left: 10rpx;
}
</style>
