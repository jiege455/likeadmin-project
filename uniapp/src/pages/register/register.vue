<!--
    开发者公众号：杰哥网络科技
    QQ: 2711793818 杰哥
-->
<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar :front-color="$theme.navColor" :background-color="$theme.navBgColor" />
        <!-- #endif -->
    </page-meta>
    <view
        class="register bg-white min-h-full flex flex-col items-center px-[40rpx] pt-[40rpx] box-border"
    >
        <view class="w-full">
            <view class="text-2xl font-medium mb-[60rpx]">注册新账号</view>

            <view v-if="!switchConfig.register_open" class="text-center py-[100rpx]">
                <view class="text-gray-400 text-lg">注册功能已关闭</view>
                <navigator
                    url="/pages/login/login"
                    hover-class="none"
                    class="text-primary mt-[20rpx]"
                >
                    返回登录
                </navigator>
            </view>

            <template v-else>
                <!-- #ifdef MP-WEIXIN || H5 -->
                <view v-if="isOpenOtherAuth && isWeixin && inWxAuth" class="mb-[40rpx]">
                    <u-button
                        type="primary"
                        @click="wxRegister"
                        :customStyle="{ height: '100rpx' }"
                        hover-class="none"
                    >
                        微信一键注册
                    </u-button>
                    <view class="text-center text-gray-400 text-sm mt-[20rpx]">
                        授权即自动注册账号
                    </view>
                </view>

                <view
                    v-if="isOpenOtherAuth && isWeixin && inWxAuth"
                    class="flex items-center my-[40rpx]"
                >
                    <view class="flex-1 h-[1rpx] bg-gray-200"></view>
                    <view class="px-[20rpx] text-gray-400 text-sm">或使用账号注册</view>
                    <view class="flex-1 h-[1rpx] bg-gray-200"></view>
                </view>
                <!-- #endif -->

                <view
                    class="px-[18rpx] border border-solid border-lightc border-light rounded-[10rpx] h-[100rpx] items-center flex"
                >
                    <u-input
                        class="flex-1"
                        v-model="formData.account"
                        :border="false"
                        placeholder="请输入账号"
                    />
                </view>

                <view
                    class="px-[18rpx] border border-solid border-lightc border-light rounded-[10rpx] h-[100rpx] items-center flex mt-[40rpx]"
                >
                    <u-input
                        class="flex-1"
                        type="password"
                        v-model="formData.password"
                        placeholder="请输入密码"
                        :border="false"
                    />
                </view>
                <view
                    class="px-[18rpx] border border-solid border-lightc border-light rounded-[10rpx] h-[100rpx] items-center flex mt-[40rpx]"
                >
                    <u-input
                        class="flex-1"
                        type="password"
                        v-model="formData.password_confirm"
                        placeholder="请再次输入密码"
                        :border="false"
                    />
                </view>

                <view
                    v-if="needMobile"
                    class="px-[18rpx] border border-solid border-lightc border-light rounded-[10rpx] h-[100rpx] items-center flex mt-[40rpx]"
                >
                    <u-input
                        class="flex-1"
                        v-model="formData.mobile"
                        placeholder="请输入手机号"
                        type="number"
                        :border="false"
                    />
                </view>

                <view
                    v-if="needMobile"
                    class="px-[18rpx] border border-solid border-lightc border-light rounded-[10rpx] h-[100rpx] items-center flex mt-[40rpx]"
                >
                    <u-input
                        class="flex-1"
                        v-model="formData.mobile_code"
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
                    class="px-[18rpx] border border-solid border-lightc border-light rounded-[10rpx] h-[100rpx] items-center flex mt-[40rpx]"
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

                <view class="mt-[40rpx]" v-if="isOpenAgreement">
                    <u-checkbox v-model="isCheckAgreement" shape="circle">
                        <view class="text-xs flex">
                            已阅读并同意
                            <view @click.stop>
                                <router-navigate
                                    class="text-primary"
                                    hover-class="none"
                                    to="/pages/agreement/agreement?type=service"
                                >
                                    《服务协议》
                                </router-navigate>
                            </view>

                            和
                            <view @click.stop>
                                <router-navigate
                                    class="text-primary"
                                    hover-class="none"
                                    to="/pages/agreement/agreement?type=privacy"
                                >
                                    《隐私协议》
                                </router-navigate>
                            </view>
                        </view>
                    </u-checkbox>
                </view>
                <view class="mt-[60rpx]">
                    <u-button
                        type="primary"
                        hover-class="none"
                        @click="accountRegister"
                        :customStyle="{
                            height: '100rpx',
                            opacity:
                                formData.account && formData.password && formData.password_confirm
                                    ? '1'
                                    : '0.5'
                        }"
                    >
                        注册
                    </u-button>
                </view>
                <view class="flex justify-center mt-[40rpx]">
                    <navigator url="/pages/login/login" hover-class="none" class="text-primary">
                        已有账号？去登录
                    </navigator>
                </view>
            </template>
        </view>
    </view>
    <u-modal
        v-model="showModel"
        show-cancel-button
        :show-title="false"
        @confirm=";(isCheckAgreement = true), (showModel = false)"
        @cancel="showModel = false"
        confirm-color="var(--color-primary)"
    >
        <view class="text-center px-[70rpx] py-[60rpx]">
            <view> 请先阅读并同意</view>
            <view class="flex justify-center">
                <router-navigate data-theme="" to="/pages/agreement/agreement?type=service">
                    <view class="text-primary">《服务协议》</view>
                </router-navigate>
                和
                <router-navigate to="/pages/agreement/agreement?type=privacy">
                    <view class="text-primary">《隐私协议》</view>
                </router-navigate>
            </view>
        </view>
    </u-modal>
    <!-- #ifdef MP-WEIXIN -->
    <mplogin-popup
        v-model:show="showLoginPopup"
        :logo="websiteConfig.shop_logo"
        :title="websiteConfig.shop_name"
        @update="handleUpdateUser"
    />
    <!-- #endif -->
</template>

<!-- 开发者：杰哥网络科技 qq2711793818 杰哥 -->
<script setup lang="ts">
import { register, login, mnpLogin, OALogin, updateUser } from '@/api/account'
import { smsSend } from '@/api/app'
import { emailSendCode } from '@/api/email'
import { getSwitchConfig } from '@/api/system'
import { useAppStore } from '@/stores/app'
import { useUserStore } from '@/stores/user'
import { useRouter, useRoute } from 'uniapp-router-next'
import cache from '@/utils/cache'
import { isWeixinClient } from '@/utils/client'
import { safeNavigateBack } from '@/utils/util'
// #ifdef H5
import wechatOa, { UrlScene } from '@/utils/wechat'
// #endif
import { computed, reactive, ref, shallowRef, onUnmounted } from 'vue'
import { onLoad } from '@dcloudio/uni-app'
import { SMSEnum } from '@/enums/appEnums'
import { EmailEnum } from '@/enums/appEnums'
import { BACK_URL } from '@/enums/constantEnums'

const isWeixin = ref(true)
// #ifdef H5
isWeixin.value = isWeixinClient()
// #endif

const route = useRoute()
const router = useRouter()
const isCheckAgreement = ref(false)
const appStore = useAppStore()
const userStore = useUserStore()
const isOpenAgreement = computed(() => appStore.getLoginConfig.login_agreement == 1)
const showModel = ref(false)
const showLoginPopup = ref(false)
const loginData = ref()
const websiteConfig = computed(() => appStore.getWebsiteConfig)

const isOpenOtherAuth = computed(() => appStore.getLoginConfig.third_auth == 1)
const inWxAuth = computed(() => appStore.getLoginConfig.wechat_auth == 1)
const isForceBindMobile = computed(() => appStore.getLoginConfig.coerce_mobile == 1)

const switchConfig = ref({
    register_open: 1,
    register_verify_type: 'email',
    email_notify_open: 0,
    sms_notify_open: 1
})

const verifyType = computed(() => switchConfig.value.register_verify_type)
const needMobile = computed(() => switchConfig.value.sms_notify_open && verifyType.value === 'mobile')
const needEmail = computed(() => switchConfig.value.email_notify_open && verifyType.value === 'email')

const formData = reactive({
    account: '',
    password: '',
    password_confirm: '',
    mobile: '',
    mobile_code: '',
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
        switchConfig.value = res
    } catch (e) {
        console.error('获取配置失败', e)
    }
}

const sendSmsCode = async () => {
    if (!formData.mobile) {
        uni.$u.toast('请输入手机号')
        return
    }
    if (!/^1[3-9]\d{9}$/.test(formData.mobile)) {
        uni.$u.toast('手机号格式错误')
        return
    }
    if (mobileCountdown.value > 0) return

    try {
        await smsSend({ scene: SMSEnum.BIND_MOBILE, mobile: formData.mobile })
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
        await emailSendCode({ scene: EmailEnum.USER_REGISTER, email: formData.email })
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

const accountRegister = async () => {
    if (!formData.account) return uni.$u.toast('请输入账号')
    if (!formData.password) return uni.$u.toast('请输入密码')
    if (!formData.password_confirm) return uni.$u.toast('请输入确认密码')
    if (!isCheckAgreement.value && isOpenAgreement.value) return (showModel.value = true)
    if (formData.password != formData.password_confirm) return uni.$u.toast('两次输入的密码不一致')

    if (needMobile.value) {
        if (!formData.mobile) return uni.$u.toast('请输入手机号')
        if (!formData.mobile_code) return uni.$u.toast('请输入手机验证码')
    }

    if (needEmail.value) {
        if (!formData.email) return uni.$u.toast('请输入邮箱')
        if (!formData.email_code) return uni.$u.toast('请输入邮箱验证码')
    }

    const inviteCode = uni.getStorageSync('invite_code') || ''
    const submitData: any = {
        account: formData.account,
        password: formData.password,
        password_confirm: formData.password_confirm,
        invite_code: inviteCode
    }

    if (needMobile.value) {
        submitData.mobile = formData.mobile
        submitData.mobile_code = formData.mobile_code
    }

    if (needEmail.value) {
        submitData.email = formData.email
        submitData.email_code = formData.email_code
    }

    uni.showLoading({ title: '注册中...' })
    try {
        const registerRes: any = await register(submitData)
        uni.$u.toast('注册成功')

        const res: any = await login({
            scene: 1,
            account: formData.account,
            password: formData.password
        })
        if (res.token) {
            // 如果开启了强制绑定手机，直接走强制绑定流程，不需要引导弹窗
            if (isForceBindMobile.value && !res.mobile) {
                userStore.temToken = res.token
                uni.hideLoading()
                router.navigateTo('/pages/bind_mobile/bind_mobile')
                return
            }
            
            userStore.login(res.token)
            await userStore.getUser()
            uni.hideLoading()
            
            // 检查是否需要引导绑定（只有未开启强制绑定时才显示引导弹窗）
            if (registerRes?.guide_bind?.need_bind && !isForceBindMobile.value) {
                const bindType = registerRes.guide_bind.bind_type
                uni.showModal({
                    title: '完善信息',
                    content: registerRes.guide_bind.message,
                    confirmText: '去绑定',
                    cancelText: '稍后再说',
                    success: (modalRes) => {
                        if (modalRes.confirm) {
                            uni.navigateTo({ url: `/pages/guide_bind/guide_bind?type=${bindType}` })
                        } else {
                            uni.switchTab({ url: '/pages/index/index' })
                        }
                    }
                })
            } else {
                uni.$u.toast('登录成功')
                setTimeout(() => {
                    uni.switchTab({ url: '/pages/index/index' })
                }, 500)
            }
        }
    } catch (e: any) {
        uni.hideLoading()
        uni.$u.toast(e || '操作失败，请重试')
    }
}

const loginHandle = async (data: any) => {
    const { token, mobile } = data
    if (!mobile && isForceBindMobile.value) {
        userStore.temToken = token
        router.navigateTo('/pages/bind_mobile/bind_mobile')
        uni.hideLoading()
        return
    }
    userStore.login(data.token)
    await userStore.getUser()
    uni.$u.toast('登录成功')
    uni.hideLoading()

    const backUrl = cache.get(BACK_URL)
    cache.remove(BACK_URL)

    setTimeout(() => {
        const pages = getCurrentPages()
        if (pages && pages.length > 1) {
            safeNavigateBack({ defaultUrl: backUrl || '/pages/index/index' })
        } else if (backUrl) {
            const tabBarPages = ['/pages/index/index', '/pages/user/user']
            if (tabBarPages.includes(backUrl)) {
                uni.switchTab({ url: backUrl })
            } else {
                uni.redirectTo({ url: backUrl })
            }
        } else {
            uni.switchTab({ url: '/pages/index/index' })
        }
    }, 500)
}

const oaRegister = async (options: any = { getUrl: true }) => {
    const { code, getUrl } = options
    if (getUrl) {
        await wechatOa.getUrl(UrlScene.LOGIN)
    } else {
        const data = await OALogin({
            code,
            invite_code: uni.getStorageSync('invite_code') || '',
            merchant_id: uni.getStorageSync('merchant_id') || ''
        })
        return data
    }
    return Promise.reject()
}

const wxRegister = async () => {
    if (!isCheckAgreement.value && isOpenAgreement.value) {
        showModel.value = true
        return
    }

    // #ifdef MP-WEIXIN
    uni.showLoading({
        title: '请稍后...'
    })
    try {
        const { code }: any = await uni.login({
            provider: 'weixin'
        })
        const data = await mnpLogin({
            code: code,
            invite_code: uni.getStorageSync('invite_code') || '',
            merchant_id: uni.getStorageSync('merchant_id') || ''
        })
        loginData.value = data
        if (data.is_new_user) {
            uni.hideLoading()
            userStore.temToken = data.token
            showLoginPopup.value = true
            return
        }
        loginHandle(data)
    } catch (error: any) {
        uni.hideLoading()
        uni.$u.toast(error)
    }
    // #endif

    // #ifdef H5
    if (isWeixin.value) {
        oaRegister()
    } else {
        uni.$u.toast('请在微信中打开使用此功能')
    }
    // #endif
}

const handleUpdateUser = async (value: any) => {
    await updateUser(value, { token: userStore.temToken })
    showLoginPopup.value = false
    loginHandle(loginData.value)
}

const removeWxQuery = () => {
    const options = route.query
    if (options.code && options.state) {
        delete options.code
        delete options.state
        router.redirectTo({ path: route.path, query: options })
    }
}

onLoad(async () => {
    fetchSwitchConfig()
    // #ifdef H5
    const options = wechatOa.getAuthData()
    try {
        if (options.code && options.scene === UrlScene.LOGIN) {
            uni.showLoading({
                title: '请稍后...'
            })
            const data = await oaRegister(options)
            if (data) {
                loginData.value = data
                loginHandle(loginData.value)
            }
        }
    } catch (error) {
        removeWxQuery()
    } finally {
        uni.hideLoading()
        wechatOa.setAuthData()
    }
    // #endif
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

<style lang="scss">
page {
    height: 100%;
}
</style>
