<template>
    <uni-nav title="申请提现" text-color="#ffffff"></uni-nav>

    <view class="distribution-withdraw min-h-screen bg-f5">
        <view class="bg-white m-3 rounded-xl p-4 shadow-sm">
            <view class="text-center py-4">
                <view class="text-sm text-gray-400">可提现佣金</view>
                <view class="text-3xl font-bold text-red-500 mt-2">¥{{ info.commission }}</view>
            </view>

            <view class="border-t border-gray-100 pt-4">
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
            <view class="mt-4">
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

            <!-- 手机验证码 -->
            <view v-if="needMobile" class="mt-4">
                <view class="text-sm text-gray-500 mb-2">手机验证码</view>
                <view class="flex items-center">
                    <u-input
                        v-model="formData.code"
                        placeholder="请输入手机验证码"
                        border="surround"
                        type="number"
                        class="flex-1"
                    />
                    <view
                        class="text-sm whitespace-nowrap ml-2"
                        :style="{ color: themeStore.primaryColor, opacity: mobileCountdown > 0 ? 0.5 : 1 }"
                        @click="sendSmsCode"
                    >
                        {{ mobileCountdown > 0 ? `${mobileCountdown}秒后重发` : '获取验证码' }}
                    </view>
                </view>
                <view class="text-xs text-gray-400 mt-1">验证码将发送至已绑定手机号</view>
            </view>

            <!-- 邮箱验证 -->
            <view v-if="needEmail" class="mt-4">
                <view class="text-sm text-gray-500 mb-2">邮箱</view>
                <u-input
                    v-model="formData.email"
                    :placeholder="userInfo.email || '请输入邮箱'"
                    border="surround"
                    :disabled="!!userInfo.email"
                />
            </view>

            <!-- 邮箱验证码 -->
            <view v-if="needEmail" class="mt-4">
                <view class="text-sm text-gray-500 mb-2">邮箱验证码</view>
                <view class="flex items-center">
                    <u-input
                        v-model="formData.email_code"
                        placeholder="请输入邮箱验证码"
                        border="surround"
                        class="flex-1"
                    />
                    <view
                        class="text-sm whitespace-nowrap ml-2"
                        :style="{ color: themeStore.primaryColor, opacity: emailCountdown > 0 || (!formData.email && !userInfo.email) ? 0.5 : 1 }"
                        @click="sendEmailCode"
                    >
                        {{ emailCountdown > 0 ? `${emailCountdown}秒后重发` : '获取验证码' }}
                    </view>
                </view>
            </view>

            <view class="mt-6">
                <u-button
                    text="提交申请"
                    @click="submit"
                    :color="themeStore.primaryColor"
                ></u-button>
            </view>
        </view>

        <view class="bg-white mx-3 mt-3 rounded-xl p-4 shadow-sm">
            <view class="text-sm text-gray-500">
                <view class="font-bold text-gray-700 mb-2">提现说明</view>
                <view>1. 提现申请提交后，将在{{ arrivalDays }}个工作日内审核</view>
                <view>2. 审核通过后，款项将打入您选择的收款账户</view>
                <view>3. 如审核拒绝，金额将退回您的佣金账户</view>
                <view v-if="withdrawFee > 0">4. 提现手续费：{{ withdrawFee }}%</view>
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue'
import request from '@/utils/request'
import { onShow } from '@dcloudio/uni-app'
import { useThemeStore } from '@/stores/theme'
import { safeNavigateBack } from '@/utils/util'
import { getSwitchConfig } from '@/api/system'
import { smsSend } from '@/api/app'
import { emailSendCode } from '@/api/email'
import { SMSEnum, EmailEnum } from '@/enums/appEnums'
import { useUserStore } from '@/stores/user'

const themeStore = useThemeStore()
const userStore = useUserStore()
const themeStyle = computed(() => `--color-primary: ${themeStore.primaryColor};`)

const switchConfig = ref({
    withdraw_verify_type: 'email',
    email_notify_open: 0,
    sms_notify_open: 1
})

const verifyType = computed(() => switchConfig.value.withdraw_verify_type)
const needMobile = computed(() => switchConfig.value.sms_notify_open && verifyType.value === 'mobile')
const needEmail = computed(() => switchConfig.value.email_notify_open && verifyType.value === 'email')

const userInfo = computed(() => userStore.userInfo)

const info = ref({
    commission: '0.00'
})

const formData = reactive({
    money: '',
    account_id: '',
    code: '',
    email: '',
    email_code: ''
})

const accountList = ref<any[]>([])
const selectedAccount = ref<any>(null)
const minWithdraw = ref(10)
const withdrawFee = ref(0)
const arrivalDays = ref(3)

const mobileCountdown = ref(0)
const emailCountdown = ref(0)
let mobileTimer: ReturnType<typeof setInterval> | null = null
let emailTimer: ReturnType<typeof setInterval> | null = null

declare const getCurrentPages: any

const fetchSwitchConfig = async () => {
    try {
        const res = await getSwitchConfig()
        switchConfig.value = {
            withdraw_verify_type: res.withdraw_verify_type || 'email',
            email_notify_open: res.email_notify_open || 0,
            sms_notify_open: res.sms_notify_open || 1
        }
    } catch (e) {
        console.error('获取配置失败', e)
    }
}

const sendSmsCode = async () => {
    const mobile = userInfo.value.mobile
    if (!mobile) return uni.$u.toast('请先绑定手机号')
    if (mobileCountdown.value > 0) return

    try {
        await smsSend({ scene: SMSEnum.WITHDRAW, mobile })
        uni.$u.toast('验证码已发送')
        mobileCountdown.value = 60
        mobileTimer = setInterval(() => {
            mobileCountdown.value--
            if (mobileCountdown.value <= 0 && mobileTimer) {
                clearInterval(mobileTimer)
                mobileTimer = null
            }
        }, 1000)
    } catch (e: any) {
        uni.$u.toast(e || '发送失败')
    }
}

const sendEmailCode = async () => {
    const email = formData.email || userInfo.value.email
    if (!email) return uni.$u.toast('请输入邮箱')
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) return uni.$u.toast('邮箱格式错误')
    if (emailCountdown.value > 0) return

    try {
        await emailSendCode({ scene: EmailEnum.WITHDRAW, email })
        uni.$u.toast('验证码已发送')
        emailCountdown.value = 60
        emailTimer = setInterval(() => {
            emailCountdown.value--
            if (emailCountdown.value <= 0 && emailTimer) {
                clearInterval(emailTimer)
                emailTimer = null
            }
        }, 1000)
    } catch (e: any) {
        uni.$u.toast(e || '发送失败')
    }
}

const getInfo = async () => {
    try {
        const res = await request.get({ url: '/distribution.index/info' })
        info.value = res
    } catch (e) {}
}

const getAccountList = async () => {
    try {
        const res = await request.get({ url: '/withdraw.account/lists' })
        accountList.value = res.lists || []

        if (accountList.value.length > 0) {
            const defaultAccount = accountList.value.find((item: any) => item.is_default == 1)
            selectedAccount.value = defaultAccount || accountList.value[0]
            formData.account_id = selectedAccount.value.id
        }
    } catch (e) {}
}

const getWithdrawConfig = async () => {
    try {
        const res = await request.get({ url: '/distribution.withdraw/config' })
        minWithdraw.value = res.min_withdraw || 10
        withdrawFee.value = res.withdraw_fee || 0
        arrivalDays.value = res.arrival_days || 3
    } catch (e) {}
}

const allWithdraw = () => {
    formData.money = info.value.commission
}

const toAccountList = () => {
    uni.navigateTo({ url: '/packages/pages/user/withdraw_account' })
}

const submit = async () => {
    if (!formData.money) return uni.$u.toast('请输入提现金额')
    if (parseFloat(formData.money) < minWithdraw.value)
        return uni.$u.toast(`最低提现金额为${minWithdraw.value}元`)
    if (parseFloat(formData.money) > parseFloat(info.value.commission))
        return uni.$u.toast('佣金余额不足')
    if (!selectedAccount.value) return uni.$u.toast('请选择收款账户')

    if (needMobile.value) {
        if (!formData.code) return uni.$u.toast('请输入手机验证码')
    }

    if (needEmail.value) {
        const email = formData.email || userInfo.value.email
        if (!email) return uni.$u.toast('请输入邮箱')
        if (!formData.email_code) return uni.$u.toast('请输入邮箱验证码')
    }

    try {
        await request.post({
            url: '/distribution.withdraw/apply',
            data: {
                money: formData.money,
                account_id: selectedAccount.value.id,
                code: formData.code,
                email: formData.email || userInfo.value.email,
                email_code: formData.email_code
            }
        })
        uni.$u.toast('申请成功')
        setTimeout(() => safeNavigateBack({ defaultUrl: '/pages/index/index' }), 1500)
    } catch (e: any) {
        uni.$u.toast(e?.msg || '申请失败')
    }
}

const goBack = () => {
    safeNavigateBack({ defaultUrl: '/pages/index/index' })
}

onShow(() => {
    uni.setNavigationBarTitle({ title: '申请提现' })
    fetchSwitchConfig()
    userStore.getUser()
    getInfo()
    getAccountList()
    getWithdrawConfig()
    themeStore.getTheme()
})

onUnmounted(() => {
    if (mobileTimer) {
        clearInterval(mobileTimer)
        mobileTimer = null
    }
    if (emailTimer) {
        clearInterval(emailTimer)
        emailTimer = null
    }
})
</script>

<style lang="scss" scoped>
.distribution-withdraw {
    background-color: #f5f5f5;
}
.bg-f5 {
    background-color: #f5f5f5;
}
</style>
