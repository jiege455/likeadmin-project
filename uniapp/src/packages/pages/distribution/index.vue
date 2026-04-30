<template>
    <uni-nav title="推广中心" bg-color="transparent" text-color="#ffffff"></uni-nav>

    <view class="distribution-index min-h-screen bg-f5">
        <template v-if="!info.is_distributor">
            <view
                class="header pb-6 px-4"
                :style="{ backgroundColor: themeStore.primaryColor, color: '#ffffff' }"
            >
                <view class="text-center py-8">
                    <u-icon name="share" size="60" color="#ffffff"></u-icon>
                    <view class="text-lg mt-3">成为推广员</view>
                    <view class="text-sm opacity-80 mt-1">推广赚佣金，轻松获取收益</view>
                </view>
            </view>

            <view class="px-4 -mt-3">
                <view class="bg-white rounded-xl p-4 shadow-sm">
                    <view class="text-lg font-bold mb-4">申请成为推广员</view>
                    <view class="mb-3">
                        <view class="text-sm text-gray-500 mb-1">真实姓名</view>
                        <u-input
                            v-model="applyForm.name"
                            placeholder="请输入真实姓名"
                            border="surround"
                        />
                    </view>
                    <view class="mb-3">
                        <view class="text-sm text-gray-500 mb-1">手机号</view>
                        <u-input
                            v-model="applyForm.mobile"
                            placeholder="请输入手机号"
                            border="surround"
                            type="number"
                        />
                    </view>
                    <!-- 手机验证码 -->
                    <view v-if="needMobile" class="mb-3">
                        <view class="text-sm text-gray-500 mb-1">手机验证码</view>
                        <view class="flex items-center">
                            <u-input
                                v-model="applyForm.code"
                                placeholder="请输入手机验证码"
                                border="surround"
                                type="number"
                                class="flex-1"
                            />
                            <view
                                class="text-sm whitespace-nowrap ml-2"
                                :style="{ color: themeStore.primaryColor, opacity: mobileCountdown > 0 || !applyForm.mobile ? 0.5 : 1 }"
                                @click="sendSmsCode"
                            >
                                {{ mobileCountdown > 0 ? `${mobileCountdown}秒后重发` : '获取验证码' }}
                            </view>
                        </view>
                    </view>
                    <!-- 邮箱 -->
                    <view v-if="needEmail" class="mb-3">
                        <view class="text-sm text-gray-500 mb-1">邮箱</view>
                        <u-input
                            v-model="applyForm.email"
                            placeholder="请输入邮箱"
                            border="surround"
                        />
                    </view>
                    <!-- 邮箱验证码 -->
                    <view v-if="needEmail" class="mb-3">
                        <view class="text-sm text-gray-500 mb-1">邮箱验证码</view>
                        <view class="flex items-center">
                            <u-input
                                v-model="applyForm.email_code"
                                placeholder="请输入邮箱验证码"
                                border="surround"
                                class="flex-1"
                            />
                            <view
                                class="text-sm whitespace-nowrap ml-2"
                                :style="{ color: themeStore.primaryColor, opacity: emailCountdown > 0 || !applyForm.email ? 0.5 : 1 }"
                                @click="sendEmailCode"
                            >
                                {{ emailCountdown > 0 ? `${emailCountdown}秒后重发` : '获取验证码' }}
                            </view>
                        </view>
                    </view>
                    <view class="mb-4">
                        <view class="text-sm text-gray-500 mb-1">申请理由（选填）</view>
                        <u-input
                            v-model="applyForm.reason"
                            placeholder="请输入申请理由"
                            border="surround"
                            type="textarea"
                        />
                    </view>
                    <u-button
                        :text="applyStatus == 0 ? '审核中' : '提交申请'"
                        :disabled="applyStatus == 0"
                        @click="submitApply"
                        :color="themeStore.primaryColor"
                    ></u-button>
                    <view v-if="applyStatus == 2" class="text-center text-red-500 text-sm mt-2"
                        >拒绝原因：{{ applyReason }}</view
                    >
                </view>
            </view>
        </template>

        <!-- 推广员 -->
        <template v-else>
            <view
                class="header pb-6 px-4"
                :style="{ backgroundColor: themeStore.primaryColor, color: '#ffffff' }"
            >
                <view class="grid grid-cols-3 gap-4 text-center">
                    <view>
                        <view class="text-2xl font-bold">¥{{ info.commission }}</view>
                        <view class="text-xs opacity-80 mt-1">可提现佣金</view>
                    </view>
                    <view>
                        <view class="text-2xl font-bold">¥{{ info.total_commission }}</view>
                        <view class="text-xs opacity-80 mt-1">累计佣金</view>
                    </view>
                    <view>
                        <view class="text-2xl font-bold">{{ info.order_count }}</view>
                        <view class="text-xs opacity-80 mt-1">推广订单</view>
                    </view>
                </view>
            </view>

            <!-- 功能入口 -->
            <view class="bg-white mx-3 -mt-3 rounded-xl p-4 shadow-sm relative z-10">
                <view class="flex">
                    <view class="flex-1 text-center" @click="toWithdraw">
                        <view
                            class="w-12 h-12 mx-auto rounded-full flex items-center justify-center"
                            :style="{ backgroundColor: themeStore.primaryColor + '20' }"
                        >
                            <u-icon
                                name="rmb-circle"
                                size="24"
                                :color="themeStore.primaryColor"
                            ></u-icon>
                        </view>
                        <view class="text-sm mt-2">申请提现</view>
                    </view>
                    <view class="flex-1 text-center" @click="toWithdrawLog">
                        <view
                            class="w-12 h-12 mx-auto rounded-full flex items-center justify-center"
                            :style="{ backgroundColor: themeStore.primaryColor + '20' }"
                        >
                            <u-icon name="list" size="24" :color="themeStore.primaryColor"></u-icon>
                        </view>
                        <view class="text-sm mt-2">提现记录</view>
                    </view>
                    <view class="flex-1 text-center" @click="toOrders">
                        <view
                            class="w-12 h-12 mx-auto rounded-full flex items-center justify-center"
                            :style="{ backgroundColor: themeStore.primaryColor + '20' }"
                        >
                            <u-icon
                                name="order"
                                size="24"
                                :color="themeStore.primaryColor"
                            ></u-icon>
                        </view>
                        <view class="text-sm mt-2">推广订单</view>
                    </view>
                </view>
            </view>

            <!-- 今日/本月数据 -->
            <view class="bg-white mx-3 mt-3 rounded-xl p-4 shadow-sm">
                <view class="flex justify-between text-center">
                    <view class="flex-1">
                        <view class="text-xl font-bold text-orange-500"
                            >¥{{ info.today_commission }}</view
                        >
                        <view class="text-xs text-gray-400 mt-1">今日佣金</view>
                    </view>
                    <view class="flex-1 border-l border-gray-100">
                        <view class="text-xl font-bold text-green-500"
                            >¥{{ info.month_commission }}</view
                        >
                        <view class="text-xs text-gray-400 mt-1">本月佣金</view>
                    </view>
                </view>
            </view>

            <!-- 推广说明 -->
            <view class="bg-white mx-3 mt-3 rounded-xl p-4 shadow-sm">
                <view class="font-bold mb-2">推广规则</view>
                <view class="text-sm text-gray-500 leading-relaxed">
                    1. 分享文章链接给好友，好友通过您的链接购买文章，您可获得佣金奖励。<br />
                    2. 佣金比例由文章作者设置，最高可达50%。<br />
                    3. 佣金可随时申请提现，审核通过后打款到您的账户。<br />
                    4. 自己购买的文章不会产生佣金。
                </view>
            </view>
        </template>
    </view>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import request from '@/utils/request'
import { onShow } from '@dcloudio/uni-app'
import { useThemeStore } from '@/stores/theme'
import { safeNavigateBack } from '@/utils/util'
import { getSwitchConfig } from '@/api/system'
import { smsSend } from '@/api/app'
import { emailSendCode } from '@/api/email'
import { SMSEnum, EmailEnum } from '@/enums/appEnums'

const themeStore = useThemeStore()
const themeStyle = computed(() => `--color-primary: ${themeStore.primaryColor};`)

const switchConfig = ref({
    distributor_apply_verify_type: 'email',
    email_notify_open: 0,
    sms_notify_open: 1
})

const verifyType = computed(() => switchConfig.value.distributor_apply_verify_type)
const needMobile = computed(() => switchConfig.value.sms_notify_open && verifyType.value === 'mobile')
const needEmail = computed(() => switchConfig.value.email_notify_open && verifyType.value === 'email')

const info = ref({
    is_distributor: 0,
    commission: '0.00',
    total_commission: '0.00',
    today_commission: '0.00',
    month_commission: '0.00',
    order_count: 0
})

const applyStatus = ref(-1)
const applyReason = ref('')
const applyForm = ref({
    name: '',
    mobile: '',
    reason: '',
    code: '',
    email: '',
    email_code: ''
})

const mobileCountdown = ref(0)
const emailCountdown = ref(0)
let mobileTimer: ReturnType<typeof setInterval> | null = null
let emailTimer: ReturnType<typeof setInterval> | null = null

const fetchSwitchConfig = async () => {
    try {
        const res = await getSwitchConfig()
        switchConfig.value = {
            distributor_apply_verify_type: res.distributor_apply_verify_type || 'email',
            email_notify_open: res.email_notify_open || 0,
            sms_notify_open: res.sms_notify_open || 1
        }
    } catch (e) {
        console.error('获取配置失败', e)
    }
}

const sendSmsCode = async () => {
    if (!applyForm.value.mobile) return uni.$u.toast('请输入手机号')
    if (!/^1[3-9]\d{9}$/.test(applyForm.value.mobile)) return uni.$u.toast('手机号格式错误')
    if (mobileCountdown.value > 0) return

    try {
        await smsSend({ scene: SMSEnum.DISTRIBUTOR_APPLY, mobile: applyForm.value.mobile })
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
    if (!applyForm.value.email) return uni.$u.toast('请输入邮箱')
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(applyForm.value.email)) return uni.$u.toast('邮箱格式错误')
    if (emailCountdown.value > 0) return

    try {
        await emailSendCode({ scene: EmailEnum.DISTRIBUTOR_APPLY, email: applyForm.value.email })
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
        const res = await request.get({ url: '/distribution/applyDetail' })
        info.value.is_distributor = res.is_distributor || 0
        applyStatus.value = res.apply?.status ?? -1
        applyReason.value = res.apply?.audit_remark || ''

        if (res.is_distributor == 1) {
            const detail = await request.get({ url: '/distribution/index' })
            info.value = { ...info.value, ...detail }
        }
    } catch (e) {}
}

const submitApply = async () => {
    if (!applyForm.value.name) return uni.$u.toast('请输入真实姓名')
    if (!applyForm.value.mobile) return uni.$u.toast('请输入手机号')

    if (needMobile.value) {
        if (!applyForm.value.code) return uni.$u.toast('请输入手机验证码')
    }

    if (needEmail.value) {
        if (!applyForm.value.email) return uni.$u.toast('请输入邮箱')
        if (!applyForm.value.email_code) return uni.$u.toast('请输入邮箱验证码')
    }

    try {
        await request.post({ url: '/distribution/apply', data: applyForm.value })
        uni.$u.toast('申请成功')
        getInfo()
    } catch (e: any) {
        uni.$u.toast(e?.msg || '申请失败')
    }
}

const toWithdraw = () => {
    uni.navigateTo({ url: '/packages/pages/distribution/withdraw' })
}

const toWithdrawLog = () => {
    uni.navigateTo({ url: '/packages/pages/distribution/withdraw_log' })
}

const toOrders = () => {
    uni.navigateTo({ url: '/packages/pages/distribution/order' })
}

const goBack = () => {
    safeNavigateBack({ defaultUrl: '/pages/index/index' })
}

onShow(() => {
    uni.setNavigationBarTitle({ title: '推广中心' })
    fetchSwitchConfig()
    getInfo()
    themeStore.getTheme()
})
</script>

<style lang="scss" scoped>
.distribution-index {
    background-color: #f5f5f5;
}
.bg-f5 {
    background-color: #f5f5f5;
}
</style>
