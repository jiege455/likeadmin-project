<template>
    <view class="invite-friends-widget" :style="{ backgroundColor: styles.root_bg_color }">
        <view
            v-if="inviteCode"
            class="invite-card"
            :style="{ backgroundColor: styles.bg_color || '#fff' }"
        >
            <view class="title" :style="{ color: styles.title_color }">{{
                content.title || '邀请好友'
            }}</view>
            <view class="code" :style="{ color: styles.text_color }"
                >我的邀请码:
                <text class="highlight" :style="{ color: styles.highlight_color || '#ff5500' }">{{
                    inviteCode
                }}</text></view
            >
            <button
                class="share-btn"
                open-type="share"
                @click="handleShare"
                :style="{
                    backgroundColor: styles.btn_bg_color || '#333',
                    color: styles.btn_text_color || '#fff'
                }"
            >
                立即邀请
            </button>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { getDistributionIndex } from '@/api/distribution'
import request from '@/utils/request'

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

const inviteCode = ref('')
const shareUrl = ref('')

const getCode = async () => {
    try {
        const res = await getDistributionIndex()
        inviteCode.value = res.invite_code || ''
        shareUrl.value = res.share_url || ''
    } catch (e: any) {
        if (e?.code === 20001) {
            inviteCode.value = ''
        }
        console.error(e)
    }
}

const handleShare = () => {
    if (!inviteCode.value) {
        uni.showToast({
            title: '请先申请成为推广员',
            icon: 'none'
        })
        return
    }

    // #ifdef H5
    const url =
        shareUrl.value ||
        `${window.location.origin}/#/pages/index/index?invite_code=${inviteCode.value}`

    uni.setClipboardData({
        data: url,
        success: () => {
            uni.showToast({
                title: '邀请链接已复制',
                icon: 'none'
            })
        }
    })
    // #endif

    // #ifdef APP-PLUS
    const content = shareUrl.value
        ? `邀请你加入，点击链接注册：${shareUrl.value}`
        : `请填写我的邀请码：${inviteCode.value}`

    uni.setClipboardData({
        data: content,
        success: () => {
            uni.showToast({
                title: '邀请内容已复制',
                icon: 'none'
            })
        }
    })
    // #endif
}

onMounted(() => {
    getCode()
})
</script>

<style scoped>
.invite-friends-widget {
    padding: 20rpx;
}
.invite-card {
    /* background-color: #fff; */
    padding: 40rpx;
    border-radius: 16rpx;
    text-align: center;
    box-shadow: 0 4rpx 12rpx rgba(0, 0, 0, 0.05);
}
.title {
    font-size: 32rpx;
    font-weight: bold;
    margin-bottom: 20rpx;
}
.code {
    font-size: 28rpx;
    /* color: #666; */
    margin-bottom: 40rpx;
}
.highlight {
    /* color: #ff5500; */
    font-weight: bold;
    margin-left: 10rpx;
}
.share-btn {
    /* background-color: #333; */
    /* color: #fff; */
    border-radius: 40rpx;
    font-size: 28rpx;
    width: 80%;
    margin: 0 auto;
}
</style>
