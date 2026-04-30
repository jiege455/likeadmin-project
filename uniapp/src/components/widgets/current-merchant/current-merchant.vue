<!--
  开发者公众号：杰哥网络科技。
  qq2711793818 杰哥
-->
<template>
    <view class="w-current-merchant">
        <!-- 加载中状态 - 避免闪烁 -->
        <view v-if="!initialized" class="loading-container">
            <view class="loading-content">
                <u-loading mode="circle" size="40"></u-loading>
                <text class="loading-text">加载中...</text>
            </view>
        </view>
        <!-- 无商家状态 -->
        <view v-else-if="!merchant || !merchant.id" class="no-merchant-container">
            <view class="no-merchant-empty">
                <u-empty
                    text="暂无关注的商家"
                    subText="关注商家后可查看最新动态和资料"
                    icon="heart"
                    iconSize="120"
                    :show="true"
                ></u-empty>
            </view>
            <view class="no-merchant-btns" :class="{ 'single-btn': isMerchant }">
                <view
                    v-if="!isMerchant"
                    class="no-merchant-btn apply-btn"
                    :style="{
                        color: themeStore.primaryColor,
                        borderColor: themeStore.primaryColor
                    }"
                    @click="handleApplyMerchant"
                >
                    <u-icon
                        name="plus-circle"
                        size="20"
                        :color="themeStore.primaryColor"
                    ></u-icon>
                    <text>申请做商家</text>
                </view>
                <view
                    class="no-merchant-btn follow-btn"
                    :style="{ backgroundColor: themeStore.primaryColor }"
                    @click="handleFollowMerchant"
                >
                    <u-icon name="heart" size="20" color="#fff"></u-icon>
                    <text>新增关注</text>
                </view>
            </view>
        </view>
        <view v-else class="has-merchant-container">
            <view
                class="header-bar flex justify-between items-center px-[30rpx]"
                :style="{
                    paddingTop: 'var(--status-bar-height)',
                    height: '88rpx',
                    color: '#fff',
                    background: themeStore.primaryColor
                }"
            >
                <view class="flex items-center">
                    <image
                        v-if="shopLogo"
                        :src="shopLogo"
                        class="w-[44rpx] h-[44rpx] rounded-lg mr-[12rpx]"
                        mode="aspectFill"
                    ></image>
                    <view class="font-bold text-lg">{{ shopName }}</view>
                </view>
                <view
                    class="text-xs bg-white/25 px-[24rpx] py-[12rpx] rounded-full"
                    @click="handleSwitch"
                >
                    切换商家
                </view>
            </view>
            <view class="merchant-header-bg" :style="{ background: themeStore.primaryColor }">
                <view class="mx-[20rpx] bg-white rounded-2xl p-[20rpx] shadow-xl">
                    <view class="flex items-start">
                        <view
                            class="w-[100rpx] h-[100rpx] rounded-full overflow-hidden shrink-0 border-4"
                            :style="{ borderColor: themeStore.primaryColor + '30' }"
                        >
                            <u-image
                                :src="merchant?.logo || merchant?.image || defaultAvatar"
                                width="100rpx"
                                height="100rpx"
                                shape="circle"
                            ></u-image>
                        </view>
                        <view class="ml-[20rpx] flex-1">
                            <view class="font-bold text-xl mb-[6rpx] text-gray-800">{{
                                merchant?.name || '商家名称'
                            }}</view>
                            <view
                                v-if="merchant?.distribution_switch == 1"
                                class="flex items-center mb-[6rpx]"
                            >
                                <view
                                    class="text-white text-xs px-[12rpx] py-[4rpx] rounded-full"
                                    :style="{ backgroundColor: themeStore.primaryColor }"
                                >
                                    推广员{{ merchant?.ratio ?? 0 }}%分成
                                </view>
                            </view>
                            <view class="text-sm text-gray-500 line-clamp-2">
                                {{ merchant?.desc || merchant?.intro || '暂无简介' }}
                            </view>
                        </view>
                    </view>
                </view>
                <view class="flex items-center justify-between px-[16rpx] py-[16rpx] mt-[4rpx]">
                    <view
                        class="flex items-center action-item flex-1 whitespace-nowrap justify-center"
                        @click="handleCopy(merchant?.wechat || '')"
                    >
                        <view
                            class="w-[48rpx] h-[48rpx] rounded-full flex items-center justify-center shrink-0"
                            :style="{ backgroundColor: themeStore.primaryColor + '30' }"
                        >
                            <u-icon name="weixin-fill" color="#fff" size="24"></u-icon>
                        </view>
                        <text class="text-white text-sm font-medium ml-[8rpx]">商家微信</text>
                    </view>
                    <view class="w-[2rpx] h-[36rpx] bg-white mx-[6rpx] shrink-0"></view>
                    <view
                        v-if="merchant?.distribution_switch == 1"
                        class="flex items-center action-item flex-1 whitespace-nowrap justify-center"
                        @click="handleShare"
                    >
                        <view
                            class="w-[48rpx] h-[48rpx] rounded-full flex items-center justify-center shrink-0"
                            :style="{ backgroundColor: themeStore.primaryColor + '30' }"
                        >
                            <u-icon name="thumb-up-fill" color="#fff" size="24"></u-icon>
                        </view>
                        <text class="text-white text-sm font-medium ml-[8rpx]">推广TA</text>
                    </view>
                    <view class="w-[2rpx] h-[36rpx] bg-white mx-[6rpx] shrink-0"></view>
                    <view class="flex items-center action-item flex-1 whitespace-nowrap justify-center" @click="handleChat">
                        <view
                            class="w-[48rpx] h-[48rpx] rounded-full flex items-center justify-center shrink-0"
                            :style="{ backgroundColor: themeStore.primaryColor + '30' }"
                        >
                            <u-icon name="chat-fill" color="#fff" size="24"></u-icon>
                        </view>
                        <text class="text-white text-sm font-medium ml-[8rpx]">私聊</text>
                    </view>
                    <view class="w-[2rpx] h-[36rpx] bg-white mx-[6rpx] shrink-0"></view>
                    <view class="flex items-center action-item flex-1 whitespace-nowrap justify-center" @click="handleComplain">
                        <view
                            class="w-[48rpx] h-[48rpx] rounded-full flex items-center justify-center shrink-0"
                            :style="{ backgroundColor: themeStore.primaryColor + '30' }"
                        >
                            <u-icon name="kefu-ermai" color="#fff" size="24"></u-icon>
                        </view>
                        <text class="text-white text-sm font-medium ml-[8rpx]">投诉反馈</text>
                    </view>
                </view>
            </view>
            <view class="bg-white flex border-b border-gray-100">
                <view
                    v-for="(tab, index) in tabs"
                    :key="index"
                    class="flex-1 py-[32rpx] text-center"
                    :class="{ active: currentTab === index }"
                    @click="currentTab = index"
                >
                    <text
                        :class="
                            currentTab === index ? 'font-bold text-base' : 'text-gray-500 text-base'
                        "
                        :style="{ color: currentTab === index ? themeStore.primaryColor : '#666' }"
                        >{{ tab.name }}</text
                    >
                    <view
                        v-if="currentTab === index"
                        class="w-[48rpx] h-[6rpx] rounded-full mx-auto mt-[12rpx]"
                        :style="{ backgroundColor: themeStore.primaryColor }"
                    ></view>
                </view>
            </view>

            <view
                v-if="currentTab === 0"
                class="bg-white px-[24rpx] py-[24rpx] flex items-center gap-[20rpx]"
            >
                <view
                    class="flex items-center text-gray-500 text-sm bg-gray-100 px-[20rpx] py-[16rpx] rounded-full"
                    @click="showFilter = true"
                >
                    <u-icon name="filter" size="18" color="#666"></u-icon>
                    <text class="ml-[8rpx] font-medium">筛选</text>
                </view>
                <view
                    class="flex-1 bg-gray-100 rounded-full px-[24rpx] py-[12rpx] flex items-center"
                >
                    <u-icon name="search" size="18" color="#999"></u-icon>
                    <input
                        class="flex-1 text-sm ml-[10rpx] bg-transparent outline-none"
                        placeholder="请输入关键词搜索资料"
                        v-model="searchKeyword"
                        confirm-type="search"
                        @confirm="handleSearch"
                        @input="handleSearchInput"
                    />
                    <u-icon
                        v-if="searchKeyword"
                        name="close-circle-fill"
                        size="16"
                        color="#ccc"
                        @click="clearSearch"
                    ></u-icon>
                </view>
            </view>

            <view class="bg-gray-50">
                <WMerchantContentList
                    v-if="currentTab === 0"
                    ref="contentListRef"
                    :content="{}"
                    :styles="{}"
                    :merchantId="merchant?.id"
                    :keyword="searchKeyword"
                    :filterParams="currentFilter"
                />
                <WMerchantCouponList v-else-if="currentTab === 1" :merchantId="merchant?.id" />
            </view>
        </view>
        <MerchantSwitcher
            v-model="showSwitcher"
            :currentMerchantId="merchant?.id || 0"
            @change="handleMerchantSwitch"
        />

        <u-popup
            :show="showFilter"
            mode="right"
            :customStyle="{ width: '80%', height: '100%' }"
            @close="showFilter = false"
        >
            <view class="filter-popup p-4">
                <view class="flex justify-between items-center mb-4 pb-3 border-b border-gray-100">
                    <text class="font-bold text-lg">筛选条件</text>
                    <u-icon name="close" size="20" @click="showFilter = false"></u-icon>
                </view>

                <view class="mb-4">
                    <text class="font-bold text-base mb-2 block">内容类型</text>
                    <view class="flex flex-wrap gap-2">
                        <view
                            v-for="(item, index) in filterTypes"
                            :key="index"
                            class="px-4 py-2 rounded-full text-sm"
                            :class="
                                tempFilter.type === item.value
                                    ? 'text-white'
                                    : 'bg-gray-100 text-gray-600'
                            "
                            :style="
                                tempFilter.type === item.value
                                    ? { backgroundColor: themeStore.primaryColor }
                                    : {}
                            "
                            @click="tempFilter.type = item.value"
                        >
                            {{ item.label }}
                        </view>
                    </view>
                </view>

                <view class="mb-4">
                    <text class="font-bold text-base mb-2 block">价格区间</text>
                    <view class="flex flex-wrap gap-2">
                        <view
                            v-for="(item, index) in filterPrices"
                            :key="index"
                            class="px-4 py-2 rounded-full text-sm"
                            :class="
                                tempFilter.price === item.value
                                    ? 'text-white'
                                    : 'bg-gray-100 text-gray-600'
                            "
                            :style="
                                tempFilter.price === item.value
                                    ? { backgroundColor: themeStore.primaryColor }
                                    : {}
                            "
                            @click="tempFilter.price = item.value"
                        >
                            {{ item.label }}
                        </view>
                    </view>
                </view>

                <view class="flex gap-3 mt-6">
                    <view
                        class="flex-1 py-3 text-center rounded-full border border-gray-300 text-gray-600"
                        @click="resetFilter"
                    >
                        重置
                    </view>
                    <view
                        class="flex-1 py-3 text-center rounded-full text-white"
                        :style="{ backgroundColor: themeStore.primaryColor }"
                        @click="applyFilter"
                    >
                        确定
                    </view>
                </view>
            </view>
        </u-popup>
    </view>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { getCurrentMerchant, getApplyDetail, getMerchantInfo } from '@/api/merchant'
import { useUserStore } from '@/stores/user'
import { useThemeStore } from '@/stores/theme'
import { useAppStore } from '@/stores/app'
import MerchantSwitcher from '@/components/business/MerchantSwitcher.vue'
import WMerchantContentList from '@/components/widgets/w-merchant-content-list/w-merchant-content-list.vue'
import WMerchantCouponList from '@/components/widgets/w-merchant-coupon-list/w-merchant-coupon-list.vue'

const defaultAvatar = '/static/images/user/default_avatar.png'

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

const emit = defineEmits(['change'])

const userStore = useUserStore()
const themeStore = useThemeStore()
const appStore = useAppStore()
const merchant = ref<any>(null)
const showSwitcher = ref(false)
const loading = ref(false)
const initialized = ref(false)
const currentTab = ref(0)
const contentListRef = ref(null)
const searchKeyword = ref('')
const showFilter = ref(false)
const searchTimer = ref<any>(null)

const currentFilter = ref({
    type: '',
    price: ''
})

const tempFilter = ref({
    type: '',
    price: ''
})

const tabs = ref([{ name: 'TA的料' }, { name: '优惠券' }])

const filterTypes = [
    { label: '全部', value: '' },
    { label: '免费', value: 'free' },
    { label: '付费', value: 'paid' }
]

const filterPrices = [
    { label: '全部', value: '' },
    { label: '10元以下', value: '0-10' },
    { label: '10-50元', value: '10-50' },
    { label: '50元以上', value: '50-' }
]

const isMerchant = ref(false)

const shopName = computed(() => {
    return appStore.getWebsiteConfig?.shop_name || ''
})

const shopLogo = computed(() => {
    return appStore.getImageUrl(appStore.getWebsiteConfig?.shop_logo) || ''
})

const checkMerchantStatus = async () => {
    if (!userStore.isLogin) {
        isMerchant.value = false
        return
    }

    try {
        const applyInfo = await getApplyDetail()

        if (!applyInfo || Object.keys(applyInfo).length === 0) {
            isMerchant.value = false
            return
        }

        if (applyInfo.status === 1) {
            try {
                await getMerchantInfo()
                isMerchant.value = true
            } catch (e) {
                isMerchant.value = false
            }
        } else {
            isMerchant.value = false
        }
    } catch (e) {
        isMerchant.value = false
    }
}

const loadMerchant = async () => {
    if (!userStore.isLogin) {
        merchant.value = null
        initialized.value = true
        return
    }

    loading.value = true
    try {
        const res = await getCurrentMerchant()
        merchant.value = res || null
        if (res && res.id) {
            emit('change', res)
        }
    } catch (e) {
        console.error('[current-merchant] 获取失败:', e)
        merchant.value = null
    } finally {
        loading.value = false
        initialized.value = true
    }
}

onMounted(() => {
    if (userStore.isLogin) {
        checkMerchantStatus()
        loadMerchant()
    } else {
        initialized.value = true
    }

    uni.$on('merchantFollowChanged', handleFollowChanged)
    uni.$on('refreshCurrentMerchant', handleRefreshMerchant)
})

onUnmounted(() => {
    uni.$off('merchantFollowChanged', handleFollowChanged)
    uni.$off('refreshCurrentMerchant', handleRefreshMerchant)
})

const handleFollowChanged = () => {
    initialized.value = false
    checkMerchantStatus()
    loadMerchant()
}

const handleRefreshMerchant = () => {
    initialized.value = false
    loadMerchant()
}

watch(
    () => userStore.isLogin,
    (val) => {
        if (val) {
            initialized.value = false
            checkMerchantStatus()
            loadMerchant()
        } else {
            merchant.value = null
            isMerchant.value = false
            initialized.value = true
        }
    }
)

const handleSwitch = () => {
    uni.navigateTo({
        url: '/packages/pages/follow/follow'
    })
}

const handleMerchantSwitch = (m: any) => {
    merchant.value = m
    showSwitcher.value = false
    uni.showToast({ title: '已切换商家', icon: 'success' })
    loadMerchant()
}

const handleCopy = (content: string) => {
    if (!content) {
        uni.showToast({ title: '暂无微信号', icon: 'none' })
        return
    }
    uni.setClipboardData({
        data: content,
        success: () => {
            uni.showToast({ title: '复制成功' })
        }
    })
}

const handleShare = () => {
    if (merchant.value && merchant.value.id) {
        uni.navigateTo({
            url: `/pages/distribution/merchant_poster?merchant_id=${merchant.value.id}`
        })
    }
}

const handleChat = () => {
    if (merchant.value && merchant.value.id) {
        uni.navigateTo({
            url: `/pages/business/chat-window?merchant_id=${merchant.value.id}&is_private=1`
        })
    }
}

const handleComplain = () => {
    if (merchant.value && merchant.value.id) {
        uni.navigateTo({
            url: `/pages/business/complaint?type=1&target_id=${
                merchant.value.id
            }&title=${encodeURIComponent(merchant.value.name || '商家')}`
        })
    }
}

const handleApplyMerchant = () => {
    uni.navigateTo({
        url: '/packages/pages/merchant_apply/merchant_apply'
    })
}

const handleFollowMerchant = () => {
    uni.navigateTo({
        url: '/packages/pages/follow/follow'
    })
}

const handleSearchInput = () => {
    if (searchTimer.value) {
        clearTimeout(searchTimer.value)
    }
    searchTimer.value = setTimeout(() => {
        triggerSearch()
    }, 500)
}

const handleSearch = () => {
    triggerSearch()
}

const triggerSearch = () => {
    if (contentListRef.value) {
        contentListRef.value.setKeyword(searchKeyword.value)
    }
}

const clearSearch = () => {
    searchKeyword.value = ''
    triggerSearch()
}

const resetFilter = () => {
    tempFilter.value = {
        type: '',
        price: ''
    }
}

const applyFilter = () => {
    // 创建一个新的对象，确保引用变化
    const newFilter = {
        type: tempFilter.value.type || '',
        price: tempFilter.value.price || ''
    }
    currentFilter.value = newFilter
    showFilter.value = false
    // 通知子组件更新筛选条件并重新加载数据
    if (contentListRef.value) {
        contentListRef.value.setFilter(newFilter)
    }
}

watch(showFilter, (val) => {
    if (val) {
        tempFilter.value = { ...currentFilter.value }
    }
})

defineExpose({
    refresh: loadMerchant,
    merchant
})
</script>

<style lang="scss" scoped>
.w-current-merchant {
    width: 100%;
}

.loading-container {
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 400rpx;
}

.loading-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60rpx 40rpx;
}

.loading-text {
    margin-top: 20rpx;
    font-size: 28rpx;
    color: #999;
}

.no-merchant-container {
    width: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    min-height: 400rpx;
}

.no-merchant-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60rpx 40rpx;
    width: 100%;
}

.no-merchant-empty {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    padding: 40rpx 0;
}

.no-merchant-icon {
    width: 160rpx;
    height: 160rpx;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 30rpx;
    background: linear-gradient(135deg, rgba(255, 45, 58, 0.1) 0%, rgba(255, 107, 107, 0.1) 100%);
}

.no-merchant-title {
    font-size: 34rpx;
    font-weight: bold;
    color: #333;
    margin-bottom: 16rpx;
}

.no-merchant-desc {
    font-size: 26rpx;
    color: #999;
    margin-bottom: 40rpx;
    text-align: center;
}

.no-merchant-btns {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 24rpx;
    width: 100%;
    max-width: 600rpx;

    &.single-btn {
        justify-content: center;
    }
}

.no-merchant-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10rpx;
    padding: 24rpx 40rpx;
    border-radius: 50rpx;
    font-size: 28rpx;
    font-weight: bold;
    box-shadow: 0 6rpx 20rpx rgba(0, 0, 0, 0.1);

    &.apply-btn {
        background: #fff;
        border: 2rpx solid;
        flex: 1;
    }

    &.follow-btn {
        color: #fff;
        flex: 1;
    }
}

.has-merchant-container {
    width: 100%;
    display: flex;
    flex-direction: column;
}

.header-bar {
    position: relative;
}

.merchant-header-bg {
    padding-bottom: 16rpx;
}

.action-item {
    transition: transform 0.2s;

    &:active {
        transform: scale(0.95);
    }
}

.active {
    position: relative;
}

.filter-popup {
    height: 100%;
    background: #fff;
}
</style>
