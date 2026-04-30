<template>
    <uni-nav title="提现记录"></uni-nav>

    <view class="withdraw-log min-h-screen bg-f5">
        <!-- 列表区域 -->
        <view
            class="content-area bg-white mx-3 mt-3 rounded-xl relative z-10 shadow-sm overflow-hidden"
        >
            <view
                class="p-4"
                v-for="(item, index) in list"
                :key="index"
                :class="index < list.length - 1 ? 'border-b border-gray-100' : ''"
            >
                <view class="flex justify-between items-center">
                    <view class="text-xl font-bold text-red-500">¥{{ item.money }}</view>
                    <view
                        class="text-sm px-3 py-1 rounded-full"
                        :class="getStatusClass(item.status)"
                    >
                        {{ getStatusText(item.status) }}
                    </view>
                </view>
                <view class="text-xs text-gray-400 mt-2"
                    >银行：{{ item.bank_name }} {{ item.bank_account }}</view
                >
                <view class="text-xs text-gray-400 mt-1">申请时间：{{ item.create_time }}</view>
                <view class="text-xs text-red-400 mt-1" v-if="item.status == 1"
                    >拒绝原因：{{ item.audit_reason || '无' }}</view
                >
            </view>
            <u-empty v-if="list.length === 0" mode="list" text="暂无记录"></u-empty>
            <u-loadmore :status="status" />
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import request from '@/utils/request'
import { onShow, onReachBottom } from '@dcloudio/uni-app'
import { useThemeStore } from '@/stores/theme'
import { safeNavigateBack } from '@/utils/util'

const themeStore = useThemeStore()
const themeStyle = computed(() => {
    return `
        --color-primary: ${themeStore.primaryColor};
    `
})

const list = ref<any[]>([])
const page = ref(1)
const status = ref('loadmore')
const hasMore = ref(true)

const getStatusClass = (status: number) => {
    const classes: Record<number, string> = {
        0: 'bg-orange-100 text-orange-600',
        1: 'bg-red-100 text-red-600',
        2: 'bg-green-100 text-green-600',
        3: 'bg-blue-100 text-blue-600'
    }
    return classes[status] || 'bg-gray-100 text-gray-600'
}

const getStatusText = (status: number) => {
    const texts: Record<number, string> = {
        0: '待审核',
        1: '已拒绝',
        2: '已通过',
        3: '已打款'
    }
    return texts[status] || '未知'
}

const getList = async () => {
    status.value = 'loading'
    try {
        const res = await request.get({
            url: '/merchant.withdraw/lists',
            data: { page_no: page.value, page_size: 10 }
        })

        if (page.value == 1) list.value = res.lists
        else list.value = list.value.concat(res.lists)

        if (res.lists.length < 10) {
            status.value = 'nomore'
            hasMore.value = false
        } else {
            status.value = 'loadmore'
        }
    } catch (e) {
        status.value = 'loadmore'
    }
}

const goBack = () => {
    safeNavigateBack({ defaultUrl: '/pages/index/index' })
}

onReachBottom(() => {
    if (hasMore.value) {
        page.value++
        getList()
    }
})

onShow(() => {
    uni.setNavigationBarTitle({ title: '提现记录' })
    getList()
    themeStore.getTheme()
})
</script>

<style lang="scss" scoped>
.withdraw-log {
    background-color: #f5f5f5;
}
.bg-f5 {
    background-color: #f5f5f5;
}
</style>
