<!--
  推广海报组件
  开发者公众号：杰哥网络科技。
  qq2711793818 杰哥
-->
<template>
    <view class="w-distribution-poster bg-gray-100 p-4 flex justify-center min-h-[1000rpx]">
        <view
            class="poster-container relative bg-white shadow-lg overflow-hidden rounded-lg"
            :style="{ width: '600rpx', height: '1000rpx' }"
        >
            <view class="absolute inset-0">
                <image
                    v-if="content.bg_image"
                    :src="appStore.getImageUrl(content.bg_image)"
                    class="w-full h-full"
                    mode="aspectFill"
                />
                <view
                    v-else
                    class="w-full h-full"
                    :style="{
                        background:
                            'linear-gradient(135deg, ' +
                            (themeStore.primaryColor || '#4173ff') +
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
                <view
                    class="absolute top-1/3 right-0 w-40 h-40 bg-white/5 rounded-full blur-2xl"
                ></view>
            </view>

            <view class="absolute inset-0 flex flex-col z-10">
                <view class="flex-1 flex flex-col items-center justify-start pt-12 px-8">
                    <view class="flex items-center gap-3 mb-4">
                        <view
                            class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-lg"
                            :style="{ backgroundColor: themeStore.primaryColor || '#4173ff' }"
                        >
                            <text class="text-white text-2xl font-bold">
                                {{ (appStore.getWebsiteConfig.shop_name || '').charAt(0) }}
                            </text>
                        </view>
                        <view>
                            <text class="text-white text-2xl font-bold tracking-wide">
                                {{ appStore.getWebsiteConfig.shop_name || '' }}
                            </text>
                        </view>
                    </view>
                    <text class="text-white/80 text-center text-sm mb-2">
                        {{ appStore.getWebsiteConfig.slogan || '分享精彩，共创未来' }}
                    </text>
                    <view class="w-16 h-1 bg-white/30 rounded-full"></view>
                </view>

                <view class="flex-1 flex items-center justify-center px-6">
                    <view
                        class="bg-white/95 backdrop-blur-xl w-full rounded-[40rpx] shadow-2xl p-8 flex flex-col items-center relative overflow-hidden"
                    >
                        <view
                            class="absolute top-0 left-1/2 -translate-x-1/2 w-32 h-2 bg-gray-200 rounded-full mt-3"
                        ></view>

                        <view class="w-full text-center mb-6">
                            <text class="text-gray-800 text-lg font-semibold">扫码加入我们</text>
                            <text class="text-gray-500 text-sm block mt-1"
                                >立即注册，享受专属优惠</text
                            >
                        </view>

                        <view class="relative">
                            <view
                                class="absolute inset-0 rounded-3xl"
                                :style="{
                                    backgroundColor: themeStore.primaryColor || '#4173ff',
                                    opacity: 0.1
                                }"
                            ></view>
                            <view
                                class="relative p-6 bg-white rounded-3xl shadow-lg border-4"
                                :style="{ borderColor: themeStore.primaryColor || '#4173ff' }"
                            >
                                <u-qrcode
                                    v-if="inviteLink"
                                    :value="inviteLink"
                                    :size="180"
                                    canvas-id="poster-qrcode"
                                />
                                <view
                                    v-else
                                    class="w-[180px] h-[180px] flex items-center justify-center"
                                >
                                    <u-loading
                                        mode="circle"
                                        :color="themeStore.primaryColor || '#4173ff'"
                                        size="40"
                                    ></u-loading>
                                </view>
                            </view>
                        </view>

                        <view class="mt-6 flex items-center gap-2">
                            <view class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></view>
                            <text class="text-gray-600 text-sm">有效期内随时扫码</text>
                        </view>
                    </view>
                </view>

                <view class="flex flex-col items-center justify-end pb-10 px-8">
                    <view
                        v-if="!isSaving"
                        class="w-full h-14 rounded-full flex items-center justify-center shadow-xl active:scale-95 transition-all mb-4"
                        :style="{
                            background:
                                'linear-gradient(135deg, ' +
                                (themeStore.primaryColor || '#4173ff') +
                                ', #667eea)',
                            boxShadow: '0 10px 30px -10px ' + (themeStore.primaryColor || '#4173ff')
                        }"
                        @click="copyInviteLink"
                    >
                        <u-icon name="link" size="22" color="#fff" class="mr-2"></u-icon>
                        <text class="text-white font-bold text-lg">复制推广链接</text>
                    </view>

                    <view
                        v-if="!isSaving"
                        class="w-full h-14 rounded-full flex items-center justify-center shadow-xl active:scale-95 transition-all bg-white/20 backdrop-blur-md border-2 border-white/30"
                        @click="savePoster"
                    >
                        <u-icon name="download" size="22" color="#fff" class="mr-2"></u-icon>
                        <text class="text-white font-bold text-lg">保存海报</text>
                    </view>

                    <view class="mt-6 text-center">
                        <text class="text-white/60 text-xs tracking-widest uppercase"
                            >Powered by</text
                        >
                        <text class="text-white/90 text-sm font-medium block mt-1">
                            {{ appStore.getWebsiteConfig.shop_name || '' }}
                        </text>
                    </view>
                </view>
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
import { computed, ref, onMounted } from 'vue'
import { useAppStore } from '@/stores/app'
import { useUserStore } from '@/stores/user'
import { useThemeStore } from '@/stores/theme'
import { getDistributionPoster } from '@/api/distribution'
import UQrcode from '@/components/u-qrcode/u-qrcode.vue'
import html2canvas from 'html2canvas'

const appStore = useAppStore()
const userStore = useUserStore()
const themeStore = useThemeStore()
const userInfo = computed(() => userStore.userInfo)
const inviteLink = ref('')
const inviteCode = ref('')
const isSaving = ref(false)

const props = defineProps({
    content: {
        type: Object,
        default: () => ({})
    },
    styles: {
        type: Object,
        default: () => ({})
    }
})

const getPosterData = async () => {
    try {
        const res = await getDistributionPoster()
        inviteLink.value = res.invite_link
        inviteCode.value = res.invite_code
    } catch (e) {
        console.error(e)
    }
}

const copyInviteCode = () => {
    if (!inviteCode.value) return
    uni.setClipboardData({
        data: inviteCode.value,
        success: () => {
            uni.showToast({ title: '邀请码已复制', icon: 'none' })
        }
    })
}

const copyInviteLink = () => {
    if (!inviteLink.value) {
        uni.showToast({ title: '正在获取链接...', icon: 'none' })
        getPosterData()
        return
    }
    uni.setClipboardData({
        data: inviteLink.value,
        success: () => {
            uni.showToast({ title: '推广链接已复制', icon: 'none' })
        }
    })
}

const savePoster = async () => {
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
            uni.showToast({
                title: '请长按图片保存',
                icon: 'none',
                duration: 3000
            })
        }, 500)
    } catch (error) {
        console.error('生成海报失败:', error)
        isSaving.value = false
        uni.hideLoading()
        uni.showToast({ title: '生成海报失败，请截图保存', icon: 'none' })
    }
}

onMounted(() => {
    getPosterData()
})
</script>

<style lang="scss" scoped>
.w-distribution-poster {
    padding-top: 50rpx;
    padding-bottom: 50rpx;
}
</style>
