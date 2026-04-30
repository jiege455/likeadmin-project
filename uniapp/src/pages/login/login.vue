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
        class="bg-white login min-h-full flex flex-col items-center px-[40rpx] pt-[120rpx] box-border"
    >
        <view>
            <image
                :src="appStore.getImageUrl(appStore.getWebsiteConfig.shop_logo)"
                mode="widthFix"
                class="w-[160rpx] h-[160rpx] rounded-full"
            />
        </view>
        <view class="w-full mt-[140rpx] pb-[60rpx]">
            <block v-if="!phoneLogin">
                <!-- #ifdef MP-WEIXIN || H5 -->
                <view v-if="isOpenOtherAuth && isWeixin && inWxAuth" class="mb-[20rpx]">
                    <u-button
                        type="primary"
                        @click="wxLogin"
                        :customStyle="{ height: '100rpx' }"
                        hover-class="none"
                    >
                        微信一键登录
                    </u-button>
                    <view class="text-center text-gray-400 text-sm mt-[20rpx]">
                        新用户授权即自动注册
                    </view>
                </view>
                <!-- #endif -->

                <view class="mt-[40rpx]">
                    <u-button
                        @click="phoneLogin = !phoneLogin"
                        :customStyle="{ height: '100rpx' }"
                        hover-class="none"
                    >
                        {{ isOpenOtherAuth && isWeixin && inWxAuth ? '其他方式登录' : '账号登录' }}
                    </u-button>
                </view>

                <!-- 聚合登录入口 -->
                <view class="mt-[30rpx]" v-if="isOpenSocialLogin && hasSocialLoginTypes">
                    <u-button
                        @click="showSocialLogin = true"
                        :customStyle="{ height: '100rpx', color: '#333333' }"
                        hover-class="none"
                        plain
                    >
                        <text class="text-[28rpx] text-black">第三方账号登录</text>
                    </u-button>
                </view>
            </block>
            <block v-if="phoneLogin">
                <!-- 密码登录 -->
                <template
                    v-if="
                        formData.scene == LoginWayEnum.ACCOUNT &&
                        includeLoginWay(LoginWayEnum.ACCOUNT)
                    "
                >
                    <view
                        class="px-[18rpx] border border-solid border-lightc border-light rounded-[10rpx] h-[100rpx] items-center flex"
                    >
                        <u-input
                            class="flex-1"
                            v-model="formData.account"
                            :border="false"
                            placeholder="输入账号"
                        />
                    </view>
                    <view
                        class="px-[18rpx] py-[10rpx] border border-solid border-light rounded-[10rpx] flex h-[100rpx] items-center mt-[40rpx]"
                    >
                        <u-input
                            class="flex-1"
                            v-model="formData.password"
                            type="password"
                            placeholder="输入密码"
                            :border="false"
                        />
                        <navigator url="/pages/forget_pwd/forget_pwd" hover-class="none">
                            <view
                                class="border-l border-solid border-0 border-light pl-3 text-muted leading-4 ml-3"
                            >
                                忘记密码？
                            </view>
                        </navigator>
                    </view>
                </template>
                <!-- 验证码登录 -->
                <template
                    v-if="
                        formData.scene == LoginWayEnum.MOBILE &&
                        includeLoginWay(LoginWayEnum.MOBILE)
                    "
                >
                    <view
                        class="px-[18rpx] border border-solid border-lightc border-light rounded-[10rpx] h-[100rpx] items-center flex"
                    >
                        <u-input
                            class="flex-1"
                            v-model="formData.account"
                            :border="false"
                            placeholder="请输入手机号码"
                        />
                    </view>
                    <view
                        class="px-[18rpx] border border-solid border-lightc border-light rounded-[10rpx] h-[100rpx] items-center flex mt-[40rpx]"
                    >
                        <u-input
                            class="flex-1"
                            v-model="formData.code"
                            placeholder="请输入验证码"
                            :border="false"
                        />

                        <view
                            class="border-l border-solid border-0 border-light pl-3 leading-4 ml-3 w-[180rpx]"
                            @click="sendSms"
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
                    </view>
                </template>
            </block>

            <view class="mt-[40rpx]" v-if="isOpenAgreement">
                <u-checkbox v-model="isCheckAgreement" shape="circle">
                    <view class="text-xs flex">
                        已阅读并同意
                        <view @click.stop>
                            <navigator
                                class="text-primary"
                                hover-class="none"
                                url="/pages/agreement/agreement?type=service"
                            >
                                《服务协议》
                            </navigator>
                        </view>

                        和
                        <view @click.stop>
                            <navigator
                                class="text-primary"
                                hover-class="none"
                                url="/pages/agreement/agreement?type=privacy"
                            >
                                《隐私协议》
                            </navigator>
                        </view>
                    </view>
                </u-checkbox>
            </view>
            <block v-if="phoneLogin">
                <view class="mt-[60rpx]">
                    <u-button
                        type="primary"
                        @click="handleLogin(formData.scene)"
                        :customStyle="{
                            height: '100rpx',
                            opacity: DisableStyle ? '1' : '0.5'
                        }"
                        hover-class="none"
                    >
                        登录
                    </u-button>
                </view>
                <view class="flex justify-between mt-[40rpx]">
                    <view
                        >已有账号，使用
                        <span
                            class="text-primary"
                            @click="changeLoginWay(LoginWayEnum.ACCOUNT)"
                            v-if="
                                formData.scene == LoginWayEnum.MOBILE &&
                                includeLoginWay(LoginWayEnum.ACCOUNT)
                            "
                            >密码登录</span
                        >
                        <span
                            class="text-primary"
                            @click="changeLoginWay(LoginWayEnum.MOBILE)"
                            v-if="
                                formData.scene == LoginWayEnum.ACCOUNT &&
                                includeLoginWay(LoginWayEnum.MOBILE)
                            "
                            >验证码登录</span
                        >
                    </view>
                    <navigator url="/pages/register/register" hover-class="none"
                        >注册账号</navigator
                    >
                </view>
            </block>
        </view>
        <!-- 协议弹框 -->
        <u-modal
            v-model="showModel"
            show-cancel-button
            :show-title="false"
            confirm-color="var(--color-primary)"
            @confirm=";(isCheckAgreement = true), (showModel = false)"
            @cancel="showModel = false"
        >
            <view class="text-center px-[70rpx] py-[60rpx]">
                <view> 请先阅读并同意 </view>
                <view class="flex justify-center">
                    <navigator data-theme="" url="/pages/agreement/agreement?type=service">
                        <view class="text-primary">《服务协议》</view>
                    </navigator>
                    和
                    <navigator url="/pages/agreement/agreement?type=privacy">
                        <view class="text-primary">《隐私协议》</view>
                    </navigator>
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
        <!--  #endif -->
        
        <!-- 滑块验证码弹窗 -->
        <slider-captcha
            v-model:show="showCaptcha"
            @success="handleCaptchaSuccess"
            @fail="handleCaptchaFail"
        />

        <!-- 聚合登录弹窗 -->
        <u-popup v-model:show="showSocialLogin" mode="bottom" border-radius="20" :closeable="true">
            <view class="p-[40rpx]">
                <view class="text-center text-lg font-medium mb-[40rpx]">选择登录方式</view>
                <view class="flex flex-wrap justify-center gap-[40rpx]">
                    <view v-if="socialLoginTypes.qq" class="flex flex-col items-center" @click="handleSocialLogin('qq')">
                        <image src="/static/images/social/qq.png" mode="aspectFit" class="w-[80rpx] h-[80rpx]"></image>
                        <text class="text-sm mt-[10rpx]">QQ</text>
                    </view>
                    <view v-if="socialLoginTypes.wx" class="flex flex-col items-center" @click="handleSocialLogin('wx')">
                        <image src="/static/images/social/wechat.png" mode="aspectFit" class="w-[80rpx] h-[80rpx]"></image>
                        <text class="text-sm mt-[10rpx]">微信</text>
                    </view>
                    <view v-if="socialLoginTypes.alipay" class="flex flex-col items-center" @click="handleSocialLogin('alipay')">
                        <image src="/static/images/social/alipay.png" mode="aspectFit" class="w-[80rpx] h-[80rpx]"></image>
                        <text class="text-sm mt-[10rpx]">支付宝</text>
                    </view>
                    <view v-if="socialLoginTypes.baidu" class="flex flex-col items-center" @click="handleSocialLogin('baidu')">
                        <image src="/static/images/social/baidu.png" mode="aspectFit" class="w-[80rpx] h-[80rpx]"></image>
                        <text class="text-sm mt-[10rpx]">百度</text>
                    </view>
                    <view v-if="socialLoginTypes.microsoft" class="flex flex-col items-center" @click="handleSocialLogin('microsoft')">
                        <image src="/static/images/social/microsoft.png" mode="aspectFit" class="w-[80rpx] h-[80rpx]"></image>
                        <text class="text-sm mt-[10rpx]">微软</text>
                    </view>
                </view>
            </view>
        </u-popup>
    </view>
</template>

<script setup lang="ts">
/**
 * 前端用户登录页面
 * 开发者：杰哥网络科技
 * QQ: 2711793818
 */
import { login, mnpLogin, updateUser, OALogin } from '@/api/account'
import { smsSend } from '@/api/app'
import { SMSEnum } from '@/enums/appEnums'
import { BACK_URL } from '@/enums/constantEnums'
import { useLockFn } from '@/hooks/useLockFn'
import { useAppStore } from '@/stores/app'
import { useUserStore } from '@/stores/user'
import { useRouter, useRoute } from 'uniapp-router-next'
import cache from '@/utils/cache'
import { isWeixinClient } from '@/utils/client'
import { safeNavigateBack } from '@/utils/util'
import SliderCaptcha from '@/components/slider-captcha/slider-captcha.vue'
// #ifdef H5
import wechatOa, { UrlScene } from '@/utils/wechat'
// #endif
import { onLoad, onShow } from '@dcloudio/uni-app'
import { computed, reactive, ref, shallowRef, watch } from 'vue'

enum LoginWayEnum {
    ACCOUNT = 1,
    MOBILE = 2
}

const isWeixin = ref(true)
// #ifdef H5
isWeixin.value = isWeixinClient()
// #endif

const route = useRoute()
const router = useRouter()
const userStore = useUserStore()
const appStore = useAppStore()
const showModel = ref(false)
const uCodeRef = shallowRef()
const codeTips = ref('')
const showLoginPopup = ref(false)
const isCheckAgreement = ref(false)
const showCaptcha = ref(false)

const formData = reactive({
    scene: 1,
    account: '',
    password: '',
    code: ''
})
const phoneLogin = ref(false)
const loginData = ref()
const codeChange = (text: string) => {
    codeTips.value = text
}
const showSocialLogin = ref(false)
const socialLoginTypes = ref<Record<string, boolean>>({})

const websiteConfig = computed(() => appStore.getWebsiteConfig)

const sendSms = async () => {
    if (!formData.account) return
    if (uCodeRef.value?.canGetCode) {
        await smsSend({
            scene: SMSEnum.LOGIN,
            mobile: formData.account
        })
        uni.$u.toast('发送成功')
        uCodeRef.value?.start()
    }
}

const changeLoginWay = (way: LoginWayEnum) => {
    formData.scene = way
}

const includeLoginWay = (way: LoginWayEnum) => {
    return appStore.getLoginConfig.login_way?.includes(String(way))
}

const inWxAuth = computed(() => {
    return appStore.getLoginConfig.wechat_auth
})

const isOpenAgreement = computed(() => appStore.getLoginConfig.login_agreement == 1)

const isOpenOtherAuth = computed(() => appStore.getLoginConfig.third_auth == 1)
const isForceBindMobile = computed(() => appStore.getLoginConfig.coerce_mobile == 1)
const isOpenSocialLogin = computed(() => appStore.getLoginConfig.social_login == 1)
const hasSocialLoginTypes = computed(() => Object.keys(socialLoginTypes.value).length > 0)

const getSocialLoginTypes = () => {
    if (!isOpenSocialLogin.value) {
        socialLoginTypes.value = {}
        return
    }
    // 根据后台配置的登录方式显示
    const types = {
        qq: appStore.getLoginConfig.social_login_qq_enable == 1,
        wx: appStore.getLoginConfig.social_login_wx_enable == 1,
        alipay: appStore.getLoginConfig.social_login_alipay_enable == 1,
        baidu: appStore.getLoginConfig.social_login_baidu_enable == 1,
        microsoft: appStore.getLoginConfig.social_login_microsoft_enable == 1
    }
    // 只保留启用的登录方式
    socialLoginTypes.value = Object.fromEntries(
        Object.entries(types).filter(([key, value]) => value)
    )
}

const handleSocialLogin = async (type: string) => {
    if (!isCheckAgreement.value && isOpenAgreement.value) {
        showModel.value = true
        return
    }

    uni.showLoading({ title: '请稍后...' })
    try {
        const res: any = await uni.request({
            url: `${uni.$u.http.config.baseUrl}/api/login/socialLogin`,
            method: 'GET',
            data: { type },
            header: {
                token: uni.getStorageSync('token') || ''
            }
        })
        uni.hideLoading()

        // 注意：uni.request 返回的数据结构是 res.data，不是直接 res
        const responseData = res.data || res

        if (responseData.code === 0 || responseData.code === undefined) {
            const url = responseData.data?.url
            if (url) {
                window.location.href = url
            } else {
                uni.$u.toast('获取登录地址失败')
            }
        } else {
            uni.$u.toast(responseData.msg || '获取登录地址失败')
        }
    } catch (error: any) {
        uni.hideLoading()
        uni.$u.toast(error?.msg || error?.message || '登录失败')
    }
}

const loginFun = async () => {
    if (!isCheckAgreement.value && isOpenAgreement.value) return (showModel.value = true)
    if (formData.scene == LoginWayEnum.ACCOUNT) {
        if (!formData.account) return uni.$u.toast('请输入账号/手机号码')
        if (!formData.password) return uni.$u.toast('请输入密码')
        
        showCaptcha.value = true
        return
    }
    if (formData.scene == LoginWayEnum.MOBILE) {
        if (!formData.account) return uni.$u.toast('请输入手机号码')
        if (!formData.code) return uni.$u.toast('请输入验证码')
    }
    
    doLogin()
}

const handleCaptchaSuccess = () => {
    showCaptcha.value = false
    doLogin()
}

const handleCaptchaFail = () => {
}

const doLogin = async () => {
    uni.showLoading({
        title: '请稍后...'
    })
    try {
        const data = await login(formData)
        loginHandle(data)
    } catch (error: any) {
        uni.hideLoading()
        const errorMsg = typeof error === 'string' ? error : (error?.msg || error?.message || '登录失败')
        uni.$u.toast(errorMsg)
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

const { lockFn: handleLogin } = useLockFn(loginFun)

const oaLogin = async (options: any = { getUrl: true }) => {
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

const wxLogin = async () => {
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
    // 检查是否在微信浏览器中
    if (isWeixin.value) {
        // H5 微信授权登录流程
        oaLogin()
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

watch(
    () => appStore.getLoginConfig,
    (value) => {
        if (value.login_way) {
            formData.scene = value.login_way[0]
        }
        getSocialLoginTypes()
    },
    {
        immediate: true
    }
)
const DisableStyle = computed(() => {
    if (formData.scene == 1 && formData.account && formData.password) {
        return true
    } else if (formData.scene == 2 && formData.account && formData.code) {
        return true
    } else {
        return false
    }
})

const removeWxQuery = () => {
    const options = route.query
    if (options.code && options.state) {
        delete options.code
        delete options.state
        router.redirectTo({ path: route.path, query: options })
    }
}

onLoad(async () => {
    //#ifdef H5
    // 处理聚合登录回调参数
    const query = route.query
    if (query.social_login === '1' && query.token) {
        uni.showLoading({ title: '登录中...' })
        try {
            const loginData = {
                token: query.token,
                user_id: query.user_id,
                nickname: decodeURIComponent(query.nickname || ''),
                avatar: decodeURIComponent(query.avatar || '')
            }
            // 直接处理登录
            userStore.login(loginData.token)
            await userStore.getUser()
            uni.$u.toast('登录成功')

            // 清除URL参数，防止刷新时重复处理
            setTimeout(() => {
                uni.switchTab({ url: '/pages/index/index' })
            }, 500)
        } catch (error) {
            console.error('聚合登录回调失败:', error)
            uni.$u.toast('登录失败，请重试')
        } finally {
            uni.hideLoading()
        }
        return
    }

    const options = wechatOa.getAuthData()
    try {
        if (options.code && options.scene === UrlScene.LOGIN) {
            uni.showLoading({
                title: '请稍后...'
            })
            const data = await oaLogin(options)
            if (data) {
                loginData.value = data

                loginHandle(loginData.value)
            }
        }
    } catch (error) {
        removeWxQuery()
    } finally {
        uni.hideLoading()
        //清除保存的授权数据
        wechatOa.setAuthData()
    }
    //#endif
})
</script>

<style lang="scss">
page {
    height: 100%;
}
</style>
