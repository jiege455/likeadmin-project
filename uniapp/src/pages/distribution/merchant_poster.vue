<template>
    <uni-nav title="商家推广"></uni-nav>

    <view class="merchant-poster bg-gray-100 min-h-screen p-4">
        <view
            class="poster-container relative bg-white shadow-lg overflow-hidden rounded-2xl mx-auto"
            :style="{ width: '650rpx' }"
        >
            <!-- 顶部背景 -->
            <view
                class="h-[240rpx] flex items-center justify-center relative overflow-hidden"
                :style="{
                    background: `linear-gradient(135deg, ${themeStore.primaryColor}, ${themeStore.primaryColor}60)`
                }"
            >
                <view class="absolute inset-0 opacity-20">
                    <view class="absolute top-4 right-4 w-32 h-32 rounded-full bg-white/30"></view>
                    <view
                        class="absolute bottom-4 left-4 w-24 h-24 rounded-full bg-white/20"
                    ></view>
                </view>
                <view class="text-center text-white relative z-10">
                    <u-image
                        :src="
                            merchantData.logo || merchantData.image || '/static/images/default.png'
                        "
                        width="100rpx"
                        height="100rpx"
                        shape="circle"
                        class="mx-auto mb-3 border-4 border-white/50"
                    ></u-image>
                    <view class="text-xl font-bold">{{ merchantData.name || '商家名称' }}</view>
                </view>
            </view>

            <!-- 商家信息 -->
            <view class="p-6">
                <view class="text-center mb-4">
                    <view class="text-gray-800 font-bold text-lg mb-1">{{
                        merchantData.name || '专业预测分析'
                    }}</view>
                    <view class="text-gray-500 text-sm">{{
                        merchantData.desc || merchantData.intro || '专注彩票分析，助您中奖'
                    }}</view>
                </view>

                <!-- 二维码区域 -->
                <view class="flex flex-col items-center py-4">
                    <view class="p-3 bg-gray-50 rounded-xl">
                        <u-qrcode
                            v-if="qrCodeUrl"
                            :value="qrCodeUrl"
                            :size="160"
                            canvas-id="merchant-qrcode"
                        />
                        <u-loading
                            v-else
                            mode="circle"
                            :color="themeStore.primaryColor"
                            size="40"
                        ></u-loading>
                    </view>
                    <view class="text-xs text-gray-400 mt-3">扫码关注商家</view>
                </view>
            </view>
        </view>

        <!-- 底部操作按钮 - 在海报容器外面 -->
        <view class="mt-4 space-y-3 mx-auto" :style="{ width: '650rpx' }">
            <view
                class="h-11 rounded-full flex items-center justify-center text-white font-bold"
                :style="{ backgroundColor: themeStore.primaryColor }"
                @click="copyMerchantLink"
            >
                <u-icon name="link" size="18" color="#fff" class="mr-2"></u-icon>
                复制推广链接
            </view>
            <view
                class="h-11 rounded-full flex items-center justify-center bg-white font-bold shadow"
                :style="{ color: themeStore.primaryColor }"
                @click="savePoster"
            >
                <u-icon
                    name="download"
                    size="18"
                    :color="themeStore.primaryColor"
                    class="mr-2"
                ></u-icon>
                保存海报
            </view>
        </view>

        <!-- 推广说明 -->
        <view class="mt-4 px-4">
            <view class="bg-white rounded-xl p-4">
                <view class="font-bold text-gray-800 mb-2">推广说明</view>
                <view class="text-sm text-gray-500 space-y-1">
                    <view>1. 分享商家主页给好友，好友关注后您可获得佣金</view>
                    <view>2. 好友购买该商家的付费文章，您可获得额外奖励</view>
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
import { getApplyDetail } from '@/api/distribution'
import request from '@/utils/request'
import UQrcode from '@/components/u-qrcode/u-qrcode.vue'
import html2canvas from 'html2canvas'
import { safeNavigateBack } from '@/utils/util'

const themeStore = useThemeStore()
const userStore = useUserStore()

const merchantId = ref('')
const merchantData = ref<any>({})
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

const getMerchantData = async () => {
    if (!merchantId.value) return
    try {
        const res = await request.get({ url: '/merchant/info', data: { id: merchantId.value } })
        merchantData.value = res || {}
        generateQrCode()
    } catch (e) {
        console.error('获取商家信息失败', e)
        merchantData.value = {
            id: merchantId.value,
            name: '商家',
            desc: '专业预测分析'
        }
        generateQrCode()
    }
}

const generateQrCode = () => {
    const inviteCode = userStore.userInfo.sn || ''
    // #ifdef H5
    const baseUrl = window.location.origin
    qrCodeUrl.value = `${baseUrl}/#/packages/pages/merchant/home?id=${merchantId.value}&invite_code=${inviteCode}`
    // #endif
    // #ifndef H5
    qrCodeUrl.value = `/packages/pages/merchant/home?id=${merchantId.value}&invite_code=${inviteCode}`
    // #endif
}

const copyMerchantLink = () => {
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
            uni.hideLoading()
            uni.showToast({ title: '海报元素不存在', icon: 'none' })
            return
        }

        const canvas = await html2canvas(element, {
            useCORS: true,
            allowTaint: true,
            backgroundColor: '#ffffff',
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
    if (options.merchant_id) {
        merchantId.value = options.merchant_id
        const isDistributor = await checkDistributorStatus()
        if (isDistributor) {
            getMerchantData()
        }
    }
})

onShow(() => {
    uni.setNavigationBarTitle({ title: '商家推广' })
})
</script>

<style lang="scss" scoped></style>
