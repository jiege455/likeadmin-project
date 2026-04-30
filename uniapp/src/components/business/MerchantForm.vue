<template>
    <div class="merchant-form">
        <view v-if="configLoading" class="flex justify-center py-[100rpx]">
            <u-loading-icon mode="circle"></u-loading-icon>
            <text class="ml-2 text-gray-400">加载配置中...</text>
        </view>

        <van-form @submit="onSubmit" v-else>
            <view v-if="!switchConfig.merchant_apply_open" class="text-center py-[100rpx]">
                <view class="text-gray-400 text-lg">入驻功能已关闭</view>
            </view>

            <template v-else>
                <van-field
                    v-model="formData.name"
                    name="name"
                    label="店铺名称"
                    placeholder="请输入店铺名称"
                    :rules="[{ required: true, message: '请填写店铺名称' }]"
                />

                <view
                    v-if="needMobile"
                    class="px-[18rpx] border border-solid border-lightc border-light rounded-[10rpx] h-[100rpx] items-center flex mt-[40rpx] mx-[30rpx]"
                >
                    <u-input
                        class="flex-1"
                        v-model="formData.mobile"
                        placeholder="请输入联系电话"
                        type="number"
                        :border="false"
                    />
                </view>

                <view
                    v-if="needMobile"
                    class="px-[18rpx] border border-solid border-lightc border-light rounded-[10rpx] h-[100rpx] items-center flex mt-[40rpx] mx-[30rpx]"
                >
                    <u-input
                        class="flex-1"
                        v-model="formData.code"
                        placeholder="请输入手机验证码"
                        type="number"
                        :border="false"
                    />
                    <view
                        class="text-primary text-sm whitespace-nowrap"
                        :class="{ 'opacity-50': mobileCountdown > 0 || !formData.mobile }"
                        @click="sendSmsCode"
                    >
                        {{ mobileCountdown > 0 ? `${mobileCountdown}秒后重发` : '获取验证码' }}
                    </view>
                </view>

                <view
                    v-if="needEmail"
                    class="px-[18rpx] border border-solid border-lightc border-light rounded-[10rpx] h-[100rpx] items-center flex mt-[40rpx] mx-[30rpx]"
                >
                    <u-input
                        class="flex-1"
                        v-model="formData.email"
                        placeholder="请输入邮箱"
                        :border="false"
                    />
                </view>

                <view
                    v-if="needEmail"
                    class="px-[18rpx] border border-solid border-lightc border-light rounded-[10rpx] h-[100rpx] items-center flex mt-[40rpx] mx-[30rpx]"
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

                <view
                    v-if="!needMobile"
                    class="px-[18rpx] border border-solid border-lightc border-light rounded-[10rpx] h-[100rpx] items-center flex mt-[40rpx] mx-[30rpx]"
                >
                    <u-input
                        class="flex-1"
                        v-model="formData.mobile"
                        placeholder="请输入联系电话"
                        type="number"
                        :border="false"
                    />
                </view>

                <view
                    class="px-[18rpx] border border-solid border-lightc border-light rounded-[10rpx] h-[100rpx] items-center flex mt-[40rpx] mx-[30rpx]"
                >
                    <u-input
                        class="flex-1"
                        v-model="formData.wechat"
                        placeholder="请输入微信号"
                        :border="false"
                    />
                </view>

                <view
                    class="px-[18rpx] border border-solid border-lightc border-light rounded-[10rpx] min-h-[100rpx] items-start flex mt-[40rpx] mx-[30rpx] py-[20rpx]"
                >
                    <u-input
                        class="flex-1"
                        v-model="formData.desc"
                        placeholder="请输入店铺简介"
                        type="textarea"
                        :border="false"
                        :autoHeight="true"
                    />
                </view>

                <div style="margin: 16px">
                    <van-button round block type="primary" native-type="submit">
                        {{ mode === 'edit' ? '保存修改' : '立即入驻' }}
                    </van-button>
                </div>
            </template>
        </van-form>
    </div>
</template>

<!-- 开发者：杰哥网络科技 qq2711793818 杰哥 -->
<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { smsSend } from '@/api/app'
import { emailSendCode } from '@/api/email'
import { getSwitchConfig } from '@/api/system'
import { SMSEnum, EmailEnum } from '@/enums/appEnums'

interface MerchantData {
    name: string
    mobile: string
    code: string
    email: string
    email_code: string
    wechat: string
    desc: string
}

const props = defineProps<{
    mode?: 'create' | 'edit'
    initialData?: Partial<MerchantData>
}>()

const emit = defineEmits<{
    (e: 'submit', data: MerchantData): void
}>()

const switchConfig = ref({
    merchant_apply_open: 1,
    merchant_apply_verify_type: 'mobile',
    email_notify_open: 0,
    sms_notify_open: 1
})

const verifyType = computed(() => switchConfig.value.merchant_apply_verify_type)
const needMobile = computed(() => switchConfig.value.sms_notify_open && verifyType.value === 'mobile')
const needEmail = computed(() => switchConfig.value.email_notify_open && verifyType.value === 'email')

const formData = ref<MerchantData>({
    name: '',
    mobile: '',
    code: '',
    email: '',
    email_code: '',
    wechat: '',
    desc: ''
})

const configLoading = ref(true)
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
    if (!formData.value.mobile) {
        uni.$u.toast('请先输入手机号')
        return
    }
    if (!/^1[3-9]\d{9}$/.test(formData.value.mobile)) {
        uni.$u.toast('手机号格式错误')
        return
    }
    if (mobileCountdown.value > 0) return

    try {
        await smsSend({
            scene: SMSEnum.MERCHANT_APPLY,
            mobile: formData.value.mobile
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
    if (!formData.value.email) {
        uni.$u.toast('请输入邮箱')
        return
    }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.value.email)) {
        uni.$u.toast('邮箱格式错误')
        return
    }
    if (emailCountdown.value > 0) return

    try {
        await emailSendCode({
            scene: EmailEnum.MERCHANT_APPLY,
            email: formData.value.email
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

const onSubmit = () => {
    if (!formData.value.name) {
        uni.$u.toast('请填写店铺名称')
        return
    }

    if (needMobile.value) {
        if (!formData.value.mobile) {
            uni.$u.toast('请输入手机号')
            return
        }
        if (!formData.value.code) {
            uni.$u.toast('请输入手机验证码')
            return
        }
    }

    if (needEmail.value) {
        if (!formData.value.email) {
            uni.$u.toast('请输入邮箱')
            return
        }
        if (!formData.value.email_code) {
            uni.$u.toast('请输入邮箱验证码')
            return
        }
    }

    // 非手机验证模式下，联系电话必填
    if (!needMobile.value) {
        if (!formData.value.mobile) {
            uni.$u.toast('请输入联系电话')
            return
        }
        if (!/^1[3-9]\d{9}$/.test(formData.value.mobile)) {
            uni.$u.toast('手机号格式错误')
            return
        }
    }

    // 微信号必填
    if (!formData.value.wechat) {
        uni.$u.toast('请输入微信号')
        return
    }

    // 店铺简介必填
    if (!formData.value.desc) {
        uni.$u.toast('请输入店铺简介')
        return
    }

    emit('submit', formData.value)
}

onMounted(() => {
    fetchSwitchConfig()
})
</script>

<style scoped>
.merchant-form {
    background-color: #fff;
    padding: 10px 0;
}
</style>
