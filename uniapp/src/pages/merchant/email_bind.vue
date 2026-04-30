<template>
    <view class="email-bind-page">
        <view class="header bg-white p-4 mb-2">
            <view class="text-lg font-bold">邮箱绑定</view>
            <view class="text-gray-400 text-sm mt-1">绑定邮箱后可接收订单通知</view>
        </view>

        <view class="bg-white p-4">
            <view v-if="emailInfo.email && emailInfo.email_verify" class="text-center py-6">
                <u-icon name="checkmark-circle" size="60" color="#19be6b"></u-icon>
                <view class="mt-4 text-gray-600">已绑定邮箱</view>
                <view class="mt-2 text-lg font-bold">{{ maskedEmail }}</view>
                <view class="mt-4">
                    <u-button type="primary" size="small" @click="showBindModal = true"
                        >更换邮箱</u-button
                    >
                </view>
            </view>

            <view v-else class="text-center py-6">
                <u-icon name="info-circle" size="60" color="#ff9900"></u-icon>
                <view class="mt-4 text-gray-600">暂未绑定邮箱</view>
                <view class="mt-4">
                    <u-button type="primary" @click="showBindModal = true">立即绑定</u-button>
                </view>
            </view>

            <view class="mt-6 border-t pt-4">
                <view class="flex justify-between items-center py-3">
                    <view>邮件通知</view>
                    <u-switch v-model="emailInfo.email_notify" @change="updateNotify"></u-switch>
                </view>
                <view class="text-gray-400 text-sm">开启后将接收订单、提现等邮件通知</view>
            </view>
        </view>

        <u-popup v-model="showBindModal" mode="bottom" :closeable="true" border-radius="20">
            <view class="p-6">
                <view class="text-lg font-bold text-center mb-6">绑定邮箱</view>

                <view class="mb-4">
                    <u-input
                        v-model="bindForm.email"
                        placeholder="请输入邮箱地址"
                        border="surround"
                        :clearable="true"
                    />
                </view>

                <view class="mb-4 flex">
                    <u-input
                        v-model="bindForm.code"
                        placeholder="请输入验证码"
                        border="surround"
                        class="flex-1 mr-2"
                    />
                    <u-button
                        type="primary"
                        size="small"
                        :disabled="countdown > 0 || !bindForm.email"
                        @click="sendCode"
                    >
                        {{ countdown > 0 ? `${countdown}秒后重发` : '获取验证码' }}
                    </u-button>
                </view>

                <u-button type="primary" @click="bindEmail" :loading="loading">确认绑定</u-button>
            </view>
        </u-popup>
    </view>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { emailSendCode } from '@/api/email'
import { getMerchantEmailInfo, bindMerchantEmail, updateMerchantEmailNotify } from '@/api/merchant'
import { EmailEnum } from '@/enums/appEnums'

const showBindModal = ref(false)
const loading = ref(false)
const countdown = ref(0)
let timer: ReturnType<typeof setInterval> | null = null

const emailInfo = ref({
    email: '',
    email_verify: 0,
    email_notify: 1
})

const bindForm = ref({
    email: '',
    code: ''
})

const maskedEmail = computed(() => {
    if (!emailInfo.value.email) return ''
    const [name, domain] = emailInfo.value.email.split('@')
    const maskedName = name.length > 2 ? name[0] + '***' + name[name.length - 1] : name[0] + '***'
    return maskedName + '@' + domain
})

const getEmailInfo = async () => {
    try {
        const res = await getMerchantEmailInfo()
        emailInfo.value = res || { email: '', email_verify: 0, email_notify: 1 }
    } catch (e) {
        console.error(e)
    }
}

const sendCode = async () => {
    if (!bindForm.value.email) {
        uni.$u.toast('请输入邮箱地址')
        return
    }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(bindForm.value.email)) {
        uni.$u.toast('邮箱格式错误')
        return
    }
    if (countdown.value > 0) return

    try {
        await emailSendCode({ scene: EmailEnum.MERCHANT_BIND_EMAIL, email: bindForm.value.email })
        uni.$u.toast('验证码已发送')
        countdown.value = 60
        timer = setInterval(() => {
            countdown.value--
            if (countdown.value <= 0 && timer) {
                clearInterval(timer)
                timer = null
            }
        }, 1000)
    } catch (e: any) {
        uni.$u.toast(e || '发送失败')
    }
}

const bindEmail = async () => {
    if (!bindForm.value.email) {
        uni.$u.toast('请输入邮箱地址')
        return
    }
    if (!bindForm.value.code) {
        uni.$u.toast('请输入验证码')
        return
    }

    loading.value = true
    try {
        await bindMerchantEmail({ email: bindForm.value.email, code: bindForm.value.code })
        uni.$u.toast('绑定成功')
        showBindModal.value = false
        bindForm.value = { email: '', code: '' }
        getEmailInfo()
    } catch (e: any) {
        uni.$u.toast(e || '绑定失败')
    } finally {
        loading.value = false
    }
}

const updateNotify = async () => {
    try {
        await updateMerchantEmailNotify({ email_notify: emailInfo.value.email_notify ? 1 : 0 })
        uni.$u.toast('设置成功')
    } catch (e: any) {
        uni.$u.toast(e || '设置失败')
        emailInfo.value.email_notify = emailInfo.value.email_notify ? 0 : 1
    }
}

onMounted(() => {
    getEmailInfo()
})
</script>

<style lang="scss" scoped>
.email-bind-page {
    min-height: 100vh;
    background-color: #f8f8f8;
}
</style>
