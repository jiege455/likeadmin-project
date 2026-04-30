<template>
    <uni-nav title="优惠券管理">
        <template #right>
            <view class="w-10 h-full flex items-center justify-center" @click="toEdit">
                <text style="font-size: 14px">添加</text>
            </view>
        </template>
    </uni-nav>

    <view class="merchant-coupon-list min-h-screen bg-f5">
        <!-- 列表区域 -->
        <view class="list px-3 mt-3">
            <view
                class="item bg-white rounded-xl mb-3 overflow-hidden shadow-sm"
                v-for="(item, index) in list"
                :key="index"
            >
                <view class="flex">
                    <view
                        class="coupon-left w-24 py-4 flex flex-col items-center justify-center"
                        :style="{ backgroundColor: themeStore.primaryColor }"
                    >
                        <view class="text-white text-2xl font-bold">¥{{ item.money }}</view>
                        <view class="text-white text-xs opacity-80 mt-1">{{
                            item.condition_money > 0
                                ? '满' + item.condition_money + '可用'
                                : '无门槛'
                        }}</view>
                    </view>
                    <view class="coupon-right flex-1 p-3 flex flex-col justify-between">
                        <view class="text-sm font-bold text-gray-800">{{ item.name }}</view>
                        <view class="text-xs text-gray-400">有效期：{{ item.use_time_desc }}</view>
                        <view class="flex justify-between items-center mt-2">
                            <view class="text-xs text-gray-400"
                                >已领取：{{ item.send_count || 0 }}/{{ item.total_count }}</view
                            >
                            <u-button size="mini" type="error" plain @click="handleDel(item.id)"
                                >删除</u-button
                            >
                        </view>
                    </view>
                </view>
            </view>
            <u-empty v-if="list.length === 0" mode="coupon" text="暂无优惠券"></u-empty>
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

const getList = async () => {
    try {
        const res = await request.get({ url: '/merchant.coupon/lists' })
        list.value = res || []
    } catch (e) {
        list.value = []
    }
}

const goBack = () => {
    safeNavigateBack({ defaultUrl: '/pages/index/index' })
}

const toEdit = () => {
    uni.navigateTo({ url: '/packages/pages/merchant/coupon/edit' })
}

const handleDel = async (id: number) => {
    uni.showModal({
        title: '提示',
        content: '确定要删除吗？',
        success: async (res) => {
            if (res.confirm) {
                await request.post({ url: '/merchant.coupon/del', data: { id } })
                getList()
            }
        }
    })
}

onShow(() => {
    uni.setNavigationBarTitle({ title: '优惠券管理' })
    getList()
    themeStore.getTheme()
})
</script>

<style lang="scss" scoped>
.merchant-coupon-list {
    background-color: #f5f5f5;
}
.bg-f5 {
    background-color: #f5f5f5;
}
</style>
