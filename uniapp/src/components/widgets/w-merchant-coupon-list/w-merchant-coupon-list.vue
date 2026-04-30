<!--
  开发者公众号：杰哥网络科技。
  qq2711793818 杰哥
-->
<template>
    <view class="w-merchant-coupon-list bg-white">
        <z-paging
            ref="paging"
            v-model="dataList"
            @query="queryList"
            :fixed="false"
            :use-page-scroll="true"
            :auto="false"
        >
            <view class="p-3">
                <view class="font-bold text-lg mb-3 border-l-4 border-red-500 pl-2"
                    >商家优惠券</view
                >
                <view
                    v-for="(item, index) in dataList"
                    :key="index"
                    class="coupon-item mb-3 rounded-2xl overflow-hidden"
                    :class="{
                        'coupon-received': item.is_received,
                        'coupon-sold-out': item.is_sold_out && !item.is_received
                    }"
                    @click="handleCouponClick(item)"
                >
                    <view class="coupon-body flex bg-white">
                        <view
                            class="coupon-left flex flex-col items-center justify-center"
                            :class="item.is_received ? 'left-received' : 'left-normal'"
                            :style="
                                !item.is_received
                                    ? {
                                          backgroundColor: themeStore.primaryColor + '15',
                                          borderColor: themeStore.primaryColor + '40'
                                      }
                                    : {}
                            "
                        >
                            <view class="price-wrapper flex items-baseline">
                                <text
                                    class="price-symbol"
                                    :class="item.is_received ? 'text-gray-400' : ''"
                                    :style="
                                        !item.is_received ? { color: themeStore.primaryColor } : {}
                                    "
                                    >¥</text
                                >
                                <text
                                    class="price-value font-bold"
                                    :class="item.is_received ? 'text-gray-400' : ''"
                                    :style="
                                        !item.is_received ? { color: themeStore.primaryColor } : {}
                                    "
                                    >{{ item.amount || item.money || 0 }}</text
                                >
                            </view>
                            <view
                                class="condition-text text-xs mt-1"
                                :class="item.is_received ? 'text-gray-400' : ''"
                                :style="!item.is_received ? { color: themeStore.primaryColor } : {}"
                            >
                                {{
                                    item.min_amount > 0 ? '满' + item.min_amount + '可用' : '无门槛'
                                }}
                            </view>
                        </view>
                        <view class="coupon-right flex-1 flex flex-col justify-between p-4">
                            <view>
                                <view
                                    class="coupon-name font-bold text-base mb-2"
                                    :class="item.is_received ? 'text-gray-400' : 'text-gray-800'"
                                >
                                    {{ item.name || '优惠券' }}
                                </view>
                                <view
                                    class="text-xs mb-1"
                                    :class="item.is_received ? 'text-gray-300' : 'text-gray-500'"
                                >
                                    <text class="text-gray-300">有效期：</text
                                    >{{ item.validity_text || '长期有效' }}
                                </view>
                                <view
                                    class="text-xs"
                                    :class="item.is_received ? 'text-gray-300' : 'text-gray-500'"
                                >
                                    <text class="text-gray-300">剩余：</text
                                    >{{
                                        item.is_sold_out && !item.is_received
                                            ? '已领完'
                                            : item.total_count - item.send_count + ' 张'
                                    }}
                                </view>
                            </view>
                            <view class="flex justify-between items-center mt-3">
                                <view
                                    class="text-xs"
                                    :class="item.is_received ? 'text-gray-300' : 'text-gray-400'"
                                >
                                    {{ item.desc || '适用于指定商品' }}
                                </view>
                                <view
                                    class="receive-btn px-4 py-2 rounded-full text-xs font-bold"
                                    :class="
                                        item.is_received || item.is_sold_out
                                            ? 'bg-gray-200 text-gray-400'
                                            : ''
                                    "
                                    :style="
                                        !item.is_received && !item.is_sold_out
                                            ? {
                                                  backgroundColor: themeStore.primaryColor,
                                                  color: '#fff'
                                              }
                                            : {}
                                    "
                                >
                                    {{
                                        item.is_received
                                            ? '已领取'
                                            : item.is_sold_out
                                            ? '已领完'
                                            : '立即领取'
                                    }}
                                </view>
                            </view>
                        </view>
                    </view>
                </view>
            </view>
        </z-paging>
    </view>
</template>

<script setup lang="ts">
import { ref, watch, nextTick, onMounted } from 'vue'
import { getMerchantCouponList, receiveCoupon } from '@/api/coupon'
import { useThemeStore } from '@/stores/theme'

const themeStore = useThemeStore()

const props = defineProps<{
    merchantId: any
}>()

const paging = ref(null)
const dataList = ref([])
const loading = ref(false)

const queryList = async (pageNo: number, pageSize: number) => {
    if (!props.merchantId) {
        paging.value?.complete([])
        return
    }
    loading.value = true
    try {
        const res = await getMerchantCouponList({
            page_no: pageNo,
            page_size: pageSize,
            merchant_id: props.merchantId
        })
        paging.value?.complete(res.lists || [])
    } catch (e) {
        console.error('获取优惠券列表失败:', e)
        paging.value?.complete(false)
    } finally {
        loading.value = false
    }
}

const handleCouponClick = async (item: any) => {
    if (item.is_received) {
        uni.showToast({ title: '您已领取过该优惠券', icon: 'none' })
        return
    }
    if (item.is_sold_out) {
        uni.showToast({ title: '该优惠券已领完', icon: 'none' })
        return
    }
    try {
        await receiveCoupon({ coupon_id: item.id })
        uni.showToast({ title: '领取成功', icon: 'success' })
        item.is_received = true
        item.send_count = (item.send_count || 0) + 1
    } catch (e: any) {
        uni.showToast({ title: e.message || '领取失败', icon: 'none' })
    }
}

watch(
    () => props.merchantId,
    (val) => {
        if (val) {
            nextTick(() => {
                paging.value?.reload()
            })
        }
    },
    { immediate: true }
)
</script>

<style scoped lang="scss">
.coupon-item {
    box-shadow: 0 8rpx 24rpx rgba(0, 0, 0, 0.08);
    min-height: 200rpx;
    position: relative;

    &::before,
    &::after {
        content: '';
        position: absolute;
        width: 24rpx;
        height: 24rpx;
        border-radius: 50%;
        background-color: #f5f5f5;
        top: 50%;
        transform: translateY(-50%);
        z-index: 1;
    }

    &::before {
        left: -12rpx;
    }

    &::after {
        right: -12rpx;
    }
}

.coupon-received {
    opacity: 0.6;
}

.coupon-sold-out {
    opacity: 0.5;
}

.coupon-body {
    min-height: 100%;
    flex: 1;
}

.coupon-left {
    width: 200rpx;
    min-height: 200rpx;
    position: relative;
    border-right: 2rpx dashed;

    &.left-normal {
        background-color: #fff5f5;
        border-color: #ffcccc;
    }

    &.left-received {
        background-color: #f5f5f5;
        border-color: #e0e0e0;
    }

    .price-wrapper {
        display: flex;
        align-items: baseline;
    }

    .price-symbol {
        font-size: 28rpx;
        margin-right: 4rpx;
        font-weight: bold;
    }

    .price-value {
        font-size: 56rpx;
        line-height: 1;
    }

    .condition-text {
        font-size: 22rpx;
        margin-top: 8rpx;
        font-weight: 500;
    }
}

.coupon-right {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.coupon-name {
    font-size: 32rpx;
    line-height: 1.3;
}

.receive-btn {
    transition: all 0.2s;
    min-width: 140rpx;
    text-align: center;

    &:active {
        transform: scale(0.95);
    }
}
</style>
