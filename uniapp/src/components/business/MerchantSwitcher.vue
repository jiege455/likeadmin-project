<template>
    <u-popup v-model="show" mode="bottom" border-radius="20" :closeable="true" @close="handleClose">
        <view class="merchant-switcher">
            <view class="header">
                <text class="title">切换商家</text>
            </view>
            <scroll-view scroll-y class="merchant-list">
                <!-- 加载中状态 -->
                <view v-if="loading" class="loading-state">
                    <u-loading mode="circle" size="40"></u-loading>
                    <text class="loading-text">加载中...</text>
                </view>
                <!-- 商家列表 -->
                <view
                    v-else
                    v-for="item in merchantList"
                    :key="item.id"
                    class="merchant-item"
                    :class="{ active: currentId === item.id }"
                    @click="handleSelect(item)"
                >
                    <image
                        class="avatar"
                        :src="item.logo || item.image || '/static/images/default_avatar.png'"
                        mode="aspectFill"
                    />
                    <view class="info">
                        <text class="name">{{ item.name }}</text>
                        <text class="desc">{{ item.desc || '暂无简介' }}</text>
                    </view>
                    <view v-if="currentId === item.id" class="check-icon">
                        <u-icon name="checkmark" color="#FF2D3A" size="40" />
                    </view>
                </view>
                <!-- 空状态 -->
                <u-empty v-if="!loading && merchantList.length === 0" text="暂无关注的商家" icon="heart" iconSize="80" :show="true"></u-empty>
            </scroll-view>
            <view class="footer">
                <u-button type="primary" @click="handleClose">取消</u-button>
            </view>
        </view>
    </u-popup>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { getFollowedMerchants, setCurrentMerchant } from '@/api/merchant'
import { useAppStore } from '@/stores/app'

const props = defineProps<{
    modelValue: boolean
    currentMerchantId: number
}>()

const emit = defineEmits<{
    (e: 'update:modelValue', value: boolean): void
    (e: 'change', merchant: any): void
}>()

const appStore = useAppStore()
const show = ref(false)
const merchantList = ref<any[]>([])
const currentId = ref(0)
const loading = ref(false)

watch(
    () => props.modelValue,
    (val) => {
        show.value = val
        if (val) {
            currentId.value = props.currentMerchantId
            loadMerchants()
        }
    }
)

watch(show, (val) => {
    emit('update:modelValue', val)
})

const loadMerchants = async () => {
    loading.value = true
    try {
        const res = await getFollowedMerchants()
        merchantList.value = res || []
    } catch (e) {
        console.error(e)
    } finally {
        loading.value = false
    }
}

const handleSelect = async (item: any) => {
    if (currentId.value === item.id) {
        handleClose()
        return
    }

    try {
        uni.showLoading({ title: '切换中...' })
        await setCurrentMerchant(item.id)
        currentId.value = item.id
        uni.hideLoading()
        emit('change', item)
        handleClose()
    } catch (e: any) {
        uni.hideLoading()
        uni.showToast({ title: e.message || '切换失败', icon: 'none' })
    }
}

const handleClose = () => {
    show.value = false
}
</script>

<style lang="scss" scoped>
.merchant-switcher {
    background: #fff;
    max-height: 70vh;
    display: flex;
    flex-direction: column;

    .header {
        padding: 30rpx;
        text-align: center;
        border-bottom: 1rpx solid #f5f5f5;

        .title {
            font-size: 32rpx;
            font-weight: bold;
            color: #333;
        }
    }

    .merchant-list {
        flex: 1;
        padding: 20rpx;
        max-height: 50vh;
    }

    .loading-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 60rpx 0;

        .loading-text {
            margin-top: 20rpx;
            font-size: 28rpx;
            color: #999;
        }
    }

    .empty-state {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 60rpx 0;
        font-size: 28rpx;
        color: #999;
    }

    .merchant-item {
        display: flex;
        align-items: center;
        padding: 24rpx 20rpx;
        margin-bottom: 16rpx;
        background: #f8f8f8;
        border-radius: 16rpx;
        transition: all 0.3s;

        &.active {
            background: #fff5f5;
            border: 2rpx solid #ff2d3a;
        }

        .avatar {
            width: 100rpx;
            height: 100rpx;
            border-radius: 50%;
            margin-right: 24rpx;
            flex-shrink: 0;
        }

        .info {
            flex: 1;
            min-width: 0;

            .name {
                font-size: 30rpx;
                font-weight: bold;
                color: #333;
                display: block;
                margin-bottom: 8rpx;
            }

            .desc {
                font-size: 24rpx;
                color: #999;
                display: block;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
        }

        .check-icon {
            margin-left: 16rpx;
        }
    }

    .footer {
        padding: 20rpx 30rpx;
        padding-bottom: calc(20rpx + env(safe-area-inset-bottom));
        border-top: 1rpx solid #f5f5f5;
    }
}
</style>
