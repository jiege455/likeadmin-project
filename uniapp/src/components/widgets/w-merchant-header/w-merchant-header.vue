<template>
    <view class="w-merchant-header" :style="bgStyle">
        <!-- 头部导航栏占位（防止内容被遮挡） -->
        <view :style="{ height: navPlaceholderHeight + 'px' }"></view>

        <!-- 顶部导航栏模拟 -->
        <view
            class="fixed top-0 left-0 w-full z-50 flex justify-between items-center px-[30rpx]"
            :style="{ paddingTop: 'var(--status-bar-height)', height: navHeight + 'px', color: '#fff' }"
        >
            <view class="font-bold text-base">{{ shopName }}</view>
            <view
                class="text-xs bg-white/20 px-[16rpx] py-[6rpx] rounded-full"
                @click="handleSwitch"
            >
                切换商家
            </view>
        </view>

        <!-- 商家信息卡片（紧凑版） -->
        <view class="mx-[20rpx] mt-[10rpx] bg-white rounded-lg p-[10rpx] relative z-10">
            <view class="flex items-center">
                <view
                    class="w-[56rpx] h-[56rpx] rounded-full bg-gray-200 overflow-hidden shrink-0"
                >
                    <u-image
                        :src="merchant?.logo || merchant?.image || ''"
                        width="56rpx"
                        height="56rpx"
                        shape="circle"
                    ></u-image>
                </view>
                <view class="ml-[10rpx] flex-1 min-w-0">
                    <view class="flex items-center gap-1">
                        <view class="font-bold text-sm truncate">{{
                            merchant?.name || '商家名称'
                        }}</view>
                        <view
                            v-if="content.show_stats && merchant?.distribution_switch == 1"
                            class="text-xs text-[#4073fa] bg-[#eef4ff] px-[4rpx] rounded shrink-0"
                        >
                            {{ merchant?.ratio ?? 0 }}%
                        </view>
                    </view>
                    <view 
                        class="text-xs text-gray-400"
                        :class="isExpanded ? '' : 'line-clamp-1'"
                        @click="toggleExpand"
                    >
                        {{ merchant?.remarks || '暂无简介' }}
                        <text v-if="hasLongRemark" class="text-primary ml-1">
                            {{ isExpanded ? '收起' : '展开' }}
                        </text>
                    </view>
                </view>
                <!-- 按钮组整合到卡片内 -->
                <view v-if="content.show_buttons" class="flex items-center gap-1 shrink-0 ml-2">
                    <view
                        v-if="content.show_wechat"
                        class="action-btn-sm"
                        @click="handleCopy(merchant?.wechat || '')"
                    >
                        <u-icon name="weixin-fill" color="#07C160" size="12"></u-icon>
                    </view>
                    <view
                        v-if="content.show_share && merchant?.distribution_switch == 1"
                        class="action-btn-sm"
                        @click="handleShare"
                    >
                        <u-icon name="share-fill" color="#4073fa" size="12"></u-icon>
                    </view>
                    <view v-if="content.show_chat" class="action-btn-sm" @click="handleChat">
                        <u-icon name="chat-fill" color="#ff9500" size="12"></u-icon>
                    </view>
                    <view
                        v-if="content.show_complain"
                        class="action-btn-sm"
                        @click="handleComplain"
                    >
                        <u-icon name="info-circle-fill" color="#999" size="12"></u-icon>
                    </view>
                </view>
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import { navigateTo } from '@/utils/util'

const props = defineProps<{
    content: any
    styles: any
    merchant: any
}>()

const isExpanded = ref(false)

const systemInfo = uni.getSystemInfoSync()
const statusBarHeight = systemInfo.statusBarHeight || 20
const navHeight = 44
const navPlaceholderHeight = navHeight + statusBarHeight

const shopName = computed(() => props.merchant?.name || '商家主页')

const hasLongRemark = computed(() => {
    const remarks = props.merchant?.remarks || ''
    return remarks.length > 20
})

const toggleExpand = () => {
    if (hasLongRemark.value) {
        isExpanded.value = !isExpanded.value
    }
}

const bgStyle = computed(() => {
    const { bg_type, bg_color, bg_image } = props.content
    if (bg_type == 2 && bg_image) {
        return {
            backgroundImage: `url(${bg_image})`,
            backgroundSize: '100% auto',
            backgroundRepeat: 'no-repeat',
            paddingBottom: '8rpx'
        }
    }
    return {
        backgroundColor: bg_color || '#e1251b',
        paddingBottom: '8rpx'
    }
})

const handleSwitch = () => {
    if (props.content.switch_link && props.content.switch_link.path) {
        navigateTo(props.content.switch_link)
    } else {
        uni.navigateTo({ url: '/packages/pages/follow/follow' })
    }
}

const handleCopy = (content: string) => {
    if (!content) {
        uni.showToast({ title: '暂无微信号', icon: 'none' })
        return
    }
    uni.setClipboardData({
        data: content,
        success: () => {
            uni.showToast({ title: '复制成功' })
        }
    })
}

const handleShare = () => {
    uni.$emit('merchantShare')
}

const handleChat = () => {
    uni.navigateTo({
        url: `/pages/chat/chat?merchant_id=${props.merchant?.id}`
    })
}

const handleComplain = () => {
    uni.navigateTo({
        url: `/pages/business/complaint?type=1&target_id=${
            props.merchant?.id
        }&title=${encodeURIComponent(props.merchant?.name || '商家')}`
    })
}
</script>

<style scoped>
.w-merchant-header {
    width: 100%;
    padding-top: var(--status-bar-height);
}

.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.action-btn-sm {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 44rpx;
    height: 44rpx;
    background: #f5f5f5;
    border-radius: 50%;
}

.action-btn-sm:active {
    background: #e8e8e8;
    transform: scale(0.95);
}
</style>
