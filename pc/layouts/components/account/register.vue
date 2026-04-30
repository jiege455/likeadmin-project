<!--
    开发者公众号：杰哥网络科技
    QQ: 2711793818 杰哥
-->
<template>
    <div class="login">
        <div class="flex justify-between">
            <span class="text-4xl">注册账号</span>
            <ElButton
                type="primary"
                link
                @click="setPopupType(PopupTypeEnum.LOGIN)"
            >
                返回登录
            </ElButton>
        </div>
        <ElForm
            ref="formRef"
            class="mt-[35px]"
            size="large"
            :model="formData"
            :rules="formRules"
        >
            <ElFormItem prop="account">
                <ElInput
                    v-model="formData.account"
                    placeholder="请输入创建的账号"
                />
            </ElFormItem>
            <ElFormItem prop="password">
                <ElInput
                    v-model="formData.password"
                    type="password"
                    show-password
                    placeholder="请输入 6-20 位数字 + 字母或符号组合"
                />
            </ElFormItem>
            <ElFormItem prop="password_confirm">
                <ElInput
                    v-model="formData.password_confirm"
                    type="password"
                    show-password
                    placeholder="请再次输入密码"
                />
            </ElFormItem>

            <!-- 手机验证码 -->
            <ElFormItem 
                v-if="needMobile" 
                prop="mobile"
            >
                <div class="flex">
                    <ElInput
                        v-model="formData.mobile"
                        placeholder="请输入手机号"
                        class="flex-1"
                    />
                    <ElButton
                        type="primary"
                        :disabled="mobileCountdown > 0 || !formData.mobile"
                        @click="sendSmsCode"
                        class="ml-2"
                    >
                        {{ mobileCountdown > 0 ? `${mobileCountdown}秒后重发` : '获取验证码' }}
                    </ElButton>
                </div>
            </ElFormItem>
            <ElFormItem 
                v-if="needMobile" 
                prop="mobile_code"
            >
                <ElInput
                    v-model="formData.mobile_code"
                    placeholder="请输入手机验证码"
                />
            </ElFormItem>

            <!-- 邮箱验证码 -->
            <ElFormItem 
                v-if="needEmail" 
                prop="email"
            >
                <ElInput
                    v-model="formData.email"
                    placeholder="请输入邮箱"
                />
            </ElFormItem>
            <ElFormItem 
                v-if="needEmail" 
                prop="email_code"
            >
                <div class="flex">
                    <ElInput
                        v-model="formData.email_code"
                        placeholder="请输入邮箱验证码"
                        class="flex-1"
                    />
                    <ElButton
                        type="primary"
                        :disabled="emailCountdown > 0 || !formData.email"
                        @click="sendEmailCode"
                        class="ml-2"
                    >
                        {{ emailCountdown > 0 ? `${emailCountdown}秒后重发` : '获取验证码' }}
                    </ElButton>
                </div>
            </ElFormItem>

            <!-- 协议 -->
            <ElFormItem v-if="isOpenAgreement" class="mt-[20px]">
                <el-checkbox v-model="isCheckAgreement">
                    已阅读并同意
                    <el-link type="primary" :underline="false" @click.stop="openAgreement('service')">
                        《服务协议》
                    </el-link>
                    和
                    <el-link type="primary" :underline="false" @click.stop="openAgreement('privacy')">
                        《隐私协议》
                    </el-link>
                </el-checkbox>
            </ElFormItem>

            <ElFormItem class="mt-[60px]">
                <ElButton
                    class="w-full"
                    type="primary"
                    :loading="isLock"
                    @click="handleConfirmLock"
                >
                    注册
                </ElButton>
            </ElFormItem>
        </ElForm>
    </div>
</template>
<script lang="ts" setup>
import {
    ElForm,
    ElFormItem,
    ElInput,
    ElButton,
    ElCheckbox,
    ElLink,
    ElMessage,
    FormInstance,
    FormRules
} from 'element-plus'
import { register } from '~~/api/account'
import { smsSend } from '~~/api/app'
import { emailSendCode } from '~~/api/email'
import { useAccount, PopupTypeEnum } from './useAccount'
import { useLockFn } from '~~/composables/useLockFn'
import { useAppStore } from '~~/stores/app'
import { SMSEnum } from '~~/enums/appEnums'
import { EmailEnum } from '~~/enums/appEnums'
import { computed, reactive, ref, shallowRef, onMounted } from 'vue'

const { setPopupType } = useAccount()
const appStore = useAppStore()
const formRef = shallowRef<FormInstance>()

// 注册配置
const registerConfig = ref({
    register_open: 1,
    register_verify_type: 'none',
    email_notify_open: 0,
    sms_notify_open: 1,
    login_agreement: 0,
    coerce_mobile: 0
})

// 获取注册配置
const fetchRegisterConfig = async () => {
    // 确保配置已加载
    if (!appStore.config.website?.shop_name) {
        await appStore.getConfig()
    }
    // 从 appStore 获取 login 配置
    const loginConfig = appStore.config.login || {}
    registerConfig.value = {
        register_verify_type: loginConfig.register_verify_type || 'none',
        register_open: loginConfig.register_open ?? 1,
        email_notify_open: loginConfig.email_notify_open ?? 0,
        sms_notify_open: loginConfig.sms_notify_open ?? 1,
        login_agreement: loginConfig.login_agreement ?? 0,
        coerce_mobile: loginConfig.coerce_mobile ?? 0
    }
}

// 计算是否需要验证
const verifyType = computed(() => registerConfig.value.register_verify_type)
const needMobile = computed(
    () => registerConfig.value.sms_notify_open && ['mobile', 'both'].includes(verifyType.value)
)
const needEmail = computed(
    () => registerConfig.value.email_notify_open && ['email', 'both'].includes(verifyType.value)
)
const isOpenAgreement = computed(() => registerConfig.value.login_agreement == 1)

// 表单数据
const formData = reactive({
    account: '',
    password: '',
    password_confirm: '',
    mobile: '',
    mobile_code: '',
    email: '',
    email_code: ''
})

// 表单验证规则
const formRules: FormRules = {
    account: [
        {
            required: true,
            message: '请输入创建的账号',
            trigger: ['change', 'blur']
        },
        {
            min: 3,
            max: 12,
            message: '账号长度应为 3-12',
            trigger: ['change', 'blur']
        }
    ],
    password: [
        {
            required: true,
            message: '请输入 6-20 位数字 + 字母或符号组合',
            trigger: ['change', 'blur']
        },
        {
            min: 6,
            max: 20,
            message: '密码长度应为 6-20',
            trigger: ['change', 'blur']
        }
    ],
    password_confirm: [
        {
            validator(rule: any, value: any, callback: any) {
                if (value === '') {
                    callback(new Error('请再次输入密码'))
                } else if (value !== formData.password) {
                    callback(new Error('两次输入的密码不一致'))
                } else {
                    callback()
                }
            },
            trigger: ['change', 'blur']
        }
    ],
    mobile: [
        {
            required: true,
            message: '请输入手机号',
            trigger: ['change', 'blur'],
            validator: (rule, value, callback) => {
                if (needMobile.value) {
                    if (!value) {
                        callback(new Error('请输入手机号'))
                    } else if (!/^1[3-9]\d{9}$/.test(value)) {
                        callback(new Error('手机号格式错误'))
                    } else {
                        callback()
                    }
                } else {
                    callback()
                }
            }
        }
    ],
    mobile_code: [
        {
            required: true,
            message: '请输入手机验证码',
            trigger: ['change', 'blur'],
            validator: (rule, value, callback) => {
                if (needMobile.value && !value) {
                    callback(new Error('请输入手机验证码'))
                } else {
                    callback()
                }
            }
        }
    ],
    email: [
        {
            required: true,
            message: '请输入邮箱',
            trigger: ['change', 'blur'],
            validator: (rule, value, callback) => {
                if (needEmail.value) {
                    if (!value) {
                        callback(new Error('请输入邮箱'))
                    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                        callback(new Error('邮箱格式错误'))
                    } else {
                        callback()
                    }
                } else {
                    callback()
                }
            }
        }
    ],
    email_code: [
        {
            required: true,
            message: '请输入邮箱验证码',
            trigger: ['change', 'blur'],
            validator: (rule, value, callback) => {
                if (needEmail.value && !value) {
                    callback(new Error('请输入邮箱验证码'))
                } else {
                    callback()
                }
            }
        }
    ]
}

const isCheckAgreement = ref(false)
const mobileCountdown = ref(0)
const emailCountdown = ref(0)
let mobileTimer: ReturnType<typeof setInterval> | null = null
let emailTimer: ReturnType<typeof setInterval> | null = null

// 发送手机验证码
const sendSmsCode = async () => {
    if (!formData.mobile) {
        ElMessage.warning('请输入手机号')
        return
    }
    if (!/^1[3-9]\d{9}$/.test(formData.mobile)) {
        ElMessage.warning('手机号格式错误')
        return
    }
    if (mobileCountdown.value > 0) return

    try {
        await smsSend({ scene: SMSEnum.BIND_MOBILE, mobile: formData.mobile })
        ElMessage.success('验证码已发送')
        mobileCountdown.value = 60
        mobileTimer = setInterval(() => {
            mobileCountdown.value--
            if (mobileCountdown.value <= 0 && mobileTimer) {
                clearInterval(mobileTimer)
                mobileTimer = null
            }
        }, 1000)
    } catch (error: any) {
        ElMessage.error(error?.message || '发送失败')
    }
}

// 发送邮箱验证码
const sendEmailCode = async () => {
    if (!formData.email) {
        ElMessage.warning('请输入邮箱')
        return
    }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) {
        ElMessage.warning('邮箱格式错误')
        return
    }
    if (emailCountdown.value > 0) return

    try {
        await emailSendCode({ scene: EmailEnum.USER_REGISTER, email: formData.email })
        ElMessage.success('验证码已发送')
        emailCountdown.value = 60
        emailTimer = setInterval(() => {
            emailCountdown.value--
            if (emailCountdown.value <= 0 && emailTimer) {
                clearInterval(emailTimer)
                emailTimer = null
            }
        }, 1000)
    } catch (error: any) {
        ElMessage.error(error?.message || '发送失败')
    }
}

// 打开协议
const openAgreement = (type: string) => {
    // 这里可以打开协议页面，或者显示协议内容弹窗
    window.open(`/agreement?type=${type}`, '_blank')
}

const handleConfirm = async () => {
    await formRef.value?.validate()
    
    if (!isCheckAgreement.value && isOpenAgreement.value) {
        ElMessage.warning('请先阅读并同意协议')
        return
    }
    
    const submitData: any = {
        account: formData.account,
        password: formData.password,
        password_confirm: formData.password_confirm
    }
    
    if (needMobile.value) {
        submitData.mobile = formData.mobile
        submitData.mobile_code = formData.mobile_code
    }
    
    if (needEmail.value) {
        submitData.email = formData.email
        submitData.email_code = formData.email_code
    }
    
    await register(submitData)
    ElMessage.success('注册成功')
    setPopupType(PopupTypeEnum.LOGIN)
}

const { lockFn: handleConfirmLock, isLock } = useLockFn(handleConfirm)

// 初始化时获取配置
onMounted(() => {
    fetchRegisterConfig()
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
</style>
