<template>
    <view class="recharge-page p-4 bg-gray-50 min-h-screen">
        <view class="bg-white rounded-lg p-6 mb-4">
            <view class="text-sm text-gray-500 mb-2">当前余额</view>
            <view class="text-3xl font-bold">¥ {{ balance }}</view>
        </view>

        <view class="bg-white rounded-lg p-6">
            <view class="font-bold mb-4">充值金额</view>
            <view class="grid grid-cols-3 gap-3 mb-6">
                <view
                    v-for="(item, index) in amountList"
                    :key="index"
                    class="h-16 rounded border flex items-center justify-center font-bold text-lg transition-all"
                    :class="
                        selectedAmount === item
                            ? 'border-primary text-primary bg-blue-50'
                            : 'border-gray-200 text-gray-700'
                    "
                    @click="selectedAmount = item"
                >
                    {{ item }}元
                </view>
            </view>

            <view class="mb-6">
                <view class="text-sm text-gray-500 mb-2">其他金额</view>
                <u-input
                    v-model="customAmount"
                    type="number"
                    placeholder="请输入充值金额"
                    border
                    @input="selectedAmount = 0"
                />
            </view>

            <view class="mb-6" v-if="payWayList.length > 1">
                <view class="text-sm text-gray-500 mb-2">支付方式</view>
                <view class="space-y-2">
                    <view
                        v-for="item in payWayList"
                        :key="item.pay_way"
                        class="p-3 border rounded-lg flex items-center justify-between"
                        :class="
                            selectedPayWay === item.pay_way
                                ? 'border-primary bg-blue-50'
                                : 'border-gray-200'
                        "
                        @click="selectedPayWay = item.pay_way"
                    >
                        <view class="flex items-center">
                            <u-icon
                                :name="item.icon"
                                size="24"
                                :color="
                                    selectedPayWay === item.pay_way
                                        ? themeStore.primaryColor
                                        : '#666'
                                "
                            ></u-icon>
                            <view class="ml-2">
                                <view class="text-sm font-medium">{{ item.name }}</view>
                                <view class="text-xs text-gray-400" v-if="item.extra">{{
                                    item.extra
                                }}</view>
                            </view>
                        </view>
                        <u-radio
                            :active-color="themeStore.primaryColor"
                            :checked="selectedPayWay === item.pay_way"
                        ></u-radio>
                    </view>
                </view>
            </view>

            <u-button type="primary" shape="circle" block @click="handleRecharge"
                >立即充值</u-button
            >
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { recharge, rechargeConfig } from '@/api/recharge'
import { prepay, getPayWay } from '@/api/pay'
import { getUserCenter } from '@/api/user'
import { useThemeStore } from '@/stores/theme'
import { formatMoney } from '@/utils/money'

const themeStore = useThemeStore()
const balance = ref('0.00')
const amountList = [10, 20, 50, 100, 200, 500]
const selectedAmount = ref(10)
const customAmount = ref('')
const minRecharge = ref(0)
const payWayList = ref<any[]>([])
const selectedPayWay = ref(1)

const getData = async () => {
    try {
        const res = await getUserCenter()
        balance.value = formatMoney(res.user_money)

        const config = await rechargeConfig()
        if (config.min_recharge) {
            minRecharge.value = Number(config.min_recharge)
        }
    } catch (e) {
        console.error(e)
    }
}

// 获取支付方式
const getPayWayList = async () => {
    try {
        const res = await getPayWay({
            order_id: 0,
            from: 'recharge'
        })
        if (res && res.lists && Array.isArray(res.lists)) {
            payWayList.value = res.lists
            const defaultPay = res.lists.find((item: any) => item.is_default) || res.lists[0]
            if (defaultPay) {
                selectedPayWay.value = defaultPay.pay_way
            }
        }
    } catch (e) {
        console.error('获取支付方式失败:', e)
    }
}

const handleRecharge = async () => {
    const money = selectedAmount.value || Number(customAmount.value)
    if (!money || money <= 0) return uni.$u.toast('请输入有效金额')
    if (minRecharge.value > 0 && money < minRecharge.value) {
        return uni.$u.toast(`最小充值金额为${minRecharge.value}元`)
    }

    // 检查选中的支付方式是否可用
    const selectedPayInfo = payWayList.value.find((item) => item.pay_way === selectedPayWay.value)
    if (!selectedPayInfo) {
        return uni.$u.toast('请选择支付方式')
    }

    uni.showLoading({ title: '创建订单中...' })
    try {
        // 1. 创建充值订单
        const orderRes = await recharge({ money, pay_way: selectedPayWay.value })
        const orderId = orderRes.order_id
        const from = orderRes.from

        // 2. 获取预支付参数
        const payRes = await prepay({
            order_id: orderId,
            from,
            pay_way: selectedPayWay.value
        })

        uni.hideLoading()

        // 3. 唤起支付
        // #ifdef MP-WEIXIN
        uni.requestPayment({
            provider: 'wxpay',
            ...payRes.config,
            success: async () => {
                uni.$u.toast('充值成功')
                await getData()
                selectedAmount.value = 10
                customAmount.value = ''
            },
            fail: (err) => {
                console.error(err)
                if (err.errMsg !== 'requestPayment:fail cancel') {
                    uni.$u.toast('支付失败')
                }
            }
        })
        // #endif

        // #ifdef H5
        if (payRes.pay_way == 2) {
            window.location.href = payRes.config.mweb_url
        } else if (payRes.pay_way == 3) {
            window.location.href = payRes.config.alipay_url
        }
        // #endif
    } catch (e: any) {
        uni.hideLoading()
        console.error('[充值失败]:', e)
        // 安全获取错误信息，避免 e.message 不存在
        const errorMsg = e?.message || e?.err_msg || '充值失败'
        uni.$u.toast(errorMsg)
    }
}

onMounted(() => {
    getData()
    getPayWayList()
})
</script>

<style scoped></style>
