<template>
    <uni-nav title="商家中心"></uni-nav>

    <view class="merchant-center min-h-screen bg-f5">
        <!-- 未入驻提示 -->
        <view
            class="content-area bg-white mx-3 mt-3 rounded-xl p-6 relative z-10 shadow-sm text-center"
            v-if="merchantStatus === -1"
        >
            <u-icon name="info-circle" size="60" color="#999"></u-icon>
            <view class="text-lg font-bold mt-4 text-gray-800">您还不是商户</view>
            <view class="text-sm text-gray-500 mt-2">申请入驻后即可发布文章、管理优惠券</view>
            <view class="mt-6">
                <u-button
                    type="primary"
                    shape="circle"
                    @click="toApply"
                    :custom-style="{
                        backgroundColor: themeStore.primaryColor,
                        color: '#ffffff',
                        border: 'none'
                    }"
                    >申请入驻</u-button
                >
            </view>
        </view>

        <!-- 商户功能 -->
        <template v-else>
            <!-- 数据看板 -->
            <view class="content-area bg-white mx-3 mt-3 rounded-xl p-4 relative z-10 shadow-sm">
                <view class="grid grid-cols-4 gap-2 text-center">
                    <view>
                        <view class="text-xl font-bold text-red-500"
                            >¥{{ stats.today_income }}</view
                        >
                        <view class="text-xs text-gray-400 mt-1">今日收入</view>
                    </view>
                    <view>
                        <view class="text-xl font-bold text-green-500"
                            >¥{{ stats.total_income }}</view
                        >
                        <view class="text-xs text-gray-400 mt-1">累计收入</view>
                    </view>
                    <view>
                        <view class="text-xl font-bold text-blue-500">{{ stats.order_count }}</view>
                        <view class="text-xs text-gray-400 mt-1">订单数</view>
                    </view>
                    <view>
                        <view class="text-xl font-bold text-orange-500">{{
                            stats.fans_count
                        }}</view>
                        <view class="text-xs text-gray-400 mt-1">粉丝数</view>
                    </view>
                </view>
            </view>

            <!-- 功能入口 -->
            <view class="content-area bg-white mx-3 mt-3 rounded-xl p-4 relative z-10 shadow-sm">
                <view class="flex flex-wrap">
                    <view class="w-1/5 flex flex-col items-center py-3" @click="toFan">
                        <view
                            class="w-12 h-12 rounded-full flex items-center justify-center"
                            :style="{ backgroundColor: themeStore.primaryColor + '20' }"
                        >
                            <u-icon
                                name="man-add"
                                size="24"
                                :color="themeStore.primaryColor"
                            ></u-icon>
                        </view>
                        <text class="text-xs mt-2 text-gray-700">粉丝管理</text>
                    </view>
                    <view class="w-1/5 flex flex-col items-center py-3" @click="toCustomer">
                        <view
                            class="w-12 h-12 rounded-full flex items-center justify-center"
                            :style="{ backgroundColor: themeStore.primaryColor + '20' }"
                        >
                            <u-icon
                                name="account"
                                size="24"
                                :color="themeStore.primaryColor"
                            ></u-icon>
                        </view>
                        <text class="text-xs mt-2 text-gray-700">客户管理</text>
                    </view>
                    <view class="w-1/5 flex flex-col items-center py-3" @click="toOrder">
                        <view
                            class="w-12 h-12 rounded-full flex items-center justify-center"
                            :style="{ backgroundColor: themeStore.primaryColor + '20' }"
                        >
                            <u-icon
                                name="order"
                                size="24"
                                :color="themeStore.primaryColor"
                            ></u-icon>
                        </view>
                        <text class="text-xs mt-2 text-gray-700">订单管理</text>
                    </view>
                    <view class="w-1/5 flex flex-col items-center py-3" @click="toCoupon">
                        <view
                            class="w-12 h-12 rounded-full flex items-center justify-center"
                            :style="{ backgroundColor: themeStore.primaryColor + '20' }"
                        >
                            <u-icon
                                name="coupon"
                                size="24"
                                :color="themeStore.primaryColor"
                            ></u-icon>
                        </view>
                        <text class="text-xs mt-2 text-gray-700">优惠券</text>
                    </view>
                    <view class="w-1/5 flex flex-col items-center py-3" @click="toSetting">
                        <view
                            class="w-12 h-12 rounded-full flex items-center justify-center"
                            :style="{ backgroundColor: themeStore.primaryColor + '20' }"
                        >
                            <u-icon
                                name="setting"
                                size="24"
                                :color="themeStore.primaryColor"
                            ></u-icon>
                        </view>
                        <text class="text-xs mt-2 text-gray-700">店铺设置</text>
                    </view>
                    <view class="w-1/5 flex flex-col items-center py-3" @click="toWithdraw">
                        <view
                            class="w-12 h-12 rounded-full flex items-center justify-center"
                            :style="{ backgroundColor: themeStore.primaryColor + '20' }"
                        >
                            <u-icon
                                name="rmb-circle"
                                size="24"
                                :color="themeStore.primaryColor"
                            ></u-icon>
                        </view>
                        <text class="text-xs mt-2 text-gray-700">提现</text>
                    </view>
                    <view class="w-1/5 flex flex-col items-center py-3" @click="toWithdrawLog">
                        <view
                            class="w-12 h-12 rounded-full flex items-center justify-center"
                            :style="{ backgroundColor: themeStore.primaryColor + '20' }"
                        >
                            <u-icon name="list" size="24" :color="themeStore.primaryColor"></u-icon>
                        </view>
                        <text class="text-xs mt-2 text-gray-700">提现记录</text>
                    </view>
                    <view
                        class="w-1/5 flex flex-col items-center py-3"
                        @click="toDistributionSetting"
                    >
                        <view
                            class="w-12 h-12 rounded-full flex items-center justify-center"
                            :style="{ backgroundColor: themeStore.primaryColor + '20' }"
                        >
                            <u-icon
                                name="share"
                                size="24"
                                :color="themeStore.primaryColor"
                            ></u-icon>
                        </view>
                        <text class="text-xs mt-2 text-gray-700">分销设置</text>
                    </view>
                    <view class="w-1/5 flex flex-col items-center py-3" @click="toMessage">
                        <view
                            class="w-12 h-12 rounded-full flex items-center justify-center relative"
                            :style="{ backgroundColor: themeStore.primaryColor + '20' }"
                        >
                            <u-icon name="chat" size="24" :color="themeStore.primaryColor"></u-icon>
                            <view
                                class="absolute -top-1 -right-1 min-w-[18px] h-[18px] rounded-full bg-red-500 text-white text-xs flex items-center justify-center px-1"
                                v-if="unreadCount > 0"
                            >
                                {{ unreadCount > 99 ? '99+' : unreadCount }}
                            </view>
                        </view>
                        <text class="text-xs mt-2 text-gray-700">消息</text>
                    </view>
                </view>
            </view>

            <!-- 文章列表 -->
            <view class="list p-3">
                <view class="text-sm text-gray-500 mb-2">我的文章</view>
                <view
                    class="item bg-white p-3 rounded-xl mb-3"
                    v-for="(item, index) in list"
                    :key="index"
                >
                    <view class="flex">
                        <view class="flex-1 flex flex-col justify-between min-w-0">
                            <view
                                class="text-base font-bold line-clamp-1"
                                @click="previewArticle(item)"
                                >{{ item.title }}</view
                            >
                            <view class="flex justify-between items-center text-xs text-gray-400">
                                <view class="flex items-center gap-2 flex-wrap">
                                    <text
                                        v-if="item.cate_name"
                                        class="px-2 py-0.5 bg-gray-100 rounded"
                                        >{{ item.cate_name }}</text
                                    >
                                    <text
                                        v-for="(tag, tagIndex) in getTags(item.tags)"
                                        :key="tagIndex"
                                        class="px-2 py-0.5 rounded"
                                        :style="{ backgroundColor: themeStore.primaryColor + '20', color: themeStore.primaryColor }"
                                        >{{ tag }}</text
                                    >
                                    <text
                                        :class="
                                            item.price > 0
                                                ? 'text-red-500 font-bold'
                                                : 'text-green-500'
                                        "
                                        >{{ item.price > 0 ? '¥' + item.price : '免费' }}</text
                                    >
                                </view>
                                <text class="text-gray-400">{{ item.create_time }}</text>
                            </view>
                            <view class="flex justify-end mt-2 gap-2">
                                <u-button
                                    size="mini"
                                    plain
                                    @click="previewArticle(item)"
                                    :custom-style="{
                                        color: themeStore.primaryColor,
                                        borderColor: themeStore.primaryColor
                                    }"
                                    >预览</u-button
                                >
                                <u-button size="mini" type="error" plain @click="handleDel(item.id)"
                                    >删除</u-button
                                >
                                <u-button
                                    size="mini"
                                    type="primary"
                                    plain
                                    @click="toEdit(item)"
                                    :custom-style="{
                                        color: themeStore.primaryColor,
                                        borderColor: themeStore.primaryColor
                                    }"
                                    >编辑</u-button
                                >
                            </view>
                        </view>
                    </view>
                </view>
                <u-empty v-if="list.length === 0" mode="list" text="暂无文章"></u-empty>
            </view>

            <!-- 悬浮发布按钮 -->
            <view
                class="fixed right-4 bottom-20 w-14 h-14 rounded-full flex items-center justify-center shadow-lg z-50"
                :style="{ backgroundColor: themeStore.primaryColor }"
                @click="toAdd"
            >
                <u-icon name="plus" size="28" color="#ffffff"></u-icon>
            </view>
        </template>

    </view>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import request from '@/utils/request'
import { onShow } from '@dcloudio/uni-app'
import { useThemeStore } from '@/stores/theme'
import { getUnreadTotal } from '@/api/chat'
import { safeNavigateBack } from '@/utils/util'

const themeStore = useThemeStore()
const themeStyle = computed(() => `--color-primary: ${themeStore.primaryColor};`)

const list = ref<any[]>([])
const merchantStatus = ref<number>(-2)
const unreadCount = ref(0)
interface MerchantStats {
    today_income: number | string
    total_income: number | string
    order_count: number
    fans_count: number
}

const stats = ref<MerchantStats>({
    today_income: 0,
    total_income: 0,
    order_count: 0,
    fans_count: 0
})

const getStatusClass = (status: number) => {
    const classes: Record<number, string> = {
        1: 'bg-green-100 text-green-600',
        2: 'bg-red-100 text-red-600'
    }
    return classes[status] || 'bg-orange-100 text-orange-600'
}

const getStatusText = (status: number) => {
    const texts: Record<number, string> = {
        1: '已通过',
        2: '已拒绝'
    }
    return texts[status] || '审核中'
}

const previewArticle = (item: any) => {
    uni.navigateTo({ url: `/pages/news_detail/news_detail?id=${item.id}` })
}

const getList = async () => {
    try {
        const res = await request.get({ url: '/merchant.article/lists' })
        list.value = res.lists || []
        merchantStatus.value = 1
    } catch (e: any) {
        merchantStatus.value = -1
    }
}

const getStats = async () => {
    try {
        const res = await request.get({ url: '/merchant.statistics/index' })
        stats.value = res
    } catch (e) {}
}

const toApply = () => {
    uni.navigateTo({ url: '/packages/pages/merchant_apply/merchant_apply' })
}

const goBack = () => {
    safeNavigateBack({ defaultUrl: '/pages/index/index' })
}

const toAdd = () => {
    uni.navigateTo({ url: '/packages/pages/merchant/article_add' })
}

const toFan = () => {
    uni.navigateTo({ url: '/packages/pages/merchant/fan' })
}

const toCustomer = () => {
    uni.navigateTo({ url: '/packages/pages/merchant/customer' })
}

const toOrder = () => {
    uni.navigateTo({ url: '/packages/pages/merchant/order' })
}

const toWithdraw = () => {
    uni.navigateTo({ url: '/packages/pages/merchant/withdraw' })
}

const toWithdrawLog = () => {
    uni.navigateTo({ url: '/packages/pages/merchant/withdraw_log' })
}

const toCoupon = () => {
    uni.navigateTo({ url: '/packages/pages/merchant/coupon/list' })
}

const toSetting = () => {
    uni.navigateTo({ url: '/packages/pages/merchant/info' })
}

const toDistributionSetting = () => {
    uni.navigateTo({ url: '/packages/pages/merchant/distribution_setting' })
}

const toMessage = () => {
    uni.navigateTo({ url: '/pages/message/conversation' })
}

const getUnreadCount = async () => {
    try {
        const res = await getUnreadTotal()
        unreadCount.value = res?.total || 0
    } catch (e) {
        console.error('获取未读消息数失败:', e)
    }
}

const toEdit = (item: any) => {
    uni.navigateTo({
        url: `/packages/pages/merchant/article_add?id=${item.id}&title=${item.title}&price=${
            item.price
        }&content=${encodeURIComponent(item.content)}`
    })
}

const handleDel = async (id: number) => {
    uni.showModal({
        title: '提示',
        content: '确定要删除吗？',
        success: async (res) => {
            if (res.confirm) {
                await request.post({ url: '/merchant.article/delete', data: { id } })
                getList()
            }
        }
    })
}

const getTags = (tags: string) => {
    if (!tags) return []
    return tags.split(',').filter(tag => tag.trim())
}

onShow(() => {
    uni.setNavigationBarTitle({ title: '商家中心' })
    getList()
    getStats()
    getUnreadCount()
    themeStore.getTheme()
})
</script>

<style lang="scss" scoped>
.merchant-center {
    background-color: #f5f5f5;
}
.bg-f5 {
    background-color: #f5f5f5;
}
</style>
