<template>
    <view class="follow-page" :style="themeStyle">
        <uni-nav title="我的关注"></uni-nav>

        <view class="search-box p-3 bg-white sticky top-0 z-10">
            <u-search
                v-model="keyword"
                placeholder="搜索您关注的商家"
                :show-action="false"
                @change="handleSearch"
                :bg-color="'#f2f2f2'"
            ></u-search>
        </view>

        <view class="list p-3" v-if="list.length > 0">
            <view
                class="item bg-white rounded-lg p-3 mb-3"
                v-for="(item, index) in list"
                :key="index"
            >
                <view class="flex items-start">
                    <u-image
                        width="100"
                        height="100"
                        :src="item.logo || item.image || '/static/images/user/default_avatar.png'"
                        border-radius="10"
                    ></u-image>
                    <view class="ml-3 flex-1">
                        <view class="flex justify-between items-center">
                            <view class="text-lg font-bold">{{ item.name }}</view>
                        </view>
                        <view class="text-gray-500 text-sm mt-2 line-clamp-2">
                            {{ item.desc || '暂无简介' }}
                        </view>
                    </view>
                </view>

                <view class="actions flex mt-3 pt-3 border-t border-gray-100 justify-end">
                    <u-button 
                        size="mini" 
                        shape="circle"
                        :custom-style="{ backgroundColor: '#ffffff', color: themeColor, border: '1px solid ' + themeColor }"
                        @click="handleOpenKeywordPopup(item)"
                    >推送关键词</u-button>
                    <u-button 
                        size="mini" 
                        shape="circle"
                        :custom-style="{ backgroundColor: '#ffffff', color: item.push_enable ? themeColor : '#999999', border: '1px solid ' + (item.push_enable ? themeColor : '#999999') }"
                        class="ml-2"
                        @click="handleTogglePush(item)"
                    >{{ item.push_enable ? '开启推送' : '关闭推送' }}</u-button>
                    <u-button 
                        size="mini" 
                        shape="circle"
                        :custom-style="{ backgroundColor: '#ffffff', color: themeColor, border: '1px solid ' + themeColor }"
                        class="ml-2"
                        @click="handleUnfollow(item.merchant_id)"
                    >取消关注</u-button>
                </view>
            </view>
        </view>

        <u-empty
            v-else
            text="您还没有关注商家"
            icon="heart"
            iconSize="120"
            :show="true"
        ></u-empty>

        <view class="fixed bottom-0 left-0 right-0 p-3 bg-white border-t safe-area-inset-bottom">
            <u-button
                type="primary"
                shape="circle"
                class="bg-primary text-white"
                @click="showAddPopup = true"
                :custom-style="{ backgroundColor: themeColor, color: '#ffffff', border: 'none' }"
                >新增关注</u-button
            >
        </view>

        <u-popup v-model="showAddPopup" mode="center" border-radius="20" width="600">
            <view class="popup-content p-4 bg-white">
                <view class="text-center font-bold text-lg mb-4">搜索商家-新增关注</view>

                <view class="search-box mb-4">
                    <u-search
                        v-model="searchKeyword"
                        placeholder="请输入您想关注的商家名称"
                        :show-action="false"
                        @search="handleSearchMerchant"
                        @change="handleSearchMerchant"
                    ></u-search>
                </view>

                <scroll-view scroll-y class="merchant-list" style="max-height: 300px">
                    <view v-if="searchList.length > 0">
                        <view
                            class="flex items-center justify-between p-2 border-b border-gray-100"
                            v-for="(item, index) in searchList"
                            :key="index"
                        >
                            <view class="flex items-center flex-1">
                                <u-image
                                    width="80"
                                    height="80"
                                    :src="
                                        item.logo ||
                                        item.image ||
                                        '/static/images/user/default_avatar.png'
                                    "
                                    border-radius="10"
                                ></u-image>
                                <view class="ml-2">
                                    <view class="font-bold text-sm">{{ item.name }}</view>
                                </view>
                            </view>
                            <u-button
                                size="mini"
                                shape="circle"
                                :type="item.is_follow ? 'default' : 'error'"
                                :custom-style="
                                    item.is_follow
                                        ? {}
                                        : {
                                              backgroundColor: themeColor,
                                              color: '#ffffff',
                                              border: 'none'
                                          }
                                "
                                @click="handleToggleFollow(item)"
                            >
                                {{ item.is_follow ? '已关注' : '关注' }}
                            </u-button>
                        </view>
                    </view>
                    <u-empty v-else text="暂无商家，请搜索" icon="search" iconSize="80" :show="true"></u-empty>
                </scroll-view>

                <view class="flex justify-between mt-4 pt-2">
                    <u-button class="flex-1 mr-2" shape="circle" @click="showAddPopup = false"
                        >取消</u-button
                    >
                    <u-button
                        class="flex-1 ml-2"
                        type="error"
                        shape="circle"
                        :custom-style="{
                            backgroundColor: themeColor,
                            color: '#ffffff',
                            border: 'none'
                        }"
                        @click="showAddPopup = false"
                        >关注</u-button
                    >
                </view>
            </view>
        </u-popup>

        <push-keyword-popup
            v-model="showKeywordPopup"
            :merchant-id="currentMerchant?.merchant_id || 0"
            :merchant-name="currentMerchant?.name || ''"
        />
    </view>
</template>

<script setup lang="ts">
/**
 * 我的关注页面
 * 开发者：杰哥网络科技 qq2711793818 杰哥
 */
import { ref, watch, computed } from 'vue'
import { getFollowLists, toggleFollow, getMerchantLists, togglePush } from '@/api/merchant'
import { getApplyDetail } from '@/api/distribution'
import { onShow } from '@dcloudio/uni-app'
import { useThemeStore } from '@/stores/theme'
import { useUserStore } from '@/stores/user'
import { safeNavigateBack } from '@/utils/util'
import PushKeywordPopup from '@/components/push-keyword-popup/push-keyword-popup.vue'

const themeStore = useThemeStore()
const userStore = useUserStore()
const themeColor = computed(() => themeStore.primaryColor)
const themeStyle = computed(() => themeStore.vars)
const navBgColor = computed(() => themeStore.navBgColor)
const navColor = computed(() => themeStore.navColor)

const list = ref<any[]>([])
const keyword = ref('')
const page = ref(1)
const finished = ref(false)

const showAddPopup = ref(false)
const searchKeyword = ref('')
const searchList = ref<any[]>([])

const showKeywordPopup = ref(false)
const currentMerchant = ref<any>(null)

const handleOpenKeywordPopup = (item: any) => {
    currentMerchant.value = item
    showKeywordPopup.value = true
}

const handleTogglePush = async (item: any) => {
    try {
        await togglePush({ merchant_id: item.merchant_id })
        item.push_enable = item.push_enable ? 0 : 1
        uni.showToast({ title: item.push_enable ? '已开启推送' : '已关闭推送', icon: 'none' })
    } catch (e: any) {
        const errMsg = typeof e === 'string' ? e : (e.msg || '操作失败')
        uni.showToast({ title: errMsg, icon: 'none' })
    }
}

const getList = async () => {
    try {
        const res = await getFollowLists({
            page_no: page.value,
            page_size: 20,
            keyword: keyword.value
        })
        if (page.value === 1) {
            list.value = res.lists
        } else {
            list.value = [...list.value, ...res.lists]
        }
        if (res.lists.length < 20) {
            finished.value = true
        }
    } catch (e) {
        // error
    }
}

const handleSearch = () => {
    page.value = 1
    getList()
}

const handleBack = () => {
    safeNavigateBack({ defaultUrl: '/pages/index/index' })
}

const handleUnfollow = async (id: number) => {
    uni.showModal({
        title: '提示',
        content: '确定取消关注吗？',
        success: async (res) => {
            if (res.confirm) {
                try {
                    await toggleFollow({ merchant_id: id })
                    uni.showToast({ title: '已取消', icon: 'none' })
                    page.value = 1
                    getList()
                    uni.$emit('merchantFollowChanged')
                } catch (e: any) {
                    uni.showToast({ title: e.msg || '操作失败', icon: 'none', duration: 3000 })
                }
            }
        }
    })
}

const handleSearchMerchant = async () => {
    if (!searchKeyword.value) {
        searchList.value = []
        return
    }
    try {
        const res = await getMerchantLists({
            page_no: 1,
            page_size: 20,
            keyword: searchKeyword.value
        })
        searchList.value = res.lists
    } catch (e) {
        // error
    }
}

const handleToggleFollow = async (item: any) => {
    try {
        await toggleFollow({ merchant_id: item.id })
        const wasFollowing = item.is_follow
        item.is_follow = item.is_follow ? 0 : 1
        uni.$emit('merchantFollowChanged')
        uni.showToast({
            title: item.is_follow ? '关注成功' : '已取消关注',
            icon: 'none'
        })

        if (!wasFollowing && item.is_follow && userStore.isLogin) {
            try {
                const distributorStatus = await getApplyDetail()
                if (!distributorStatus.is_distributor) {
                    setTimeout(() => {
                        uni.showModal({
                            title: '成为分销员',
                            content: '您已成功关注商家！是否申请成为分销员，推广商家文章赚取佣金？',
                            confirmText: '去申请',
                            cancelText: '稍后再说',
                            success: (res) => {
                                if (res.confirm) {
                                    uni.navigateTo({ url: '/pages/business/promotion-apply' })
                                }
                            }
                        })
                    }, 1000)
                }
            } catch (e) {
                console.error('检查分销员状态失败', e)
            }
        }
    } catch (e: any) {
        uni.showToast({ title: e.msg || '操作失败', icon: 'none' })
    }
}

watch(showAddPopup, (val) => {
    if (!val) {
        page.value = 1
        getList()
        searchKeyword.value = ''
        searchList.value = []
    }
})

onShow(() => {
    page.value = 1
    getList()
    themeStore.getTheme()
})
</script>

<style lang="scss" scoped>
.follow-page {
    min-height: 100vh;
    background-color: #f8f8f8;
    padding-bottom: 80px;
}
</style>
