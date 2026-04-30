<template>
    <uni-nav title="客户管理"></uni-nav>

    <view class="customer-list min-h-screen bg-f5">
        <!-- 搜索 -->
        <view class="search bg-white mx-3 mt-3 rounded-xl p-3 relative z-10 shadow-sm">
            <u-search
                placeholder="搜索客户昵称"
                v-model="keyword"
                @search="doSearch"
                @clear="doSearch"
            ></u-search>
        </view>

        <!-- 客户列表 -->
        <view class="list p-3">
            <view
                class="item bg-white rounded-xl mb-3 p-3 shadow-sm"
                v-for="(item, index) in list"
                :key="index"
            >
                <view class="flex items-center">
                    <u-avatar :src="item.avatar" size="48"></u-avatar>
                    <view class="ml-3 flex-1">
                        <view class="flex items-center">
                            <view class="font-bold">{{
                                item.nickname || '用户' + item.user_id
                            }}</view>
                            <view
                                v-if="item.is_fan"
                                class="ml-2 px-1.5 py-0.5 bg-orange-100 text-orange-500 text-xs rounded"
                                >粉丝</view
                            >
                        </view>
                    </view>
                    <view class="text-right">
                        <view class="text-red-500 font-bold">¥{{ item.total_amount }}</view>
                        <view class="text-xs text-gray-400">{{ item.order_count }}笔订单</view>
                    </view>
                </view>
                <view
                    class="text-xs text-gray-400 mt-2 pt-2 border-t border-gray-100 flex justify-between"
                >
                    <text>最近购买：{{ item.last_pay_time || '-' }}</text>
                </view>
            </view>
            <u-empty v-if="list.length === 0" mode="list" text="暂无客户"></u-empty>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import request from '@/utils/request'
import { onShow } from '@dcloudio/uni-app'
import { useThemeStore } from '@/stores/theme'
import { safeNavigateBack } from '@/utils/util'

const themeStore = useThemeStore()
const themeStyle = computed(() => `--color-primary: ${themeStore.primaryColor};`)

const list = ref<any[]>([])
const keyword = ref('')
const stats = ref({
    customer_count: 0,
    new_customers_today: 0
})

const getList = async () => {
    try {
        const res = await request.get({
            url: '/merchant.fan/customers',
            data: { keyword: keyword.value }
        })
        list.value = res.lists || []
    } catch (e) {
        list.value = []
    }
}

const getStats = async () => {
    try {
        const res = await request.get({ url: '/merchant.fan/statistics' })
        stats.value = res
    } catch (e) {}
}

const doSearch = () => {
    getList()
}

const goBack = () => {
    safeNavigateBack({ defaultUrl: '/pages/index/index' })
}

onShow(() => {
    uni.setNavigationBarTitle({ title: '客户管理' })
    getList()
    getStats()
    themeStore.getTheme()
})
</script>

<style lang="scss" scoped>
.customer-list {
    background-color: #f5f5f5;
}
.bg-f5 {
    background-color: #f5f5f5;
}
</style>
