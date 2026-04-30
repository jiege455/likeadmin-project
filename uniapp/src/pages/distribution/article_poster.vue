<template>
    <uni-nav title="文章推广"></uni-nav>

    <view class="article-poster min-h-screen bg-f5">
        <view class="poster-content mx-3 mt-3">
            <view
                class="poster-container relative bg-white shadow-lg overflow-hidden rounded-2xl mx-auto"
                :style="{ width: '650rpx' }"
            >
            <view class="absolute inset-0">
                <view
                    class="w-full h-full"
                    :style="{
                        background:
                            'linear-gradient(135deg, ' +
                            themeStore.primaryColor +
                            ' 0%, #667eea 50%, #764ba2 100%)'
                    }"
                ></view>
                <view class="absolute inset-0 bg-black/10"></view>
            </view>

            <view class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
                <view
                    class="absolute -top-20 -left-20 w-80 h-80 bg-white/10 rounded-full blur-3xl"
                ></view>
                <view
                    class="absolute -bottom-20 -right-20 w-96 h-96 bg-white/10 rounded-full blur-3xl"
                ></view>
            </view>

            <view class="relative z-10">
                <view class="pt-8 pb-4 px-6 text-center">
                    <view class="flex items-center justify-center gap-3 mb-2">
                        <image
                            v-if="appStore.getWebsiteConfig.shop_logo"
                            :src="appStore.getImageUrl(appStore.getWebsiteConfig.shop_logo)"
                            class="w-12 h-12 rounded-xl shadow-lg"
                            mode="aspectFill"
                        ></image>
                        <view
                            v-else
                            class="w-12 h-12 rounded-xl flex items-center justify-center shadow-lg"
                            :style="{ backgroundColor: themeStore.primaryColor }"
                        >
                            <text class="text-white text-xl font-bold">
                                {{ (appStore.getWebsiteConfig.shop_name || '').charAt(0) }}
                            </text>
                        </view>
                        <view>
                            <text class="text-white text-xl font-bold tracking-wide">
                                {{ appStore.getWebsiteConfig.shop_name || '' }}
                            </text>
                        </view>
                    </view>
                    <view class="w-12 h-0.5 bg-white/30 rounded-full mx-auto"></view>
                </view>

                <view
                    class="mx-4 bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl overflow-hidden"
                >
                    <view class="p-5 border-b border-gray-100">
                        <view class="text-lg font-bold text-gray-800 line-clamp-2 mb-3">
                            {{ articleData.title || '精彩文章' }}
                        </view>
                        <view class="flex items-center justify-between">
                            <view class="flex items-center gap-2">
                                <u-image
                                    :src="
                                        articleData.merchant_image || '/static/images/default.png'
                                    "
                                    width="40rpx"
                                    height="40rpx"
                                    shape="circle"
                                ></u-image>
                                <view>
                                    <view class="text-sm font-medium text-gray-700">{{
                                        articleData.merchant_name || '平台'
                                    }}</view>
                                    <view class="text-xs text-gray-400">{{
                                        articleData.create_time
                                    }}</view>
                                </view>
                            </view>
                            <view
                                v-if="articleData.price > 0"
                                class="flex items-center bg-red-50 px-3 py-1 rounded-full"
                            >
                                <text class="text-red-500 font-bold">¥{{ articleData.price }}</text>
                            </view>
                        </view>
                    </view>

                    <view class="p-5 flex flex-col items-center">
                        <view class="relative">
                            <view
                                class="p-3 bg-white rounded-xl shadow-md border-2"
                                :style="{ borderColor: themeStore.primaryColor }"
                            >
                                <u-qrcode
                                    v-if="qrCodeUrl"
                                    :value="qrCodeUrl"
                                    :size="120"
                                    canvas-id="article-qrcode"
                                />
                                <view
                                    v-else
                                    class="w-[120px] h-[120px] flex items-center justify-center"
                                >
                                    <u-loading
                                        mode="circle"
                                        :color="themeStore.primaryColor"
                                        size="30"
                                    ></u-loading>
                                </view>
                            </view>
                        </view>

                        <view class="mt-3 flex items-center gap-2">
                            <view class="w-1.5 h-1.5 rounded-full bg-green-500"></view>
                            <text class="text-gray-500 text-xs">长按识别二维码阅读全文</text>
                        </view>
                    </view>
                </view>

                <view class="px-6 py-5 space-y-3">
                    <view
                        v-if="!isSaving"
                        class="h-11 rounded-full flex items-center justify-center text-white font-bold shadow-lg active:scale-95 transition-all"
                        :style="{
                            background:
                                'linear-gradient(135deg, ' + themeStore.primaryColor + ', #667eea)',
                            boxShadow: '0 8px 20px -8px ' + themeStore.primaryColor
                        }"
                        @click="copyArticleLink"
                    >
                        <u-icon name="link" size="18" color="#fff" class="mr-2"></u-icon>
                        <text>复制推广链接</text>
                    </view>
                    <view
                        v-if="!isSaving"
                        class="h-11 rounded-full flex items-center justify-center font-bold shadow-md active:scale-95 transition-all bg-white/20 backdrop-blur-md border border-white/30"
                        @click="savePoster"
                    >
                        <u-icon name="download" size="18" color="#fff" class="mr-2"></u-icon>
                        <text class="text-white">保存海报</text>
                    </view>
                </view>

                <view class="pb-5 text-center">
                    <text class="text-white/50 text-xs"
                        >Powered by {{ appStore.getWebsiteConfig.shop_name || '' }}</text
                    >
                </view>
            </view>
            </view>
        </view>

        <view class="mt-4 px-4">
            <view class="bg-white rounded-xl p-4">
                <view class="font-bold text-gray-800 mb-2">推广说明</view>
                <view class="text-sm text-gray-500 space-y-1">
                    <view>1. 分享文章链接给好友，好友通过链接注册后，您可获得佣金</view>
                    <view>2. 好友购买该文章后，您可获得额外奖励</view>
                    <view>3. 推广数据可在"我的-推广中心"查看</view>
                </view>
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { onLoad, onShow } from '@dcloudio/uni-app'
import { useThemeStore } from '@/stores/theme'
import { useUserStore } from '@/stores/user'
import { useAppStore } from '@/stores/app'
import { getArticleDetail } from '@/api/news'
import { getApplyDetail } from '@/api/distribution'
import UQrcode from '@/components/u-qrcode/u-qrcode.vue'
import html2canvas from 'html2canvas'
import { safeNavigateBack } from '@/utils/util'

const themeStore = useThemeStore()
const userStore = useUserStore()
const appStore = useAppStore()

const articleId = ref('')
const articleData = ref<any>({})
const qrCodeUrl = ref('')
const isSaving = ref(false)

const goBack = () => {
    safeNavigateBack({ defaultUrl: '/pages/index/index' })
}

const checkDistributorStatus = async () => {
    if (!userStore.isLogin) {
        uni.showToast({ title: '请先登录', icon: 'none' })
        setTimeout(() => {
            uni.navigateTo({ url: '/pages/login/login' })
        }, 1500)
        return false
    }

    try {
        const distributorStatus = await getApplyDetail()
        if (!distributorStatus.is_distributor) {
            uni.showModal({
                title: '提示',
                content: '您还不是分销员，是否立即申请成为分销员？',
                confirmText: '去申请',
                cancelText: '取消',
                success: (res) => {
                    if (res.confirm) {
                        uni.navigateTo({ url: '/pages/business/promotion-apply' })
                    } else {
                        safeNavigateBack({ defaultUrl: '/pages/index/index' })
                    }
                }
            })
            return false
        }
        return true
    } catch (e: any) {
        uni.showToast({ title: e.msg || '获取分销员状态失败', icon: 'none' })
        return false
    }
}

const getArticleData = async () => {
    if (!articleId.value) return
    try {
        const res = await getArticleDetail({ id: articleId.value })
        articleData.value = res
        generateQrCode()
    } catch (e) {
        console.error('获取文章详情失败', e)
    }
}

const generateQrCode = () => {
    const inviteCode = userStore.userInfo.sn || ''
    // #ifdef H5
    const baseUrl = window.location.origin
    qrCodeUrl.value = `${baseUrl}/#/pages/news_detail/news_detail?id=${articleId.value}&invite_code=${inviteCode}`
    // #endif
    // #ifndef H5
    qrCodeUrl.value = `/pages/news_detail/news_detail?id=${articleId.value}&invite_code=${inviteCode}`
    // #endif
}

const copyArticleLink = () => {
    if (!qrCodeUrl.value) {
        uni.showToast({ title: '链接生成中...', icon: 'none' })
        return
    }
    let fullUrl = qrCodeUrl.value
    // #ifndef H5
    // 使用配置中的域名，如果没有配置则使用当前域名
    const domain = appStore.config?.domain || ''
    if (domain) {
        fullUrl = `${domain}${qrCodeUrl.value}`
    } else {
        uni.showToast({ title: '请先配置网站域名', icon: 'none' })
        return
    }
    // #endif

    uni.setClipboardData({
        data: fullUrl,
        success: () => {
            uni.showToast({ title: '推广链接已复制', icon: 'success' })
        }
    })
}

const savePoster = async () => {
    // #ifdef H5
    try {
        isSaving.value = true
        uni.showLoading({ title: '生成海报中...' })

        await new Promise((resolve) => setTimeout(resolve, 100))

        const element = document.querySelector('.poster-container') as HTMLElement
        if (!element) {
            isSaving.value = false
            return
        }

        const canvas = await html2canvas(element, {
            useCORS: true,
            allowTaint: true,
            backgroundColor: null,
            scale: 2
        })

        const base64 = canvas.toDataURL('image/png')
        isSaving.value = false
        uni.hideLoading()

        uni.previewImage({
            urls: [base64],
            current: 0
        })

        setTimeout(() => {
            uni.showToast({ title: '请长按图片保存', icon: 'none', duration: 3000 })
        }, 500)
    } catch (error) {
        console.error('生成海报失败:', error)
        isSaving.value = false
        uni.hideLoading()
        uni.showToast({ title: '生成海报失败，请截图保存', icon: 'none' })
    }
    // #endif

    // #ifndef H5
    uni.showToast({ title: '请截图保存海报', icon: 'none' })
    // #endif
}

onLoad(async (options: any) => {
    if (options.article_id) {
        articleId.value = options.article_id
        if (options.article_title) {
            articleData.value.title = decodeURIComponent(options.article_title)
        }
        const isDistributor = await checkDistributorStatus()
        if (isDistributor) {
            getArticleData()
        }
    }
})

onShow(() => {
    uni.setNavigationBarTitle({ title: '文章推广' })
})
</script>

<style lang="scss" scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
