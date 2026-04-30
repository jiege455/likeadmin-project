<template>
    <view class="merchant-header-wrapper">
        <!-- 加载中 - 初始化状态 -->
        <view class="loading-header" v-if="!initialized">
            <u-loading mode="circle" />
            <text class="loading-text">加载中...</text>
        </view>
        <!-- 已关注商家 - 显示商家信息 -->
        <view class="current-merchant-header" v-else-if="merchant && merchant.id">
            <view class="merchant-info" @click="handleClick">
                <image
                    class="avatar"
                    :src="merchant.logo || merchant.image || '/static/images/default_avatar.png'"
                    mode="aspectFill"
                />
                <view class="info">
                    <text class="name">{{ merchant.name }}</text>
                    <text class="desc">{{ merchant.desc || '暂无简介' }}</text>
                </view>
            </view>
            <view class="action-btns">
                <view class="action-btn" @click="handleClick">
                    <u-icon name="home" size="28" color="#fff" />
                    <text class="btn-text">进入主页</text>
                </view>
                <view class="action-btn" @click="handleSwitch">
                    <u-icon name="list" size="28" color="#fff" />
                    <text class="btn-text">切换商家</text>
                </view>
            </view>
        </view>

        <!-- 未关注商家 - 显示引导卡片 -->
        <view class="empty-header" v-else-if="userStore.isLogin">
            <view class="empty-icon">
                <u-icon name="home" size="80" color="#FF2D3A" />
            </view>
            <view class="empty-content">
                <text class="empty-title">还没有关注的商家</text>
                <text class="empty-desc">关注商家后可查看最新动态</text>
            </view>
            <view class="action-btns">
                <view class="action-btn primary" @click="goToMerchantList">
                    <u-icon name="plus" size="28" color="#fff" />
                    <text class="btn-text">去关注</text>
                </view>
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, onUnmounted } from 'vue'
import { getCurrentMerchant } from '@/api/merchant'
import { useUserStore } from '@/stores/user'

const props = defineProps<{
    merchantId?: number
}>()

const emit = defineEmits<{
    (e: 'switch'): void
    (e: 'change', merchant: any): void
}>()

const userStore = useUserStore()
const merchant = ref<any>(null)
const loading = ref(false)
const initialized = ref(false)

const loadMerchant = async () => {
    if (!userStore.isLogin) {
        console.log('[CurrentMerchantHeader] 用户未登录')
        merchant.value = null
        initialized.value = true
        return
    }

    loading.value = true
    try {
        console.log('[CurrentMerchantHeader] 开始获取当前商家...')
        const res = await getCurrentMerchant()
        console.log('[CurrentMerchantHeader] 获取结果:', res)
        merchant.value = res || null
        if (res && res.id) {
            emit('change', res)
        }
    } catch (e) {
        console.error('[CurrentMerchantHeader] 获取失败:', e)
        merchant.value = null
    } finally {
        loading.value = false
        initialized.value = true
    }
}

onMounted(() => {
    console.log('[CurrentMerchantHeader] onMounted, isLogin:', userStore.isLogin)
    if (userStore.isLogin) {
        loadMerchant()
    } else {
        initialized.value = true
    }
    uni.$on('merchantFollowChanged', handleFollowChanged)
})

onUnmounted(() => {
    uni.$off('merchantFollowChanged', handleFollowChanged)
})

const handleFollowChanged = () => {
    initialized.value = false
    loadMerchant()
}

watch(
    () => userStore.isLogin,
    (val) => {
        console.log('[CurrentMerchantHeader] isLogin changed:', val)
        if (val) {
            initialized.value = false
            loadMerchant()
        } else {
            merchant.value = null
            initialized.value = true
        }
    }
)

const handleClick = () => {
    if (merchant.value && merchant.value.id) {
        uni.navigateTo({
            url: `/pages/merchant_detail/merchant_detail?id=${merchant.value.id}`
        })
    }
}

const handleSwitch = () => {
    uni.navigateTo({
        url: '/packages/pages/follow/follow'
    })
}

const goToMerchantList = () => {
    uni.navigateTo({
        url: '/packages/pages/follow/follow'
    })
}

defineExpose({
    refresh: loadMerchant,
    merchant
})
</script>

<style lang="scss" scoped>
.merchant-header-wrapper {
    margin: 20rpx;
}

.current-merchant-header {
    background: linear-gradient(135deg, #ff2d3a 0%, #ff6b6b 100%);
    border-radius: 20rpx;
    padding: 30rpx;
    box-shadow: 0 8rpx 24rpx rgba(255, 45, 58, 0.3);

    .merchant-info {
        display: flex;
        align-items: center;
        margin-bottom: 24rpx;

        .avatar {
            width: 100rpx;
            height: 100rpx;
            border-radius: 50%;
            margin-right: 24rpx;
            border: 4rpx solid rgba(255, 255, 255, 0.5);
            flex-shrink: 0;
        }

        .info {
            flex: 1;
            min-width: 0;

            .name {
                font-size: 36rpx;
                font-weight: bold;
                color: #fff;
                display: block;
                margin-bottom: 8rpx;
            }

            .desc {
                font-size: 26rpx;
                color: rgba(255, 255, 255, 0.8);
                display: block;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
        }
    }

    .action-btns {
        display: flex;
        justify-content: center;
        gap: 30rpx;

        .action-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20rpx 40rpx;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 40rpx;
            flex: 1;

            .btn-text {
                font-size: 28rpx;
                color: #fff;
                margin-left: 10rpx;
            }
        }
    }
}

.empty-header {
    background: #fff;
    border-radius: 20rpx;
    padding: 50rpx 30rpx 30rpx;
    box-shadow: 0 4rpx 16rpx rgba(0, 0, 0, 0.08);
    display: flex;
    flex-direction: column;
    align-items: center;

    .empty-icon {
        width: 140rpx;
        height: 140rpx;
        background: linear-gradient(
            135deg,
            rgba(255, 45, 58, 0.1) 0%,
            rgba(255, 107, 107, 0.1) 100%
        );
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 24rpx;
    }

    .empty-content {
        text-align: center;
        margin-bottom: 30rpx;

        .empty-title {
            font-size: 32rpx;
            font-weight: bold;
            color: #333;
            display: block;
            margin-bottom: 12rpx;
        }

        .empty-desc {
            font-size: 26rpx;
            color: #999;
            display: block;
        }
    }

    .action-btns {
        display: flex;
        justify-content: center;
        width: 100%;

        .action-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24rpx 60rpx;
            background: #f5f5f5;
            border-radius: 40rpx;
            flex: 1;
            max-width: 400rpx;

            .btn-text {
                font-size: 30rpx;
                color: #666;
                margin-left: 10rpx;
            }

            &.primary {
                background: linear-gradient(135deg, #ff2d3a 0%, #ff6b6b 100%);
                box-shadow: 0 6rpx 16rpx rgba(255, 45, 58, 0.3);

                .btn-text {
                    color: #fff;
                }
            }
        }
    }
}

.loading-header {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40rpx;
    background: #f5f5f5;
    border-radius: 20rpx;

    .loading-text {
        margin-left: 16rpx;
        font-size: 28rpx;
        color: #999;
    }
}
</style>
