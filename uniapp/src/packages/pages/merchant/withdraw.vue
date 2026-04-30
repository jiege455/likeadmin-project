<template>
    <uni-nav title="申请提现"></uni-nav>

    <view class="withdraw min-h-screen bg-f5">
        <!-- 表单区域 -->
        <view class="content-area bg-white mx-3 mt-3 rounded-xl p-4 relative z-10 shadow-sm">
            <view class="border-b border-gray-100 pb-4 mb-4">
                <view class="text-sm text-gray-500 mb-2">提现金额</view>
                <view class="flex items-center border-b border-gray-200 pb-2">
                    <text class="text-xl">¥</text>
                    <input
                        type="digit"
                        v-model="formData.money"
                        placeholder="请输入提现金额"
                        class="flex-1 text-xl ml-2"
                    />
                    <view
                        class="text-sm"
                        :style="{ color: themeStore.primaryColor }"
                        @click="allWithdraw"
                        >全部提现</view
                    >
                </view>
                <view class="text-xs text-gray-400 mt-1">最低提现金额：¥{{ minWithdraw }}</view>
            </view>

            <!-- 收款账户选择 -->
            <view class="border-b border-gray-100 pb-4 mb-4">
                <view class="flex items-center justify-between mb-2">
                    <view class="text-sm text-gray-500">收款账户</view>
                    <view
                        class="text-sm"
                        :style="{ color: themeStore.primaryColor }"
                        @click="toAccountList"
                    >
                        <text>{{ accountList.length > 0 ? '更换账户' : '添加账户' }}</text>
                        <u-icon
                            name="arrow-right"
                            size="12"
                            :color="themeStore.primaryColor"
                        ></u-icon>
                    </view>
                </view>

                <!-- 已选账户 -->
                <view
                    v-if="selectedAccount"
                    class="border border-gray-200 rounded-lg p-3"
                    @click="toAccountList"
                >
                    <view class="flex items-center">
                        <view
                            class="w-10 h-10 rounded-full flex items-center justify-center"
                            :class="
                                selectedAccount.type == 2
                                    ? 'bg-blue-100'
                                    : selectedAccount.type == 1
                                    ? 'bg-green-100'
                                    : 'bg-orange-100'
                            "
                        >
                            <u-icon
                                :name="
                                    selectedAccount.type == 2
                                        ? 'zhifubao'
                                        : selectedAccount.type == 1
                                        ? 'weixin-fill'
                                        : 'integral-fill'
                                "
                                size="20"
                                :color="
                                    selectedAccount.type == 2
                                        ? '#1677FF'
                                        : selectedAccount.type == 1
                                        ? '#07C160'
                                        : '#FF8C00'
                                "
                            ></u-icon>
                        </view>
                        <view class="ml-3 flex-1">
                            <view class="font-medium">{{ selectedAccount.type_text }}</view>
                            <view class="text-sm text-gray-500">{{
                                selectedAccount.account_mask
                            }}</view>
                            <view class="text-xs text-gray-400">{{
                                selectedAccount.real_name
                            }}</view>
                        </view>
                        <u-icon name="arrow-right" size="16" color="#999"></u-icon>
                    </view>
                </view>

                <!-- 未选账户 -->
                <view
                    v-else
                    class="border border-dashed border-gray-300 rounded-lg p-4 text-center"
                    @click="toAccountList"
                >
                    <u-icon name="plus" size="20" color="#999"></u-icon>
                    <view class="text-sm text-gray-400 mt-1">选择收款账户</view>
                </view>
            </view>

            <view class="mt-6">
                <u-button
                    type="primary"
                    shape="circle"
                    @click="submit"
                    :loading="loading"
                    :custom-style="{
                        backgroundColor: themeStore.primaryColor,
                        color: '#ffffff',
                        border: 'none'
                    }"
                    >确认提现</u-button
                >
            </view>
        </view>

        <!-- 提现记录入口 -->
        <view class="bg-white mx-3 mt-3 rounded-xl p-4 shadow-sm flex items-center justify-between" @click="toWithdrawLog">
            <view class="flex items-center">
                <u-icon name="list" size="20" color="#666"></u-icon>
                <text class="ml-2 text-sm text-gray-700">提现记录</text>
            </view>
            <view class="flex items-center text-sm text-gray-400">
                <text>查看全部</text>
                <u-icon name="arrow-right" size="14" color="#999"></u-icon>
            </view>
        </view>

        <view class="bg-white mx-3 mt-3 rounded-xl p-4 shadow-sm">
            <view class="text-sm text-gray-500">
                <view class="font-bold text-gray-700 mb-2">提现说明</view>
                <view>1. 提现申请提交后，将在{{ arrivalDays }}个工作日内审核</view>
                <view>2. 审核通过后，款项将打入您选择的收款账户</view>
                <view>3. 如审核拒绝，金额将退回您的账户余额</view>
                <view v-if="withdrawFee > 0">4. 提现手续费：{{ withdrawFee }}%</view>
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue'
import request from '@/utils/request'
import { onShow } from '@dcloudio/uni-app'
import { useThemeStore } from '@/stores/theme'
import { safeNavigateBack } from '@/utils/util'

const themeStore = useThemeStore()
const themeStyle = computed(() => `--color-primary: ${themeStore.primaryColor};`)

const merchantInfo = ref<any>({})
const loading = ref(false)
const accountList = ref<any[]>([])
const selectedAccount = ref<any>(null)
const minWithdraw = ref(100)
const withdrawFee = ref(0)
const arrivalDays = ref(3)
const merchantId = ref(0)

const formData = reactive({
    money: '',
    account_id: ''
})

const getInfo = async () => {
    try {
        const res = await request.get({ url: '/merchant.withdraw/info' })
        merchantInfo.value = res.merchant || res
        merchantId.value = res.merchant_id || res.merchant?.id || 0
        minWithdraw.value = res.min_withdraw || 100
        withdrawFee.value = res.withdraw_fee || 0
        arrivalDays.value = res.arrival_days || 3

        await getAccountList()
    } catch (e) {}
}

const getAccountList = async () => {
    try {
        const res = await request.get({
            url: '/withdraw.account/lists',
            data: { merchant_id: merchantId.value }
        })
        accountList.value = res.lists || []

        if (accountList.value.length > 0) {
            const defaultAccount = accountList.value.find((item: any) => item.is_default == 1)
            selectedAccount.value = defaultAccount || accountList.value[0]
            formData.account_id = selectedAccount.value.id
        }
    } catch (e) {}
}

const allWithdraw = () => {
    formData.money = merchantInfo.value.money || '0.00'
}

const toAccountList = () => {
    console.log('toAccountList called, merchantId:', merchantId.value)
    if (merchantId.value <= 0) {
        uni.showToast({ title: '请稍后再试', icon: 'none' })
        return
    }
    const url = `/packages/pages/user/withdraw_account?merchant_id=${merchantId.value}`
    console.log('navigateTo:', url)
    uni.navigateTo({ url })
}

const toWithdrawLog = () => {
    uni.navigateTo({ url: '/packages/pages/merchant/withdraw_log' })
}

const goBack = () => {
    safeNavigateBack({ defaultUrl: '/pages/index/index' })
}

const submit = async () => {
    if (!formData.money) return uni.showToast({ title: '请输入金额', icon: 'none' })
    if (parseFloat(formData.money) <= 0)
        return uni.showToast({ title: '金额必须大于0', icon: 'none' })
    if (parseFloat(formData.money) > parseFloat(merchantInfo.value.money))
        return uni.showToast({ title: '余额不足', icon: 'none' })
    if (parseFloat(formData.money) < minWithdraw.value)
        return uni.showToast({ title: `最低提现金额为${minWithdraw.value}元`, icon: 'none' })
    if (!selectedAccount.value) return uni.showToast({ title: '请选择收款账户', icon: 'none' })

    loading.value = true
    try {
        await request.post({
            url: '/merchant.withdraw/apply',
            data: {
                money: formData.money,
                account_id: selectedAccount.value.id
            }
        })
        uni.showToast({ title: '申请成功' })
        setTimeout(() => uni.navigateBack(), 1500)
    } catch (e: any) {
        uni.showToast({ title: e?.msg || '申请失败', icon: 'none' })
    } finally {
        loading.value = false
    }
}

onShow(() => {
    uni.setNavigationBarTitle({ title: '申请提现' })
    getInfo()
    themeStore.getTheme()
})
</script>

<style lang="scss" scoped>
.withdraw {
    background-color: #f5f5f5;
}
.bg-f5 {
    background-color: #f5f5f5;
}
</style>
