<template>
    <uni-nav title="提现记录" text-color="#ffffff"></uni-nav>

    <view class="distribution-withdraw-log min-h-screen bg-f5">
        <view class="list p-3">
            <view
                class="item bg-white rounded-xl mb-3 p-4 shadow-sm"
                v-for="(item, index) in list"
                :key="index"
            >
                <view class="flex justify-between items-center">
                    <view class="text-xl font-bold text-red-500">¥{{ item.money }}</view>
                    <view
                        class="text-sm px-3 py-1 rounded-full"
                        :class="getStatusClass(item.status)"
                    >
                        {{ item.status_text }}
                    </view>
                </view>
                <view class="text-xs text-gray-400 mt-2"
                    >提现方式：{{ item.type == 2 ? '支付宝' : '银行卡' }}</view
                >
                <view class="text-xs text-gray-400 mt-1">申请时间：{{ item.create_time }}</view>
                <view class="text-xs text-red-400 mt-1" v-if="item.status == 1"
                    >拒绝原因：{{ item.audit_reason || '无' }}</view
                >
            </view>
            <u-empty v-if="list.length === 0" mode="list" text="暂无提现记录"></u-empty>
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

const getStatusClass = (status: number) => {
    const classes: Record<number, string> = {
        0: 'bg-orange-100 text-orange-600',
        1: 'bg-red-100 text-red-600',
        2: 'bg-green-100 text-green-600',
        3: 'bg-blue-100 text-blue-600'
    }
    return classes[status] || 'bg-gray-100 text-gray-600'
}

const getList = async () => {
    try {
        const res = await request.get({ url: '/distribution.withdraw/lists' })
        list.value = res.lists || []
    } catch (e) {
        list.value = []
    }
}

const goBack = () => {
    safeNavigateBack({ defaultUrl: '/pages/index/index' })
}

onShow(() => {
    uni.setNavigationBarTitle({ title: '提现记录' })
    getList()
    themeStore.getTheme()
})
</script>

<style lang="scss" scoped>
.distribution-withdraw-log {
    background-color: #f5f5f5;
}
.bg-f5 {
    background-color: #f5f5f5;
}
</style>
