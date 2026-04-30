<template>
    <view
        class="w-recharge-panel p-4 -mt-4 bg-white rounded-t-xl relative z-10"
        :style="{
            paddingTop: `${styles.padding_top}px`,
            paddingBottom: `${styles.padding_bottom}px`
        }"
    >
        <!-- 充值金额 -->
        <view class="mb-6">
            <view class="text-base font-bold mb-3">选择充值金额</view>
            <view class="grid grid-cols-3 gap-3">
                <view
                    v-for="(item, index) in amounts"
                    :key="index"
                    class="flex items-center justify-center h-16 rounded-lg border-2 transition-all text-lg font-medium"
                    :class="money == item.value ? '' : 'border-gray-100 bg-gray-50 text-gray-600'"
                    :style="
                        money == item.value
                            ? {
                                  borderColor: 'var(--color-primary)',
                                  backgroundColor: 'var(--color-primary-light-9)',
                                  color: 'var(--color-primary)'
                              }
                            : {}
                    "
                    @click="money = item.value"
                >
                    {{ item.text }}
                </view>
            </view>
            <!-- 自定义金额 -->
            <view
                v-if="content.show_custom_amount !== 0"
                class="mt-3 flex items-center bg-gray-50 rounded-lg px-4 h-14 border border-gray-100"
            >
                <text class="text-gray-600 font-bold mr-2">¥</text>
                <input
                    v-model="money"
                    type="digit"
                    class="flex-1 h-full text-lg"
                    placeholder="请输入自定义充值金额"
                    placeholder-class="text-gray-400 text-sm"
                />
                <text class="text-gray-600 ml-2">元</text>
            </view>
        </view>

        <!-- 确认按钮 -->
        <view class="mt-8">
            <u-button
                type="primary"
                shape="circle"
                :custom-style="{
                    backgroundColor: content.btn_color,
                    borderColor: content.btn_color,
                    height: '88rpx',
                    fontSize: '32rpx'
                }"
                :loading="isLock"
                @click="rechargeLock"
            >
                {{ content.btn_text }}
            </u-button>
        </view>

        <!-- 说明 -->
        <view class="mt-8 text-xs text-gray-400 leading-relaxed whitespace-pre-wrap">
            <view class="mb-1">充值说明：</view>
            {{ content.notice }}
        </view>

        <!-- 支付组件 -->
        <payment
            v-model:show="payState.showPay"
            v-model:show-check="payState.showCheck"
            :order-id="payState.orderId"
            :from="payState.from"
            :redirect="payState.redirect"
            @success="handlePaySuccess"
            @fail="handlePayFail"
        />
    </view>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue'
import { recharge, rechargeConfig } from '@/api/recharge'
import { useLockFn } from '@/hooks/useLockFn'

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

const amounts = computed(() => {
    if (props.content.amounts && props.content.amounts.length > 0) {
        return props.content.amounts
    }
    // 默认兜底数据
    return [
        { value: 10, text: '10元' },
        { value: 20, text: '20元' },
        { value: 30, text: '30元' },
        { value: 50, text: '50元' },
        { value: 100, text: '100元' },
        { value: 200, text: '200元' }
    ]
})

const money = ref('')
const wallet = reactive({
    min_amount: 0
})

const payState = reactive({
    orderId: 0,
    from: '',
    showPay: false,
    showCheck: false,
    redirect: '/packages/pages/user_wallet/user_wallet'
})

// 获取钱包配置
const getWalletConfig = async () => {
    try {
        const data = await rechargeConfig()
        Object.assign(wallet, data)
    } catch (e) {
        console.error(e)
    }
}
getWalletConfig()

const { isLock, lockFn: rechargeLock } = useLockFn(async () => {
    if (!money.value) return uni.$u.toast('请选择或输入充值金额')
    const amount = Number(money.value)
    if (isNaN(amount) || amount <= 0) return uni.$u.toast('充值金额必须大于0')
    if (amount < wallet.min_amount)
        return uni.$u.toast(`最低充值金额${wallet.min_amount}元`)

    const data = await recharge({
        money: amount
    })

    payState.orderId = data.order_id
    payState.from = data.from
    payState.showPay = true
})

const handlePaySuccess = () => {
    payState.showPay = false
    payState.showCheck = false
    uni.$u.toast('充值成功')
    // 刷新页面或余额
    uni.$emit('refreshWallet')
}

const handlePayFail = () => {
    uni.$u.toast('支付取消或失败')
}
</script>

<style lang="scss" scoped></style>
