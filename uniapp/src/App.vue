<!-- 开发者：杰哥网络科技 qq2711793818 杰哥 -->
<script setup lang="ts">
import { onLaunch, onShow } from '@dcloudio/uni-app'
import { useAppStore } from './stores/app'
import { useUserStore } from './stores/user'
import { useThemeStore } from './stores/theme'
import { useRoute, useRouter } from 'uniapp-router-next'
const appStore = useAppStore()
const userStore = useUserStore()
const themeStore = useThemeStore()
const router = useRouter()
const route = useRoute()

let lastConfigTime = 0
const CONFIG_REFRESH_INTERVAL = 5 * 60 * 1000

//#ifdef H5
const setH5WebIcon = () => {
    const config = appStore.getWebsiteConfig
    let favicon: HTMLLinkElement = document.querySelector('link[rel="icon"]')!
    if (favicon) {
        favicon.href = config.h5_favicon
        return
    }
    favicon = document.createElement('link')
    favicon.rel = 'icon'
    favicon.href = config.h5_favicon
    document.head.appendChild(favicon)
}

const parseInviteCodeFromUrl = () => {
    let hasInviteCode = false
    const url = window.location.href
    let inviteCode = ''

    try {
        const hashIndex = url.indexOf('#')
        if (hashIndex !== -1) {
            const hashPart = url.substring(hashIndex + 1)
            const queryIndex = hashPart.indexOf('?')
            if (queryIndex !== -1) {
                const queryString = hashPart.substring(queryIndex + 1)
                const params = new URLSearchParams(queryString)
                inviteCode = params.get('invite_code') || ''
            }
        }
    } catch (e) {
        console.error('解析邀请码失败', e)
    }

    if (inviteCode) {
        uni.setStorageSync('invite_code', inviteCode)
        hasInviteCode = true
    }

    return hasInviteCode
}
//#endif

const getConfig = async () => {
    await appStore.getConfig()
    //#ifdef H5
    setH5WebIcon()
    //#endif
    const { status, page_status, page_url } = appStore.getH5Config
    if (route.meta.webview) return
}

const onLaunchFn = async (options: any) => {
    let hasInviteCode = false
    const query = options.query || {}

    if (query.invite_code) {
        uni.setStorageSync('invite_code', query.invite_code)
        hasInviteCode = true
    }

    if (query.scene) {
        const scene = decodeURIComponent(query.scene)
        if (scene.includes('=')) {
            const params = new URLSearchParams(scene)
            const code = params.get('invite_code') || params.get('code')
            if (code) {
                uni.setStorageSync('invite_code', code)
                hasInviteCode = true
            }
        } else {
            uni.setStorageSync('invite_code', scene)
            hasInviteCode = true
        }
    }

    //#ifdef H5
    if (!hasInviteCode) {
        hasInviteCode = parseInviteCodeFromUrl()
    }
    //#endif

    themeStore.getTheme()
    await appStore.getConfig()
    lastConfigTime = Date.now()

    //#ifdef H5
    setH5WebIcon()
    const { status, page_status, page_url } = appStore.getH5Config
    if (status == 0) {
        if (page_status == 1) return (location.href = page_url)
        uni.reLaunch({ url: '/pages/empty/empty' })
        return
    }
    //#endif

    await userStore.getUser()

    const storedInviteCode = uni.getStorageSync('invite_code')
    if (!userStore.isLogin && storedInviteCode) {
        setTimeout(() => {
            uni.navigateTo({ url: '/pages/register/register' })
        }, 200)
    }
}

const onShowFn = async () => {
    //#ifdef H5
    parseInviteCodeFromUrl()
    //#endif

    const now = Date.now()
    if (now - lastConfigTime > CONFIG_REFRESH_INTERVAL) {
        try {
            await appStore.getConfig()
            lastConfigTime = now
            //#ifdef H5
            setH5WebIcon()
            //#endif
        } catch (e) {
            console.error('刷新配置失败', e)
        }
    }
}

onLaunch((options) => {
    onLaunchFn(options)
})

onShow(() => {
    onShowFn()
})
</script>
<style lang="scss">
//
</style>
