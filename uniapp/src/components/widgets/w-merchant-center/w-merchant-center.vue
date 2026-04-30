<!--
  开发者公众号：杰哥网络科技。
  qq2711793818 杰哥
  功能说明：商家中心入口组件，根据用户商家状态显示不同内容
-->
<template>
    <view
        class="w-merchant-center"
        :style="{
            marginTop: `${styles.margin_top || 0}px`,
            marginBottom: `${styles.margin_bottom || 10}px`,
            marginLeft: `${styles.padding_horizontal || 12}px`,
            marginRight: `${styles.padding_horizontal || 12}px`,
            borderRadius: `${styles.border_radius || 12}px`
        }"
    >
        <!-- 加载中状态 -->
        <view v-if="!initialized" class="loading-state bg-white rounded-xl p-4">
            <view class="flex items-center justify-center py-4">
                <u-loading mode="circle" size="32"></u-loading>
                <text class="ml-2 text-gray-400">加载中...</text>
            </view>
        </view>
        <!-- 未登录状态 -->
        <view v-else-if="!isLogin" class="not-login bg-white rounded-xl p-4" @click="handleLogin">
            <view class="flex items-center justify-between">
                <view class="flex items-center">
                    <view
                        class="icon-box w-12 h-12 rounded-full flex items-center justify-center"
                        :style="{ backgroundColor: primaryColor + '20' }"
                    >
                        <u-icon name="shop" size="24" :color="primaryColor"></u-icon>
                    </view>
                    <view class="ml-3">
                        <view class="text-base font-bold text-gray-800">商家中心</view>
                        <view class="text-xs text-gray-400 mt-1">登录后查看商家功能</view>
                    </view>
                </view>
                <u-icon name="arrow-right" size="16" color="#999"></u-icon>
            </view>
        </view>

        <!-- 已登录但未申请 -->
        <view
            v-else-if="merchantStatus === 0"
            class="not-apply bg-white rounded-xl overflow-hidden"
        >
            <view
                class="header px-4 py-3 flex items-center justify-between"
                :style="{ backgroundColor: primaryColor }"
            >
                <view class="flex items-center">
                    <u-icon name="shop" size="20" color="#fff"></u-icon>
                    <text class="text-white font-bold ml-2">成为商家</text>
                </view>
                <view
                    class="apply-btn px-3 py-1 bg-white rounded-full text-xs"
                    :style="{ color: primaryColor }"
                    @click="toApply"
                >
                    立即申请
                </view>
            </view>
            <view class="content p-4">
                <view class="grid grid-cols-4 gap-2 text-center">
                    <view class="flex flex-col items-center">
                        <u-icon name="edit" size="24" :color="primaryColor"></u-icon>
                        <text class="text-xs text-gray-500 mt-1">发布文章</text>
                    </view>
                    <view class="flex flex-col items-center">
                        <u-icon name="coupon" size="24" :color="primaryColor"></u-icon>
                        <text class="text-xs text-gray-500 mt-1">优惠券</text>
                    </view>
                    <view class="flex flex-col items-center">
                        <u-icon name="account" size="24" :color="primaryColor"></u-icon>
                        <text class="text-xs text-gray-500 mt-1">粉丝管理</text>
                    </view>
                    <view class="flex flex-col items-center">
                        <u-icon name="rmb-circle" size="24" :color="primaryColor"></u-icon>
                        <text class="text-xs text-gray-500 mt-1">收益提现</text>
                    </view>
                </view>
            </view>
        </view>

        <!-- 审核中 -->
        <view v-else-if="merchantStatus === 1" class="pending bg-white rounded-xl p-4">
            <view class="flex items-center">
                <view
                    class="icon-box w-12 h-12 rounded-full flex items-center justify-center bg-orange-100"
                >
                    <u-icon name="clock" size="24" color="#f97316"></u-icon>
                </view>
                <view class="ml-3 flex-1">
                    <view class="text-base font-bold text-gray-800">商家申请审核中</view>
                    <view class="text-xs text-gray-400 mt-1">请耐心等待管理员审核</view>
                </view>
                <view class="px-3 py-1 bg-orange-100 rounded-full text-xs text-orange-500"
                    >审核中</view
                >
            </view>
        </view>

        <!-- 审核被拒绝 -->
        <view v-else-if="merchantStatus === 2" class="rejected bg-white rounded-xl p-4">
            <view class="flex items-center">
                <view
                    class="icon-box w-12 h-12 rounded-full flex items-center justify-center bg-red-100"
                >
                    <u-icon name="close-circle" size="24" color="#ef4444"></u-icon>
                </view>
                <view class="ml-3 flex-1">
                    <view class="text-base font-bold text-gray-800">申请被拒绝</view>
                    <view class="text-xs text-red-400 mt-1">{{
                        rejectReason || '请重新提交申请'
                    }}</view>
                </view>
                <view
                    class="px-3 py-1 rounded-full text-xs text-white"
                    :style="{ backgroundColor: primaryColor }"
                    @click="toApply"
                >
                    重新申请
                </view>
            </view>
        </view>

        <!-- 已通过审核 - 商家中心入口 -->
        <view v-else-if="merchantStatus === 3" class="approved bg-white rounded-xl overflow-hidden">
            <view
                class="header px-4 py-3 flex items-center justify-between"
                :style="{ backgroundColor: primaryColor }"
                @click="toMerchantCenter"
            >
                <view class="flex items-center">
                    <image
                        v-if="merchantInfo.logo"
                        :src="merchantInfo.logo"
                        class="w-8 h-8 rounded-full"
                        mode="aspectFill"
                    ></image>
                    <u-icon v-else name="shop" size="20" color="#fff"></u-icon>
                    <text class="text-white font-bold ml-2">{{
                        merchantInfo.name || '商家中心'
                    }}</text>
                </view>
                <u-icon name="arrow-right" size="16" color="#fff"></u-icon>
            </view>
            <view class="content p-4" @click="toMerchantCenter">
                <view class="grid grid-cols-4 gap-2 text-center">
                    <view class="flex flex-col items-center">
                        <view class="text-lg font-bold text-red-500"
                            >¥{{ merchantInfo.money || '0.00' }}</view
                        >
                        <text class="text-xs text-gray-400 mt-1">账户余额</text>
                    </view>
                    <view class="flex flex-col items-center">
                        <view class="text-lg font-bold text-blue-500">{{
                            stats.order_count || 0
                        }}</view>
                        <text class="text-xs text-gray-400 mt-1">订单数</text>
                    </view>
                    <view class="flex flex-col items-center">
                        <view class="text-lg font-bold text-green-500">{{
                            stats.fans_count || 0
                        }}</view>
                        <text class="text-xs text-gray-400 mt-1">粉丝数</text>
                    </view>
                    <view class="flex flex-col items-center">
                        <view class="text-lg font-bold text-orange-500">{{
                            stats.article_count || 0
                        }}</view>
                        <text class="text-xs text-gray-400 mt-1">文章数</text>
                    </view>
                </view>
            </view>
            <view class="quick-actions flex border-t border-gray-100">
                <view class="flex-1 py-3 flex items-center justify-center" @click="toArticleAdd">
                    <u-icon name="edit" size="18" :color="primaryColor"></u-icon>
                    <text class="text-xs text-gray-600 ml-1">发布文章</text>
                </view>
                <view
                    class="flex-1 py-3 flex items-center justify-center border-l border-r border-gray-100"
                    @click="toOrder"
                >
                    <u-icon name="order" size="18" :color="primaryColor"></u-icon>
                    <text class="text-xs text-gray-600 ml-1">订单管理</text>
                </view>
                <view class="flex-1 py-3 flex items-center justify-center" @click="toWithdraw">
                    <u-icon name="rmb-circle" size="18" :color="primaryColor"></u-icon>
                    <text class="text-xs text-gray-600 ml-1">提现</text>
                </view>
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { onShow } from '@dcloudio/uni-app'
import { useUserStore } from '@/stores/user'
import { useThemeStore } from '@/stores/theme'
import { getApplyDetail, getMerchantInfo, getMerchantStatistics } from '@/api/merchant'

const userStore = useUserStore()
const themeStore = useThemeStore()

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

const isLogin = computed(() => userStore.isLogin)
const primaryColor = computed(() => themeStore.primaryColor || '#EF4444')

const initialized = ref(false)
const merchantStatus = ref(0)
const rejectReason = ref('')
const merchantInfo = ref<any>({})
const stats = ref({
    order_count: 0,
    fans_count: 0,
    article_count: 0
})

const checkMerchantStatus = async () => {
    if (!isLogin.value) {
        initialized.value = true
        return
    }

    try {
        const applyInfo = await getApplyDetail()

        if (!applyInfo || Object.keys(applyInfo).length === 0) {
            merchantStatus.value = 0
            initialized.value = true
            return
        }

        switch (applyInfo.status) {
            case 0:
                merchantStatus.value = 1
                break
            case 1:
                try {
                    const info = await getMerchantInfo()
                    merchantInfo.value = info || {}
                    merchantStatus.value = 3
                    getStats()
                } catch (e) {
                    merchantInfo.value = {}
                    merchantStatus.value = 3
                }
                break
            case 2:
                merchantStatus.value = 2
                rejectReason.value = applyInfo.audit_remark || ''
                break
            default:
                merchantStatus.value = 0
        }
    } catch (e) {
        merchantStatus.value = 0
    } finally {
        initialized.value = true
    }
}

const getStats = async () => {
    try {
        const res = await getMerchantStatistics()
        stats.value = {
            order_count: res.order_count || 0,
            fans_count: res.fans_count || 0,
            article_count: res.article_count || 0
        }
    } catch (e) {}
}

const handleLogin = () => {
    uni.navigateTo({ url: '/pages/login/login' })
}

const toApply = () => {
    uni.navigateTo({ url: '/packages/pages/merchant_apply/merchant_apply' })
}

const toMerchantCenter = () => {
    uni.navigateTo({ url: '/packages/pages/merchant/index' })
}

const toArticleAdd = () => {
    uni.navigateTo({ url: '/packages/pages/merchant/article_add' })
}

const toOrder = () => {
    uni.navigateTo({ url: '/packages/pages/merchant/order' })
}

const toWithdraw = () => {
    uni.navigateTo({ url: '/packages/pages/merchant/withdraw' })
}

onMounted(() => {
    if (isLogin.value) {
        checkMerchantStatus()
    } else {
        initialized.value = true
    }
})

watch(
    () => userStore.isLogin,
    (newVal) => {
        if (newVal) {
            initialized.value = false
            checkMerchantStatus()
        } else {
            merchantStatus.value = 0
            merchantInfo.value = {}
            initialized.value = true
        }
    }
)
</script>

<style lang="scss" scoped>
.w-merchant-center {
    .icon-box {
        flex-shrink: 0;
    }
}
</style>
