<template>
    <uni-nav title="推广订单" text-color="#ffffff"></uni-nav>

    <view class="distribution-order min-h-screen bg-f5">
        <view class="list p-3">
            <view
                class="item bg-white rounded-xl mb-3 p-3 shadow-sm"
                v-for="(item, index) in list"
                :key="index"
            >
                <view class="flex items-center">
                    <u-avatar :src="item.avatar" size="40"></u-avatar>
                    <view class="ml-3 flex-1">
                        <view class="font-bold">{{ item.nickname || '用户' }}</view>
                        <view class="text-xs text-gray-400 mt-1">{{ item.create_time }}</view>
                    </view>
                    <view class="text-right">
                        <view class="text-red-500 font-bold">+¥{{ item.amount }}</view>
                        <view class="text-xs text-gray-400">佣金</view>
                    </view>
                </view>
                <view class="mt-2 pt-2 border-t border-gray-100">
                    <view class="text-sm text-gray-500">购买文章：{{ item.article_title }}</view>
                    <view class="text-xs text-gray-400 mt-1"
                        >订单金额：¥{{ item.order_amount }}</view
                    >
                </view>
            </view>
            <u-empty v-if="list.length === 0" mode="list" text="暂无推广订单"></u-empty>
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
const page = ref(1)
const hasMore = ref(true)

const getList = async () => {
    if (!hasMore.value) return
    try {
        const res = await request.get({
            url: '/distribution.index/orders',
            data: { page: page.value, limit: 20 }
        })
        if (page.value === 1) {
            list.value = res.lists || []
        } else {
            list.value = [...list.value, ...(res.lists || [])]
        }
        hasMore.value = (res.lists || []).length >= 20
    } catch (e) {
        list.value = []
    }
}

const goBack = () => {
    safeNavigateBack({ defaultUrl: '/pages/index/index' })
}

onShow(() => {
    uni.setNavigationBarTitle({ title: '推广订单' })
    page.value = 1
    hasMore.value = true
    getList()
    themeStore.getTheme()
})
</script>

<style lang="scss" scoped>
.distribution-order {
    background-color: #f5f5f5;
}
.bg-f5 {
    background-color: #f5f5f5;
}
</style>
