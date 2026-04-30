<template>
    <!--
    开发者公众号：杰哥网络科技
    qq2711793818 杰哥
    忘记密码页面 - 支持手机/邮箱验证码找回密码
    -->
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar :front-color="$theme.navColor" :background-color="$theme.navBgColor" />
        <!-- #endif -->
    </page-meta>
    <view
        class="register bg-white min-h-full flex flex-col items-center px-[40rpx] pt-[100rpx] box-border"
    >
        <view class="w-full">
            <view class="text-2xl font-medium mb-[60rpx]">忘记登录密码</view>

            <view v-if="!emailNotifyOpen && !smsNotifyOpen" class="text-center py-[100rpx]">
                <view class="text-gray-400 text-lg">找回密码功能暂未开放</view>
                <view class="text-gray-400 text-sm mt-[20rpx]">请联系管理员开启验证方式</view>
                <navigator
                    url="/pages/login/login"
                    hover-class="none"
                    class="text-primary mt-[40rpx]"
                >
                    返回登录
                </navigator>
            </view>

            <template v-else>
                <view class="flex mb-[40rpx]" v-if="emailNotifyOpen && smsNotifyOpen">
                    <view
                        class="flex-1 text-center py-[20rpx] rounded-[10rpx] mr-[20rpx]"
                        :class="verifyType === 'mobile' ? 'bg-primary text-white' : 'bg-gray-100 text-gray-500'"
                        @click="verifyType = 'mobile'"
                    >
                        手机验证
                    </view>
                    <view
                        class="flex-1 text-center py-[20rpx] rounded-[10rpx]"
                        :class="verifyType === 'email' ? 'bg-primary text-white' : 'bg-gray-100 text-gray-500'"
                        @click="verifyType = 'email'"
                    >
                        邮箱验证
                    </view>
                </view>

                <u-form borderBottom :label-width="150">
                    <u-form-item :label="verifyType === 'mobile' ? '手机号' : '邮箱'" borderBottom>
                        <u-input
                            class="flex-1"
                            v-model="formData.account"
                            :border="false"
                            :placeholder="verifyType === 'mobile' ? '请输入手机号码' : '请输入邮箱地址'"
                        />
                    </u-form-item>
                    <u-form-item label="验证码" borderBottom>
                        <u-input
                            class="flex-1"
                            v-model="formData.code"
                            placeholder="请输入验证码"
                            :border="false"
                        />
                        <view
                            class="border-l border-solid border-0 border-light pl-3 text-muted leading-4 ml-3 w-[180rpx]"
                            @click="sendCode"
                        >
                            <u-verification-code
                                ref="uCodeRef"
                                :seconds="60"
                                @change="codeChange"
                                change-text="x秒"
                            />
                            <text :class="formData.account ? 'text-primary' : 'text-muted'">
                                {{ codeTips }}
                            </text>
                        </view>
                    </u-form-item>
                    <u-form-item label="新密码" borderBottom>
                        <u-input
                            class="flex-1"
                            type="password"
                            v-model="formData.password"
                            placeholder="6-20位数字+字母或符号组合"
                            :border="false"
                        />
                    </u-form-item>
                    <u-form-item label="确认密码" borderBottom>
                        <u-input
                            class="flex-1"
                            type="password"
                            v-model="formData.password_confirm"
                            placeholder="再次输入新密码"
                            :border="false"
                        />
                    </u-form-item>
                </u-form>
                <view class="mt-[100rpx]">
                    <u-button type="primary" shape="circle" @click="handleConfirm"> 确定 </u-button>
                </view>
            </template>
        </view>
    </view>
</template>

<script setup lang="ts">
import { smsSend } from '@/api/app'
import { emailSendCode } from '@/api/email'
import { forgotPassword, forgotPasswordByEmail } from '@/api/user'
import { getSwitchConfig } from '@/api/system'
import { SMSEnum, EmailEnum } from '@/enums/appEnums'
import { reactive, ref, shallowRef } from 'vue'
import { safeNavigateBack } from '@/utils/util'
import { onLoad } from '@dcloudio/uni-app'

const uCodeRef = shallowRef()
const codeTips = ref('')
const verifyType = ref('mobile')
const emailNotifyOpen = ref(0)
const smsNotifyOpen = ref(1)

const formData = reactive({
    account: '',
    code: '',
    password: '',
    password_confirm: ''
})

const codeChange = (text: string) => {
    codeTips.value = text
}

const fetchSwitchConfig = async () => {
    try {
        const res = await getSwitchConfig()
        emailNotifyOpen.value = res.email_notify_open || 0
        smsNotifyOpen.value = res.sms_notify_open || 1
        if (!emailNotifyOpen.value) {
            verifyType.value = 'mobile'
        }
        if (!smsNotifyOpen.value && emailNotifyOpen.value) {
            verifyType.value = 'email'
        }
    } catch (e) {
        console.error('获取配置失败', e)
    }
}

const sendCode = async () => {
    if (!formData.account) {
        uni.$u.toast(verifyType.value === 'mobile' ? '请输入手机号码' : '请输入邮箱地址')
        return
    }

    if (verifyType.value === 'mobile') {
        if (!/^1[3-9]\d{9}$/.test(formData.account)) {
            uni.$u.toast('手机号格式错误')
            return
        }
        if (uCodeRef.value?.canGetCode) {
            await smsSend({
                scene: SMSEnum.FIND_PASSWORD,
                mobile: formData.account
            })
            uni.$u.toast('发送成功')
            uCodeRef.value?.start()
        }
    } else {
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.account)) {
            uni.$u.toast('邮箱格式错误')
            return
        }
        if (uCodeRef.value?.canGetCode) {
            await emailSendCode({
                scene: EmailEnum.FORGOT_PASSWORD,
                email: formData.account
            })
            uni.$u.toast('发送成功')
            uCodeRef.value?.start()
        }
    }
}

const handleConfirm = async () => {
    if (!formData.account) {
        uni.$u.toast(verifyType.value === 'mobile' ? '请输入手机号码' : '请输入邮箱地址')
        return
    }
    if (!formData.code) return uni.$u.toast('请输入验证码')
    if (!formData.password) return uni.$u.toast('请输入密码')
    if (!formData.password_confirm) return uni.$u.toast('请输入确认密码')
    if (formData.password != formData.password_confirm) return uni.$u.toast('两次输入的密码不一致')

    try {
        if (verifyType.value === 'mobile') {
            await forgotPassword({
                mobile: formData.account,
                code: formData.code,
                password: formData.password,
                password_confirm: formData.password_confirm
            })
        } else {
            await forgotPasswordByEmail({
                email: formData.account,
                code: formData.code,
                password: formData.password,
                password_confirm: formData.password_confirm
            })
        }
        uni.$u.toast('密码重置成功')
        setTimeout(() => {
            safeNavigateBack({ defaultUrl: '/pages/login/login' })
        }, 1500)
    } catch (e: any) {
        uni.$u.toast(e || '操作失败')
    }
}

onLoad(() => {
    fetchSwitchConfig()
})
</script>

<style lang="scss">
page {
    height: 100%;
}
</style>
