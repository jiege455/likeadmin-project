<template>
    <view
        class="social-actions flex items-center justify-between px-4 py-3 bg-white border-t border-gray-100"
    >
        <view class="flex items-center space-x-6">
            <view class="action-item flex items-center" @click="handleLike">
                <u-icon
                    :name="isLiked ? 'thumb-up-fill' : 'thumb-up'"
                    size="40"
                    :color="isLiked ? '#ff5500' : '#666'"
                ></u-icon>
                <text class="ml-1 text-sm text-gray-500">{{ likeCount }}</text>
            </view>
            <view class="action-item flex items-center" @click="handleComment">
                <u-icon name="chat" size="40" color="#666"></u-icon>
                <text class="ml-1 text-sm text-gray-500">{{ commentCount }}</text>
            </view>
            <view class="action-item flex items-center" @click="handleCollect">
                <u-icon
                    :name="isCollected ? 'star-fill' : 'star'"
                    size="40"
                    :color="isCollected ? '#ff5500' : '#666'"
                ></u-icon>
            </view>
        </view>
        <view class="share-btn">
            <u-button open-type="share" size="mini" shape="circle" type="info" :plain="true">
                <u-icon name="share" size="28" class="mr-1"></u-icon>分享
            </u-button>
        </view>
    </view>

    <!-- 评论列表弹窗 -->
    <u-popup v-model="showComment" mode="bottom" border-radius="20" height="60%">
        <view class="comment-box flex flex-col h-full">
            <view class="title p-3 text-center font-bold border-b border-gray-100"
                >全部评论 ({{ commentCount }})</view
            >
            <scroll-view scroll-y class="flex-1 p-3">
                <view v-for="item in commentList" :key="item.id" class="comment-item mb-4 flex">
                    <u-avatar :src="item.avatar" size="60" class="mr-2"></u-avatar>
                    <view class="flex-1">
                        <view class="flex justify-between items-center mb-1">
                            <text class="text-sm font-bold text-gray-700">{{ item.nickname }}</text>
                            <text class="text-xs text-gray-400">{{ item.create_time }}</text>
                        </view>
                        <view class="text-sm text-gray-800">{{ item.content }}</view>
                    </view>
                </view>
                <u-empty v-if="commentList.length === 0" mode="message" text="暂无评论"></u-empty>
            </scroll-view>
            <view class="input-box p-3 border-t border-gray-100 flex items-center">
                <u-input
                    v-model="commentContent"
                    placeholder="说点什么..."
                    border
                    class="flex-1 mr-2 bg-gray-50 rounded-full px-3"
                    :clearable="false"
                ></u-input>
                <u-button size="mini" type="primary" shape="circle" @click="submitComment"
                    >发送</u-button
                >
            </view>
        </view>
    </u-popup>
</template>

<script setup lang="ts">
import { ref, defineProps, defineEmits } from 'vue'

const props = defineProps({
    articleId: { type: [String, Number], required: true },
    initialLikeCount: { type: Number, default: 0 },
    initialCommentCount: { type: Number, default: 0 },
    initialIsLiked: { type: Boolean, default: false },
    initialIsCollected: { type: Boolean, default: false }
})

const emit = defineEmits(['like', 'collect', 'comment'])

const isLiked = ref(props.initialIsLiked)
const likeCount = ref(props.initialLikeCount)
const isCollected = ref(props.initialIsCollected)
const commentCount = ref(props.initialCommentCount)
const showComment = ref(false)
const commentContent = ref('')
const commentList = ref<any[]>([])

// 模拟评论数据
const mockComments = [
    { id: 1, nickname: '用户A', avatar: '', content: '写得真好！', create_time: '10分钟前' },
    { id: 2, nickname: '用户B', avatar: '', content: '学到了', create_time: '1小时前' }
]

const handleLike = () => {
    isLiked.value = !isLiked.value
    likeCount.value += isLiked.value ? 1 : -1
    emit('like', isLiked.value)
    uni.$u.toast(isLiked.value ? '点赞成功' : '取消点赞')
}

const handleCollect = () => {
    isCollected.value = !isCollected.value
    emit('collect', isCollected.value)
    uni.$u.toast(isCollected.value ? '收藏成功' : '取消收藏')
}

const handleComment = () => {
    showComment.value = true
    // 实际应调用API获取评论列表
    if (commentList.value.length === 0) {
        commentList.value = mockComments
    }
}

const submitComment = () => {
    if (!commentContent.value.trim()) return uni.$u.toast('请输入内容')

    // 模拟发送
    const newComment = {
        id: Date.now(),
        nickname: '我',
        avatar: '', // 实际应取当前用户头像
        content: commentContent.value,
        create_time: '刚刚'
    }
    commentList.value.unshift(newComment)
    commentCount.value++
    commentContent.value = ''
    emit('comment', newComment)
    uni.$u.toast('评论成功')
}
</script>

<style scoped>
.social-actions {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 99;
    padding-bottom: env(safe-area-inset-bottom);
}
</style>
