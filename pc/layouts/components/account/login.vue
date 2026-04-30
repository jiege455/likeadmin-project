<!--
    开发者公众号：杰哥网络科技
    QQ: 2711793818 杰哥
-->
<template>
    <div class="login">
        <div class="text-4xl">欢迎登录</div>
        <ElForm
            ref="formRef"
            class="mt-[35px]"
            size="large"
            :model="formData"
            :rules="formRules"
        >
            <template
                v-if="isAccountLogin && includeLoginWay(LoginWayEnum.ACCOUNT)"
            >
                <ElFormItem prop="account">
                    <ElInput
                        v-model="formData.account"
                        placeholder="请输入账号/手机号"
                    />
                </ElFormItem>
                <ElFormItem prop="password">
                    <ElInput
                        v-model="formData.password"
                        type="password"
                        show-password
                        placeholder="请输入密码"
                    />
                </ElFormItem>
            </template>
            <template
                v-if="isMobileLogin && includeLoginWay(LoginWayEnum.MOBILE)"
            >
                <ElFormItem prop="account">
                    <ElInput
                        v-model="formData.account"
                        placeholder="请输入手机号"
                    />
                </ElFormItem>
                <ElFormItem prop="code">
                    <ElInput v-model="formData.code" placeholder="请输入验证码">
                        <template #suffix>
                            <div
                                class="flex justify-center leading-5 w-[90px] pl-2.5 border-l border-br"
                            >
                                <VerificationCode
                                    ref="verificationCodeRef"
                                    @click-get="sendSms"
                                />
                            </div>
                        </template>
                    </ElInput>
                </ElFormItem>
            </template>
            <div class="flex">
                <div class="flex-1">
                    <ElButton
                        v-if="
                            isAccountLogin &&
                            includeLoginWay(LoginWayEnum.MOBILE)
                        "
                        type="primary"
                        link
                        @click="changeLoginWay"
                    >
                        手机验证码登录
                    </ElButton>
                    <ElButton
                        v-if="
                            isMobileLogin &&
                            includeLoginWay(LoginWayEnum.ACCOUNT)
                        "
                        type="primary"
                        link
                        @click="changeLoginWay"
                    >
                        账号密码登录
                    </ElButton>
                </div>

                <ElButton
                    v-if="isAccountLogin"
                    link
                    @click="setPopupType(PopupTypeEnum.FORGOT_PWD)"
                >
                    忘记密码？
                </ElButton>
            </div>
            <ElFormItem class="mt-[30px]">
                <ElButton
                    class="w-full"
                    type="primary"
                    :loading="isLock"
                    @click="loginLock"
                >
                    登录
                </ElButton>
            </ElFormItem>
            <div class="mt-[40px]" v-if="isOpenOtherAuth">
                <ElDivider>
                    <span class="text-tx-secondary font-normal">
                        第三方登录
                    </span>
                </ElDivider>
                <div class="flex justify-center">
                    <ElButton link @click="getWxCodeLock" v-if="inWxAuth">
                        <img
                            class="w-[48px] h-[48px]"
                            src="@/assets/images/icon/icon_wx.png"
                        />
                    </ElButton>
                    <ElButton link @click="showSocialLogin = true" v-if="isOpenSocialLogin">
                        <img
                            class="w-[48px] h-[48px]"
                            src="@/assets/images/icon/icon_more_login.png"
                        />
                    </ElButton>
                </div>
            </div>

            <!-- 聚合登录弹窗 -->
            <el-dialog v-model="showSocialLogin" title="选择登录方式" width="400px" append-to-body>
                <div class="flex flex-wrap justify-center gap-[30px] py-[20px]">
                    <div v-if="socialLoginTypes.qq" class="flex flex-col items-center cursor-pointer" @click="handleSocialLogin('qq')">
                        <img src="@/assets/images/social/qq.png" class="w-[48px] h-[48px]" />
                        <span class="text-sm mt-[8px]">QQ</span>
                    </div>
                    <div v-if="socialLoginTypes.wx" class="flex flex-col items-center cursor-pointer" @click="handleSocialLogin('wx')">
                        <img src="@/assets/images/social/wx.png" class="w-[48px] h-[48px]" />
                        <span class="text-sm mt-[8px]">微信</span>
                    </div>
                    <div v-if="socialLoginTypes.alipay" class="flex flex-col items-center cursor-pointer" @click="handleSocialLogin('alipay')">
                        <img src="@/assets/images/social/alipay.png" class="w-[48px] h-[48px]" />
                        <span class="text-sm mt-[8px]">支付宝</span>
                    </div>
                    <div v-if="socialLoginTypes.baidu" class="flex flex-col items-center cursor-pointer" @click="handleSocialLogin('baidu')">
                        <img src="@/assets/images/social/baidu.png" class="w-[48px] h-[48px]" />
                        <span class="text-sm mt-[8px]">百度</span>
                    </div>
                    <div v-if="socialLoginTypes.microsoft" class="flex flex-col items-center cursor-pointer" @click="handleSocialLogin('microsoft')">
                        <img src="@/assets/images/social/microsoft.png" class="w-[48px] h-[48px]" />
                        <span class="text-sm mt-[8px]">微软</span>
                    </div>
                </div>
            </el-dialog>
            <div
                class="mb-[-15px] mx-[-40px] mt-[30px] bg-primary-light-9 rounded-b-md px-[15px] flex leading-10"
            >
                <div class="flex-1">
                    <ElCheckbox v-if="isOpenAgreement" v-model="isAgreement">
                        <span class="text-tx-secondary text-sm">
                            已阅读并同意
                            <NuxtLink
                                :to="`/policy/${PolicyAgreementEnum.SERVICE}`"
                                custom
                                v-slot="{ href }"
                            >
                                <a
                                    class="text-tx-primary"
                                    :href="href"
                                    target="_blank"
                                >
                                    《服务协议》
                                </a>
                            </NuxtLink>
                            和
                            <NuxtLink
                                class="text-tx-primary"
                                :to="`/policy/${PolicyAgreementEnum.PRIVACY}`"
                                custom
                                v-slot="{ href }"
                            >
                                <a
                                    class="text-tx-primary"
                                    :href="href"
                                    target="_blank"
                                >
                                    《隐私政策》
                                </a>
                            </NuxtLink>
                        </span>
                    </ElCheckbox>
                </div>
                <div>
                    <ElButton
                        link
                        type="primary"
                        @click="setPopupType(PopupTypeEnum.REGISTER)"
                    >
                        <span class="text-sm">注册账号</span>
                    </ElButton>
                </div>
            </div>
        </ElForm>
    </div>
</template>
<script lang="ts" setup>
import {
    ElForm,
    ElFormItem,
    ElInput,
    ElButton,
    ElDivider,
    ElCheckbox,
    FormInstance,
    FormRules
} from 'element-plus'
import { useAccount, PopupTypeEnum } from './useAccount'
import { getWxCodeUrl, login } from '@/api/account'
import { useAppStore } from '@/stores/app'
import { useUserStore } from '@/stores/user'
import { smsSend } from '~~/api/app'
import { PolicyAgreementEnum, SMSEnum } from '~~/enums/appEnums'
import feedback from '~~/utils/feedback'
const appStore = useAppStore()
const userStore = useUserStore()
const { setPopupType, toggleShowPopup } = useAccount()
enum LoginWayEnum {
    ACCOUNT = 1,
    MOBILE = 2
}
const isAgreement = ref(false)
const formRef = shallowRef<FormInstance>()
const formRules: FormRules = {
    account: [
        {
            required: true,
            validator(rule: any, value: any, callback: any) {
                if (value === '') {
                    callback(
                        new Error(
                            formData.scene == LoginWayEnum.ACCOUNT
                                ? '请输入账号/手机号'
                                : '请输入手机号'
                        )
                    )
                    return
                }
                callback()
            },
            trigger: ['change', 'blur']
        }
    ],
    password: [
        {
            required: true,
            message: '请输入密码',
            trigger: ['change', 'blur']
        }
    ],
    code: [
        {
            required: true,
            message: '请输入验证码',
            trigger: ['change', 'blur']
        }
    ]
}
const formData = reactive({
    code: '',
    account: '',
    password: '',
    scene: 0
})
const isAccountLogin = computed(() => formData.scene == LoginWayEnum.ACCOUNT)
const isMobileLogin = computed(() => formData.scene == LoginWayEnum.MOBILE)
const includeLoginWay = (way: LoginWayEnum) =>
    appStore.getLoginConfig?.login_way?.includes(String(way)) ?? false

const inWxAuth = computed(() => {
    return appStore.getLoginConfig.wechat_auth
})

const isOpenAgreement = computed(
    () => appStore.getLoginConfig.login_agreement == 1
)
const isOpenOtherAuth = computed(() => appStore.getLoginConfig.third_auth == 1)
const isForceBindMobile = computed(
    () => appStore.getLoginConfig.coerce_mobile == 1
)
const isOpenSocialLogin = computed(() => appStore.getLoginConfig.social_login == 1)
const showSocialLogin = ref(false)
const socialLoginTypes = ref<Record<string, boolean>>({})
const changeLoginWay = () => {
    if (formData.scene == LoginWayEnum.ACCOUNT) {
        formData.scene = LoginWayEnum.MOBILE
    } else {
        formData.scene = LoginWayEnum.ACCOUNT
    }
}
const verificationCodeRef = shallowRef()
const sendSms = async () => {
    await formRef.value?.validateField(['account'])
    await smsSend({
        scene: SMSEnum.LOGIN,
        mobile: formData.account
    })

    verificationCodeRef.value?.start()
}

const handleLogin = async () => {
    await formRef.value?.validate()
    const data = await login(formData)
    if (isForceBindMobile.value && !data.mobile) {
        userStore.temToken = data.token
        setPopupType(PopupTypeEnum.BIND_MOBILE)
        return
    }
    userStore.login(data.token)
    await userStore.getUser()
    toggleShowPopup(false)
}
const { lockFn: handleLoginLock, isLock } = useLockFn(handleLogin)
const agreementConfirm = async () => {
    if (isAgreement.value) {
        return
    }
    await feedback.confirm('确认已阅读并同意《服务协议》和《隐私政策》')
    isAgreement.value = true
}
const loginLock = async () => {
    await agreementConfirm()
    await handleLoginLock()
}

const getWxCode = async () => {
    await agreementConfirm()
    const { url } = await getWxCodeUrl()
    await navigateTo(url, { external: true })
}
const { lockFn: getWxCodeLock } = useLockFn(getWxCode)

const getSocialLoginTypes = () => {
    if (!isOpenSocialLogin.value) {
        socialLoginTypes.value = {}
        return
    }
    socialLoginTypes.value = {
        qq: true,
        wx: true,
        alipay: true,
        baidu: true,
        microsoft: true
    }
}

const handleSocialLogin = async (type: string) => {
    try {
        const res: any = await useNuxtApp().$api.get('/api/login/socialLogin', { type })
        if (res.code === 1) {
            feedback.msgError(res.msg || '获取登录地址失败')
            return
        }
        const url = res.data?.url
        if (url) {
            window.location.href = url
        } else {
            feedback.msgError('获取登录地址失败')
        }
    } catch (error: any) {
        feedback.msgError(error?.msg || '登录失败')
    }
}

watch(
    () => appStore.getLoginConfig,
    (value) => {
        const { login_way } = value
        if (login_way && login_way.length) {
            formData.scene = login_way?.[0] ?? LoginWayEnum.ACCOUNT
        }
        getSocialLoginTypes()
    },
    {
        immediate: true
    }
)
</script>

<style lang="scss" scoped></style>
