<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar :front-color="$theme.navColor" :background-color="$theme.navBgColor" />
        <!-- #endif -->
    </page-meta>
    <view class="guide-bind bg-white min-h-full flex flex-col items-center px-[40rpx] pt-[80rpx] box-border">
        <view class="w-full">
            <view class="text-center mb-[60rpx]">
                <u-icon :name="bindType === 'email' ? 'email' : 'phone'" size="80" color="#409eff"></u-icon>
                <view class="text-2xl font-medium mt-[30rpx]">
                    {{ bindType === 'email' ? '绑定邮箱' : '绑定手机号' }}
                </view>
                <view class="text-gray-400 text-sm mt-[20rpx]">
                    {{ bindType === 'email' ? '绑定邮箱后可接收邮件通知' : '绑定手机号后可接收短信通知' }}
                </view>
            </view>

            <view v-if="bindType === 'mobile'" class="mobile-bind">
                <view
                    class="px-[18rpx] border border-solid border-lightc border-light rounded-[10rpx] h-[100rpx] items-center flex"
                >
                    <u-input
                        class="flex-1"
                        v-model="formData.mobile"
                        :border="false"
                        placeholder="请输入手机号码"
                        type="number"
                    />
                </view>
                <view
                    class="px-[18rpx] border border-solid border-lightc border-light rounded-[10rpx] h-[100rpx] items-center flex mt-[40rpx]"
                >
                    <u-input
                        class="flex-1"
                        v-model="formData.code"
                        placeholder="请输入验证码"
                        type="number"
                        :border="false"
                    />
                    <view
                        class="text-primary text-sm whitespace-nowrap"
                        :class="{ 'opacity-50': countdown > 0 || !formData.mobile }"
                        @click="sendSmsCode"
                    >
                        {{ countdown > 0 ? `${countdown}秒后重发` : '获取验证码' }}
                    </view>
                </view>
            </view>

            <view v-if="bindType === 'email'" class="email-bind">
                <view
                    class="px-[18rpx] border border-solid border-lightc border-light rounded-[10rpx] h-[100rpx] items-center flex"
                >
                    <u-input
                        class="flex-1"
                        v-model="formData.email"
                        :border="false"
                        placeholder="请输入邮箱地址"
                    />
                </view>
                <view
                    class="px-[18rpx] border border-solid border-lightc border-light rounded-[10rpx] h-[100rpx] items-center flex mt-[40rpx]"
                >
                    <u-input
                        class="flex-1"
                        v-model="formData.email_code"
                        placeholder="请输入邮箱验证码"
                        :border="false"
                    />
                    <view
                        class="text-primary text-sm whitespace-nowrap"
                        :class="{ 'opacity-50': emailCountdown > 0 || !formData.email }"
                        @click="sendEmailCode"
                    >
                        {{ emailCountdown > 0 ? `${emailCountdown}秒后重发` : '获取验证码' }}
                    </view>
                </view>
            </view>

            <view class="mt-[60rpx]">
                <u-button
                    type="primary"
                    hover-class="none"
                    :customStyle="{ height: '100rpx' }"
                    @click="handleBind"
                >
                    立即绑定
                </u-button>
            </view>

            <view class="mt-[40rpx] text-center">
                <view class="text-gray-400 text-sm" @click="skipBind">
                    暂不绑定，稍后再说
                </view>
            </view>
        </view>
    </view>
</template>

<!-- 开发者：杰哥网络科技 qq2711793818 杰哥 -->
<script setup lang="ts">
import { ref, reactive, onMounted, onUnmounted } from 'vue'
import { onLoad } from '@dcloudio/uni-app'
import { userBindMobile } from '@/api/user'
import { smsSend } from '@/api/app'
import { emailSendCode } from '@/api/email'
import { bindUserEmail } from '@/api/user'
import { SMSEnum, EmailEnum } from '@/enums/appEnums'
import { useUserStore } from '@/stores/user'

const userStore = useUserStore()
const bindType = ref<'mobile' | 'email'>('email')

const formData = reactive({
    mobile: '',
    code: '',
    email: '',
    email_code: ''
})

const countdown = ref(0)
const emailCountdown = ref(0)
let timer: ReturnType<typeof setInterval> | null = null
let emailTimer: ReturnType<typeof setInterval> | null = null

const sendSmsCode = async () => {
    if (!formData.mobile) {
        uni.$u.toast('请输入手机号')
        return
    }
    if (!/^1[3-9]\d{9}$/.test(formData.mobile)) {
        uni.$u.toast('手机号格式错误')
        return
    }
    if (countdown.value > 0) return

    try {
        await smsSend({ scene: SMSEnum.BIND_MOBILE, mobile: formData.mobile })
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

const sendEmailCode = async () => {
    if (!formData.email) {
        uni.$u.toast('请输入邮箱')
        return
    }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) {
        uni.$u.toast('邮箱格式错误')
        return
    }
    if (emailCountdown.value > 0) return

    try {
        await emailSendCode({ scene: EmailEnum.USER_BIND_EMAIL, email: formData.email })
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

const handleBind = async () => {
    if (bindType.value === 'mobile') {
        if (!formData.mobile) return uni.$u.toast('请输入手机号')
        if (!formData.code) return uni.$u.toast('请输入验证码')

        uni.showLoading({ title: '绑定中...' })
        try {
            await userBindMobile({ type: 'bind', mobile: formData.mobile, code: formData.code })
            uni.hideLoading()
            uni.$u.toast('绑定成功')
            setTimeout(() => {
                uni.switchTab({ url: '/pages/index/index' })
            }, 1000)
        } catch (e: any) {
            uni.hideLoading()
            uni.$u.toast(e || '绑定失败')
        }
    } else {
        if (!formData.email) return uni.$u.toast('请输入邮箱')
        if (!formData.email_code) return uni.$u.toast('请输入验证码')

        uni.showLoading({ title: '绑定中...' })
        try {
            await bindUserEmail({ email: formData.email, code: formData.email_code })
            uni.hideLoading()
            uni.$u.toast('绑定成功')
            setTimeout(() => {
                uni.switchTab({ url: '/pages/index/index' })
            }, 1000)
        } catch (e: any) {
            uni.hideLoading()
            uni.$u.toast(e || '绑定失败')
        }
    }
}

const skipBind = () => {
    uni.showModal({
        title: '提示',
        content: '确定暂不绑定吗？绑定后可接收重要通知',
        success: (res) => {
            if (res.confirm) {
                uni.switchTab({ url: '/pages/index/index' })
            }
        }
    })
}

onLoad((options: any) => {
    if (options.type) {
        bindType.value = options.type
    }
})

onUnmounted(() => {
    if (timer) {
        clearInterval(timer)
        timer = null
    }
    if (emailTimer) {
        clearInterval(emailTimer)
        emailTimer = null
    }
})
</script>

<style lang="scss">
page {
    height: 100%;
}
</style>
