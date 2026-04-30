<template>
    <view class="push-message-page" :style="themeStyle">
        <uni-nav title="推送消息"></uni-nav>

        <view class="header p-3 bg-white flex justify-between items-center" v-if="list.length > 0">
            <view class="text-gray-500 text-sm">
                未读消息：<text class="text-red-500 font-bold">{{ unreadCount }}</text> 条
            </view>
            <view class="read-all text-sm" :style="{ color: themeColor }" @click="handleReadAll">
                全部已读
            </view>
        </view>

        <view class="list p-3" v-if="list.length > 0">
            <view
                class="item bg-white rounded-lg p-3 mb-3"
                :class="{ 'is-read': item.is_read }"
                v-for="(item, index) in list"
                :key="index"
                @click="handleItemClick(item)"
            >
                <view class="flex items-start">
                    <u-image
                        width="80"
                        height="80"
                        :src="item.merchant_logo || '/static/images/user/default_avatar.png'"
                        border-radius="10"
                    ></u-image>
                    <view class="ml-3 flex-1">
                        <view class="flex justify-between items-center">
                            <view class="font-bold text-base">{{ item.title }}</view>
                            <view class="text-xs text-gray-400">{{ formatTime(item.create_time) }}</view>
                        </view>
                        <view class="text-gray-500 text-sm mt-2 line-clamp-2">
                            {{ item.content }}
                        </view>
                        <view class="keyword-tag mt-2">
                            <text class="text-xs px-2 py-1 rounded" :style="{ backgroundColor: themeColor + '20', color: themeColor }">
                                关键词：{{ item.keyword }}
                            </text>
                        </view>
                    </view>
                </view>
            </view>
        </view>

        <view class="empty-state flex flex-col items-center justify-center mt-20" v-else>
            <u-image
                width="200"
                height="200"
                src="/static/images/empty/data.png"
                mode="widthFix"
            ></u-image>
            <text class="text-gray-400 mt-4">暂无推送消息</text>
        </view>
    </view>
</template>

<script setup lang="ts">
/**
 * 推送消息列表页面
 * 开发者：杰哥网络科技 qq2711793818 杰哥
 */
import { ref, computed, onMounted } from 'vue'
import { onShow, onPullDownRefresh } from '@dcloudio/uni-app'
import { getPushMessageLists, readPushMessage, readAllPushMessage, getPushMessageUnreadCount } from '@/api/push'
import { useThemeStore } from '@/stores/theme'

const themeStore = useThemeStore()
const themeColor = computed(() => themeStore.primaryColor)
const themeStyle = computed(() => themeStore.vars)

const list = ref<any[]>([])
const unreadCount = ref(0)
const page = ref(1)
const finished = ref(false)

const getList = async () => {
    try {
        const res = await getPushMessageLists({
            page_no: page.value,
            page_size: 20
        })
        if (page.value === 1) {
            list.value = res.lists || []
        } else {
            list.value = [...list.value, ...(res.lists || [])]
        }
        if ((res.lists || []).length < 20) {
            finished.value = true
        }
    } catch (e) {
        console.error('获取推送消息列表失败', e)
    }
}

const getUnreadCount = async () => {
    try {
        const res = await getPushMessageUnreadCount()
        unreadCount.value = res.count || 0
    } catch (e) {
        console.error('获取未读数量失败', e)
    }
}

const handleItemClick = async (item: any) => {
    if (!item.is_read) {
        try {
            await readPushMessage({ id: item.id })
            item.is_read = 1
            unreadCount.value = Math.max(0, unreadCount.value - 1)
        } catch (e) {
            console.error('标记已读失败', e)
        }
    }
    
    if (item.article_id) {
        uni.navigateTo({
            url: `/pages/article/detail?id=${item.article_id}`
        })
    }
}

const handleReadAll = async () => {
    if (unreadCount.value === 0) {
        uni.showToast({ title: '没有未读消息', icon: 'none' })
        return
    }
    
    try {
        await readAllPushMessage()
        list.value.forEach(item => {
            item.is_read = 1
        })
        unreadCount.value = 0
        uni.showToast({ title: '操作成功', icon: 'success' })
    } catch (e) {
        uni.showToast({ title: '操作失败', icon: 'none' })
    }
}

const formatTime = (timestamp: number) => {
    if (!timestamp) return ''
    const date = new Date(timestamp * 1000)
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
        return `${date.getMonth() + 1}-${date.getDate()}`
    }
}

onShow(() => {
    page.value = 1
    finished.value = false
    getList()
    getUnreadCount()
    themeStore.getTheme()
})

onPullDownRefresh(() => {
    page.value = 1
    finished.value = false
    getList()
    getUnreadCount()
    uni.stopPullDownRefresh()
})
</script>

<style lang="scss" scoped>
.push-message-page {
    min-height: 100vh;
    background-color: #f8f8f8;
    padding-bottom: 20px;
}

.read-all {
    cursor: pointer;
}

.item.is-read {
    opacity: 0.6;
}
</style>
