<!--
    订单列表页面
    开发者公众号：杰哥网络科技
    QQ：2711793818 杰哥
-->
<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar :front-color="$theme.navColor" :background-color="$theme.navBgColor" />
        <!-- #endif -->
    </page-meta>
    <view class="order-page">
        <z-paging
            ref="paging"
            v-model="orderList"
            @query="queryList"
            :fixed="false"
            height="calc(100vh - 120rpx)"
            :auto="true"
            :empty-view-text="'暂无订单数据'"
        >
            <template #top>
                <view class="tabs-card bg-white mb-[20rpx]">
                    <view class="tabs-content">
                        <u-tabs
                            :list="tabs"
                            :current="current"
                            @change="changeTab"
                            :is-scroll="false"
                            :active-color="$theme.primaryColor || '#4A5DFF'"
                            line-height="6rpx"
                            bar-height="6rpx"
                            :bar-style="{ borderRadius: '3rpx' }"
                        ></u-tabs>
                    </view>
                </view>
            </template>

            <template #empty>
                <view class="flex flex-col items-center justify-center py-[120rpx]">
                    <view
                        class="w-[240rpx] h-[240rpx] rounded-full bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center mb-[32rpx]"
                    >
                        <u-icon name="order" size="120" color="#C0C4CC"></u-icon>
                    </view>
                    <text class="text-[28rpx] text-gray-500 font-medium">暂无订单数据</text>
                </view>
            </template>

            <view class="order-list p-[20rpx]">
                <view
                    class="order-item bg-white rounded-xl mb-[20rpx] overflow-hidden shadow-sm"
                    v-for="(item, index) in orderList"
                    :key="index"
                >
                    <!-- 订单头部 -->
                    <view class="p-[20rpx] border-b border-gray-100">
                        <view class="flex justify-between items-center">
                            <text class="text-xs text-gray-500">订单号: {{ item.sn }}</text>
                            <view
                                class="text-xs px-2 py-1 rounded"
                                :class="
                                    item.pay_status === 1
                                        ? 'bg-green-100 text-green-600'
                                        : item.pay_timeout
                                          ? 'bg-gray-100 text-gray-500'
                                          : 'bg-orange-100 text-orange-600'
                                "
                            >
                                {{ item.pay_timeout ? '已超时' : item.pay_status_text }}
                            </view>
                        </view>
                        <view class="flex justify-between items-center mt-1">
                            <text class="text-xs text-gray-400">{{ item.create_time_text }}</text>
                            <text class="text-xs text-gray-400" v-if="item.pay_way_text">
                                {{ item.pay_way_text }}
                            </text>
                        </view>
                    </view>

                    <!-- 商品信息 -->
                    <view
                        class="p-[20rpx] flex border-b border-gray-50"
                        v-for="(goods, gIndex) in item.order_goods"
                        :key="gIndex"
                    >
                        <u-image
                            width="160"
                            height="160"
                            :src="goods.image || '/static/images/default_goods.png'"
                            border-radius="8"
                            mode="aspectFill"
                        ></u-image>
                        <view class="flex-1 ml-[20rpx] flex flex-col justify-between">
                            <view>
                                <view class="text-sm line-clamp-2 font-medium">{{
                                    goods.goods_name
                                }}</view>
                                <view class="text-xs text-gray-400 mt-1" v-if="item.issue_no_text">
                                    {{ item.issue_no_text }}
                                </view>
                                <view
                                    class="text-xs text-gray-400 mt-1"
                                    v-if="goods.spec_value_str"
                                >
                                    {{ goods.spec_value_str }}
                                </view>
                            </view>
                            <view class="flex justify-between items-center">
                                <view>
                                    <view v-if="item.coupon_id > 0 && goods.coupon_money > 0">
                                        <text class="text-xs text-gray-400 line-through mr-1"
                                            >¥{{ goods.original_price }}</text
                                        >
                                        <text class="price-text">¥{{ goods.goods_price }}</text>
                                    </view>
                                    <view v-else class="price-text">¥{{ goods.goods_price }}</view>
                                </view>
                                <view class="text-gray-500 text-xs">x{{ goods.goods_num }}</view>
                            </view>
                            <view v-if="item.coupon_id > 0 && goods.coupon_money > 0" class="mt-1">
                                <text
                                    class="text-xs bg-orange-50 text-orange-500 px-2 py-1 rounded"
                                >
                                    优惠券: {{ goods.coupon_name || '优惠券' }} -¥{{
                                        goods.coupon_money
                                    }}
                                </text>
                            </view>
                        </view>
                    </view>

                    <!-- 金额和操作 -->
                    <view class="p-[20rpx]">
                        <view class="flex justify-between items-center">
                            <view class="text-xs text-gray-400">
                                共{{ item.total_num }}件商品
                            </view>
                            <view class="text-right">
                                实付
                                <text class="price-text font-bold text-lg"
                                    >¥{{ item.order_amount }}</text
                                >
                            </view>
                        </view>

                        <!-- 支付时间 -->
                        <view
                            v-if="item.pay_time_text"
                            class="mt-2 text-xs text-gray-400 text-right"
                        >
                            支付时间: {{ item.pay_time_text }}
                        </view>

                        <!-- 操作按钮 -->
                        <view class="flex justify-end items-center mt-[20rpx] gap-2">
                            <u-button
                                v-if="item.pay_btn"
                                size="mini"
                                shape="circle"
                                type="primary"
                                @click.stop="handlePay(item)"
                                >立即支付</u-button
                            >
                            <u-button
                                v-if="item.delete_btn"
                                size="mini"
                                shape="circle"
                                @click.stop="handleDelete(item.id)"
                                >删除订单</u-button
                            >
                        </view>
                    </view>
                </view>
            </view>
        </z-paging>

        <payment
            v-model:show="payState.showPay"
            v-model:show-check="payState.showCheck"
            :order-id="payState.orderId"
            :from="payState.from"
            :redirect="payState.redirect"
            @success="handlePaySuccess"
            @fail="handlePayFail"
        />

        <!-- 底部导航 -->
        <tabbar />
    </view>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { getOrderList, deleteOrder } from '@/api/order'
import { onLoad } from '@dcloudio/uni-app'
import payment from '@/components/payment/payment.vue'
import { useThemeStore } from '@/stores/theme'

// 初始化主题
const themeStore = useThemeStore()
// 确保主题已加载
if (!themeStore.primaryColor) {
    themeStore.getTheme()
}

const paging = ref(null)
const current = ref(0)
const orderList = ref([])

const payState = reactive({
    orderId: 0,
    from: 'article',
    showPay: false,
    showCheck: false,
    redirect: '/pages/order/order'
})

const tabs = [{ name: '全部' }, { name: '待付款' }, { name: '已完成' }]

const tabType = ['all', 'pay', 'finish']

const queryList = async (pageNo, pageSize) => {
    try {
        const res = await getOrderList({
            type: tabType[current.value],
            page_no: pageNo,
            page_size: pageSize
        })
        paging.value.complete(res.lists || [])
    } catch (e) {
        paging.value.complete(false)
    }
}

const changeTab = (index) => {
    current.value = index
    paging.value.reload()
}

const handleDelete = async (id) => {
    uni.showModal({
        title: '提示',
        content: '确定删除该订单吗？',
        success: async (res) => {
            if (res.confirm) {
                await deleteOrder(id)
                uni.showToast({ title: '删除成功' })
                paging.value.reload()
            }
        }
    })
}

const handlePay = (item) => {
    payState.orderId = item.id
    payState.from = 'article'
    payState.showPay = true
}

const handlePaySuccess = async () => {
    payState.showPay = false
    payState.showCheck = false
    uni.showToast({ title: '支付成功', icon: 'success' })
    paging.value.reload()
}

const handlePayFail = async () => {
    uni.$u.toast('支付失败')
}
</script>

<style lang="scss" scoped>
.order-page {
    height: 100vh;
    background-color: #f5f5f5;
}

// 选项卡卡片样式
.tabs-card {
    border-radius: 20rpx;
    box-shadow: 0 2rpx 12rpx rgba(0, 0, 0, 0.04);

    .tabs-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    }

    .tabs-content {
        background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
    }
}

// 订单卡片样式
.order-item {
    background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
    box-shadow: 0 2rpx 12rpx rgba(0, 0, 0, 0.04);
    transition: all 0.3s ease;

    &:active {
        transform: translateY(2rpx);
        box-shadow: 0 1rpx 8rpx rgba(0, 0, 0, 0.03);
    }
}

.order-list {
    padding-bottom: env(safe-area-inset-bottom);
}

// 按钮样式
:deep(.u-button--primary) {
    background: linear-gradient(135deg, var(--color-primary, #4A5DFF) 0%, var(--color-primary-light, #6b91ff) 100%);
    border: none;
}

:deep(.u-button--default) {
    border-color: #dcdfe6;
}

// 价格文字使用主题色
.price-text {
    color: var(--color-primary, #4A5DFF);
}
</style>
