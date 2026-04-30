<template>
    <view class="merchant-apply" :style="themeStyle">
        <uni-nav title="商家入驻申请"></uni-nav>

        <view class="bg-white p-4 rounded-lg m-3" v-if="!applyInfo || isReApply">
            <!-- 加载配置中 -->
            <view v-if="configLoading" class="flex justify-center py-10">
                <u-loading-icon mode="circle"></u-loading-icon>
                <text class="ml-2 text-gray-400">加载配置中...</text>
            </view>

            <u-form :model="formData" ref="form" label-width="160" v-else>
                <u-form-item label="商户名称" prop="name" required>
                    <u-input v-model="formData.name" placeholder="请输入商户名称" />
                </u-form-item>

                <!-- 手机号输入（需要验证时必填） -->
                <u-form-item v-if="needMobile" label="联系电话" prop="mobile" required>
                    <u-input v-model="formData.mobile" placeholder="请输入联系电话" type="number" />
                </u-form-item>

                <!-- 手机验证码 -->
                <u-form-item v-if="needMobile" label="验证码" prop="code" required>
                    <view class="flex items-center w-full">
                        <u-input
                            v-model="formData.code"
                            placeholder="请输入手机验证码"
                            type="number"
                            class="flex-1"
                        />
                        <view
                            class="text-primary text-sm whitespace-nowrap ml-2"
                            :class="{ 'opacity-50': mobileCountdown > 0 || !formData.mobile }"
                            @click="sendSmsCode"
                        >
                            {{ mobileCountdown > 0 ? `${mobileCountdown}秒后重发` : '获取验证码' }}
                        </view>
                    </view>
                </u-form-item>

                <!-- 邮箱输入（需要验证时必填） -->
                <u-form-item v-if="needEmail" label="邮箱" prop="email" required>
                    <u-input v-model="formData.email" placeholder="请输入邮箱" />
                </u-form-item>

                <!-- 邮箱验证码 -->
                <u-form-item v-if="needEmail" label="邮箱验证码" prop="email_code" required>
                    <view class="flex items-center w-full">
                        <u-input
                            v-model="formData.email_code"
                            placeholder="请输入邮箱验证码"
                            class="flex-1"
                        />
                        <view
                            class="text-primary text-sm whitespace-nowrap ml-2"
                            :class="{ 'opacity-50': emailCountdown > 0 || !formData.email }"
                            @click="sendEmailCode"
                        >
                            {{ emailCountdown > 0 ? `${emailCountdown}秒后重发` : '获取验证码' }}
                        </view>
                    </view>
                </u-form-item>

                <!-- 手机号输入（不需要验证时必填） -->
                <u-form-item v-if="!needMobile" label="联系电话" prop="mobile" required>
                    <u-input
                        v-model="formData.mobile"
                        placeholder="请输入联系电话"
                        type="number"
                    />
                </u-form-item>

                <u-form-item label="微信号" prop="wechat" required>
                    <u-input v-model="formData.wechat" placeholder="请输入微信号" />
                </u-form-item>
                <u-form-item label="简介" prop="desc" required>
                    <u-input v-model="formData.desc" type="textarea" placeholder="请输入商户简介" />
                </u-form-item>
            </u-form>

            <view class="mt-8 p-3">
                <u-button
                    type="primary"
                    shape="circle"
                    @click="submit"
                    :loading="submitLoading"
                    :disabled="submitLoading"
                    :custom-style="{
                        backgroundColor: themeLoaded ? themeColor : '#409eff',
                        color: '#ffffff',
                        border: 'none',
                        height: '88rpx',
                        fontSize: '32rpx'
                    }"
                    >{{ submitLoading ? '提交中...' : '提交申请' }}</u-button
                >
            </view>
        </view>

        <!-- 审核状态展示 -->
        <view class="status-container flex flex-col items-center justify-center mt-20" v-else>
            <block v-if="applyInfo.status === 0">
                <u-icon name="clock" size="100" color="#ff9900"></u-icon>
                <view class="text-lg font-bold mt-4">审核中</view>
                <view class="text-gray-500 mt-2 text-center px-4"
                    >您的申请已提交，请耐心等待管理员审核。</view
                >
            </block>
            <block v-if="applyInfo.status === 1">
                <u-icon name="checkmark-circle" size="100" color="#19be6b"></u-icon>
                <view class="text-lg font-bold mt-4">审核通过</view>
                <view class="text-gray-500 mt-2 text-center px-4">恭喜！您的入驻申请已通过。</view>
                <view class="mt-6 w-full px-10">
                    <u-button
                        type="primary"
                        shape="circle"
                        @click="goCenter"
                        :custom-style="{
                            backgroundColor: themeLoaded ? themeColor : '#409eff',
                            color: '#ffffff',
                            border: 'none'
                        }"
                        >进入商户中心</u-button
                    >
                </view>
            </block>
            <!-- 增加审核拒绝的状态展示 -->
            <block v-if="applyInfo.status === 2">
                <u-icon name="close-circle" size="100" color="#fa3534"></u-icon>
                <view class="text-lg font-bold mt-4">审核未通过</view>
                <view class="text-gray-500 mt-2 text-center px-4"
                    >原因：{{ applyInfo.audit_remark || '未填写原因' }}</view
                >
                <view class="mt-6 w-full px-10">
                    <u-button
                        type="primary"
                        shape="circle"
                        @click="reApply"
                        :custom-style="{
                            backgroundColor: themeLoaded ? themeColor : '#409eff',
                            color: '#ffffff',
                            border: 'none'
                        }"
                        >重新申请</u-button
                    >
                </view>
            </block>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue'
import { merchantApply, getApplyDetail } from '@/api/merchant'
import { onShow } from '@dcloudio/uni-app'
import { useThemeStore } from '@/stores/theme'
import { getSwitchConfig } from '@/api/system'
import { smsSend } from '@/api/app'
import { emailSendCode } from '@/api/email'
import { SMSEnum, EmailEnum } from '@/enums/appEnums'
import { safeNavigateBack } from '@/utils/util'

const themeStore = useThemeStore()
const themeColor = computed(() => themeStore.primaryColor || '#409eff')
const themeStyle = computed(() => themeStore.vars)
const navBgColor = computed(() => themeStore.navBgColor || '#409eff')
const navColor = computed(() => themeStore.navColor || '#ffffff')
const themeLoaded = ref(false)

const switchConfig = ref({
    merchant_apply_open: 1,
    merchant_apply_verify_type: 'email',
    email_notify_open: 0,
    sms_notify_open: 1
})

const verifyType = computed(() => switchConfig.value.merchant_apply_verify_type)
const needMobile = computed(() => switchConfig.value.sms_notify_open && verifyType.value === 'mobile')
const needEmail = computed(() => switchConfig.value.email_notify_open && verifyType.value === 'email')

const formData = reactive({
    name: '',
    mobile: '',
    code: '',
    email: '',
    email_code: '',
    wechat: '',
    desc: ''
})

const applyInfo = ref<any>(null)
const isReApply = ref(false)
const configLoading = ref(true)
const submitLoading = ref(false)
const mobileCountdown = ref(0)
const emailCountdown = ref(0)
let mobileTimer: ReturnType<typeof setInterval> | null = null
let emailTimer: ReturnType<typeof setInterval> | null = null

const fetchSwitchConfig = async () => {
    configLoading.value = true
    try {
        const res = await getSwitchConfig()
        switchConfig.value = {
            merchant_apply_open: res.merchant_apply_open || 1,
            merchant_apply_verify_type: res.merchant_apply_verify_type || 'email',
            email_notify_open: res.email_notify_open || 0,
            sms_notify_open: res.sms_notify_open || 1
        }
        console.log('商家入驻配置:', {
            merchant_apply_verify_type: res.merchant_apply_verify_type,
            email_notify_open: res.email_notify_open,
            sms_notify_open: res.sms_notify_open,
            needEmail: res.email_notify_open && res.merchant_apply_verify_type === 'email',
            needMobile: res.sms_notify_open && res.merchant_apply_verify_type === 'mobile'
        })
    } catch (e) {
        console.error('获取配置失败', e)
    } finally {
        configLoading.value = false
    }
}

const sendSmsCode = async () => {
    if (!formData.mobile) {
        uni.$u.toast('请先输入手机号')
        return
    }
    if (!/^1[3-9]\d{9}$/.test(formData.mobile)) {
        uni.$u.toast('手机号格式错误')
        return
    }
    if (mobileCountdown.value > 0) return

    try {
        await smsSend({
            scene: SMSEnum.MERCHANT_APPLY,
            mobile: formData.mobile
        })
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
        await emailSendCode({
            scene: EmailEnum.MERCHANT_APPLY,
            email: formData.email
        })
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

const toast = (title: string) => {
    uni.showToast({ title, icon: 'none' })
}

const getDetail = async () => {
    try {
        const res = await getApplyDetail()
        // 兼容后端返回空数组的情况
        if (Array.isArray(res) && res.length === 0) {
            applyInfo.value = null
        } else {
            applyInfo.value = res
        }

        // 如果被拒绝，回显数据方便修改
        // 注意：这里只回显数据，但不自动切换到表单视图，而是显示拒绝状态页
        if (applyInfo.value && applyInfo.value.status === 2) {
            formData.name = applyInfo.value.name
            formData.mobile = applyInfo.value.mobile
            formData.wechat = applyInfo.value.wechat
            formData.desc = applyInfo.value.desc
        }
    } catch (e) {
        // 无申请记录
        applyInfo.value = null
    }
}

const submit = async () => {
    if (!formData.name) return toast('请输入商户名称')

    // 手机验证
    if (needMobile.value) {
        if (!formData.mobile) return toast('请输入手机号')
        if (!/^1[3-9]\d{9}$/.test(formData.mobile)) return toast('手机号格式错误')
        if (!formData.code) return toast('请输入手机验证码')
    }

    // 邮箱验证
    if (needEmail.value) {
        if (!formData.email) return toast('请输入邮箱')
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) return toast('邮箱格式错误')
        if (!formData.email_code) return toast('请输入邮箱验证码')
    }

    // 非手机验证模式下，联系电话必填
    if (!needMobile.value) {
        if (!formData.mobile) return toast('请输入联系电话')
        if (!/^1[3-9]\d{9}$/.test(formData.mobile)) return toast('手机号格式错误')
    }

    // 微信号必填
    if (!formData.wechat) return toast('请输入微信号')

    // 简介必填
    if (!formData.desc) return toast('请输入商户简介')

    try {
        submitLoading.value = true
        const res = await merchantApply(formData)
        toast(res.msg || '提交成功')
        isReApply.value = false
        getDetail()
    } catch (e: any) {
        toast(e.msg || e.message || '提交失败')
    } finally {
        submitLoading.value = false
    }
}

const goCenter = () => {
    uni.navigateTo({ url: '/packages/pages/merchant/index' })
}

const reApply = () => {
    isReApply.value = true
}

const handleBack = () => {
    safeNavigateBack({ defaultUrl: '/pages/index/index' })
}

onShow(() => {
    themeStore.getTheme().then(() => {
        themeLoaded.value = true
    })
    fetchSwitchConfig()
    getDetail()
})
</script>

<style lang="scss" scoped>
.merchant-apply {
    min-height: 100vh;
    background-color: #f5f5f5;
    overflow: hidden;
}
</style>
