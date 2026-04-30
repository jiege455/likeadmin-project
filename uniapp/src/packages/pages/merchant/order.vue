<template>
    <uni-nav title="订单管理"></uni-nav>

    <view class="merchant-order min-h-screen bg-f5">
        <!-- 筛选 -->
        <view
            class="filter bg-white mx-3 mt-3 rounded-xl p-3 relative z-10 shadow-sm flex items-center"
        >
            <view
                class="flex-1 text-center py-2 rounded-lg"
                :class="status === '' ? 'text-white' : 'text-gray-600'"
                :style="status === '' ? { backgroundColor: themeStore.primaryColor } : {}"
                @click="status = ''"
                >全部</view
            >
            <view
                class="flex-1 text-center py-2 rounded-lg mx-2"
                :class="status === '1' ? 'text-white' : 'text-gray-600'"
                :style="status === '1' ? { backgroundColor: themeStore.primaryColor } : {}"
                @click="status = '1'"
                >已支付</view
            >
            <view
                class="flex-1 text-center py-2 rounded-lg"
                :class="status === '0' ? 'text-white' : 'text-gray-600'"
                :style="status === '0' ? { backgroundColor: themeStore.primaryColor } : {}"
                @click="status = '0'"
                >待支付</view
            >
        </view>

        <!-- 订单列表 -->
        <view class="list p-3">
            <view
                class="item bg-white rounded-xl mb-3 overflow-hidden shadow-sm"
                v-for="(item, index) in list"
                :key="index"
            >
                <!-- 订单头部 -->
                <view class="p-3 border-b border-gray-100">
                    <view class="flex justify-between items-center">
                        <text class="text-xs text-gray-500">订单号: {{ item.order_sn }}</text>
                        <view
                            class="text-xs px-2 py-1 rounded"
                            :class="
                                item.pay_status === 1
                                    ? 'bg-green-100 text-green-600'
                                    : 'bg-orange-100 text-orange-600'
                            "
                        >
                            {{ item.pay_status_text }}
                        </view>
                    </view>
                    <view class="flex justify-between items-center mt-1">
                        <text class="text-xs text-gray-400">{{ item.create_time_text }}</text>
                        <text class="text-xs text-gray-400" v-if="item.pay_way_text">
                            支付方式: {{ item.pay_way_text }}
                        </text>
                    </view>
                </view>

                <!-- 文章信息 -->
                <view class="p-3 flex items-center border-b border-gray-50">
                    <u-image
                        :src="item.article_image"
                        width="80"
                        height="80"
                        border-radius="8"
                        mode="aspectFill"
                    ></u-image>
                    <view class="flex-1 ml-3">
                        <view class="font-medium text-sm line-clamp-2">{{
                            item.article_title
                        }}</view>
                        <view class="text-xs text-gray-400 mt-1" v-if="item.issue_no_text">
                            {{ item.issue_no_text }}
                        </view>
                    </view>
                </view>

                <!-- 购买者信息 -->
                <view class="p-3 border-b border-gray-50">
                    <view class="flex items-center">
                        <u-avatar :src="item.avatar" size="28"></u-avatar>
                        <text class="text-sm text-gray-700 ml-2">{{ item.nickname }}</text>
                    </view>
                </view>

                <!-- 金额信息 -->
                <view class="p-3">
                    <view class="flex justify-between items-center">
                        <text class="text-sm text-gray-500">订单金额</text>
                        <view class="text-right">
                            <view v-if="item.coupon_id > 0 && item.coupon_money > 0">
                                <text class="text-xs text-gray-400 line-through mr-1"
                                    >¥{{ item.original_amount }}</text
                                >
                                <text class="text-red-500 font-bold">¥{{ item.order_amount }}</text>
                            </view>
                            <view v-else class="text-red-500 font-bold"
                                >¥{{ item.order_amount }}</view
                            >
                        </view>
                    </view>

                    <!-- 优惠券 -->
                    <view v-if="item.coupon_id > 0 && item.coupon_money > 0" class="mt-2">
                        <text class="text-xs bg-orange-50 text-orange-500 px-2 py-1 rounded">
                            优惠券: {{ item.coupon_name || '优惠券' }} -¥{{ item.coupon_money }}
                        </text>
                    </view>

                    <!-- 利润信息 -->
                    <view
                        v-if="item.pay_status === 1 && item.profit_text"
                        class="mt-2 pt-2 border-t border-gray-100"
                    >
                        <text class="text-xs text-green-600">{{ item.profit_text }}</text>
                    </view>

                    <!-- 支付时间 -->
                    <view v-if="item.pay_time_text" class="mt-1">
                        <text class="text-xs text-gray-400"
                            >支付时间: {{ item.pay_time_text }}</text
                        >
                    </view>
                </view>
            </view>
            <u-empty v-if="list.length === 0" mode="list" text="暂无订单"></u-empty>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import request from '@/utils/request'
import { onShow } from '@dcloudio/uni-app'
import { useThemeStore } from '@/stores/theme'
import { safeNavigateBack } from '@/utils/util'

const themeStore = useThemeStore()
const themeStyle = computed(() => `--color-primary: ${themeStore.primaryColor};`)

const list = ref<any[]>([])
const status = ref('')

const getList = async () => {
    try {
        const res = await request.get({
            url: '/merchant.order/lists',
            data: {
                pay_status: status.value
            }
        })
        list.value = res.lists || []
    } catch (e) {
        list.value = []
    }
}

const goBack = () => {
    safeNavigateBack({ defaultUrl: '/pages/index/index' })
}

watch([status], () => {
    getList()
})

onShow(() => {
    uni.setNavigationBarTitle({ title: '订单管理' })
    getList()
    themeStore.getTheme()
})
</script>

<style lang="scss" scoped>
.merchant-order {
    background-color: #f5f5f5;
}
.bg-f5 {
    background-color: #f5f5f5;
}
</style>
