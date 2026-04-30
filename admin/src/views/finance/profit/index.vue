<template>
    <div class="platform-profit">
        <!-- 统计卡片 -->
        <div class="grid grid-cols-4 gap-4">
            <el-card class="!border-none" shadow="never">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                        <el-icon :size="24" color="#409EFF"><Money /></el-icon>
                    </div>
                    <div class="ml-3">
                        <div class="text-2xl font-bold">¥{{ stats.total_order_amount }}</div>
                        <div class="text-sm text-gray-400">总交易额</div>
                    </div>
                </div>
            </el-card>
            <el-card class="!border-none" shadow="never">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                        <el-icon :size="24" color="#67C23A"><Coin /></el-icon>
                    </div>
                    <div class="ml-3">
                        <div class="text-2xl font-bold text-green-500">¥{{ stats.total_platform_profit }}</div>
                        <div class="text-sm text-gray-400">平台总收益</div>
                    </div>
                </div>
            </el-card>
            <el-card class="!border-none" shadow="never">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                        <el-icon :size="24" color="#E6A23C"><Wallet /></el-icon>
                    </div>
                    <div class="ml-3">
                        <div class="text-2xl font-bold text-orange-500">¥{{ stats.pending_settle }}</div>
                        <div class="text-sm text-gray-400">待结算金额</div>
                    </div>
                </div>
            </el-card>
            <el-card class="!border-none" shadow="never">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                        <el-icon :size="24" color="#9B59B6"><CreditCard /></el-icon>
                    </div>
                    <div class="ml-3">
                        <div class="text-2xl font-bold text-purple-500">¥{{ stats.settled_amount }}</div>
                        <div class="text-sm text-gray-400">已结算金额</div>
                    </div>
                </div>
            </el-card>
        </div>

        <!-- 今日/本月数据 -->
        <div class="grid grid-cols-2 gap-4 mt-4">
            <el-card class="!border-none" shadow="never">
                <div class="text-gray-500 mb-2">今日数据</div>
                <div class="flex justify-between">
                    <div>
                        <div class="text-xl font-bold">¥{{ stats.today_order_amount }}</div>
                        <div class="text-sm text-gray-400">交易额</div>
                    </div>
                    <div>
                        <div class="text-xl font-bold text-green-500">¥{{ stats.today_platform_profit }}</div>
                        <div class="text-sm text-gray-400">平台收益</div>
                    </div>
                </div>
            </el-card>
            <el-card class="!border-none" shadow="never">
                <div class="text-gray-500 mb-2">本月数据</div>
                <div class="flex justify-between">
                    <div>
                        <div class="text-xl font-bold">¥{{ stats.month_order_amount }}</div>
                        <div class="text-sm text-gray-400">交易额</div>
                    </div>
                    <div>
                        <div class="text-xl font-bold text-green-500">¥{{ stats.month_platform_profit }}</div>
                        <div class="text-sm text-gray-400">平台收益</div>
                    </div>
                </div>
            </el-card>
        </div>

        <!-- 商户收益列表 -->
        <el-card class="!border-none mt-4" shadow="never">
            <template #header>
                <div class="flex items-center justify-between">
                    <span class="font-medium">商户收益统计</span>
                </div>
            </template>
            <el-form :inline="true" class="mb-4">
                <el-form-item>
                    <el-input v-model="merchantName" placeholder="商户名称" clearable @keyup.enter="getMerchantProfit" />
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="getMerchantProfit">查询</el-button>
                </el-form-item>
            </el-form>
            <el-table :data="merchantList" v-loading="merchantLoading">
                <el-table-column label="商户" min-width="150">
                    <template #default="{ row }">
                        <div class="font-medium">{{ row.name }}</div>
                    </template>
                </el-table-column>
                <el-table-column label="订单数" prop="total_order_count" width="100" align="center" />
                <el-table-column label="交易额" min-width="120" align="right">
                    <template #default="{ row }">
                        <span class="text-blue-500">¥{{ row.total_order_amount }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="账户余额" min-width="120" align="right">
                    <template #default="{ row }">
                        <span class="text-orange-500">¥{{ row.balance }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="已结算" min-width="120" align="right">
                    <template #default="{ row }">
                        <span class="text-green-500">¥{{ row.settled_amount }}</span>
                    </template>
                </el-table-column>
            </el-table>
        </el-card>
    </div>
</template>

<script setup lang="ts">
import { profitStatistics, profitTrend, merchantProfit } from '@/api/finance/profit'
import { Money, Coin, Wallet, CreditCard } from '@element-plus/icons-vue'

const stats = ref({
    total_order_amount: '0.00',
    total_platform_profit: '0.00',
    total_merchant_profit: '0.00',
    today_order_amount: '0.00',
    today_platform_profit: '0.00',
    month_order_amount: '0.00',
    month_platform_profit: '0.00',
    pending_settle: '0.00',
    settled_amount: '0.00'
})

const merchantList = ref<any[]>([])
const merchantLoading = ref(false)
const merchantName = ref('')

const getStatistics = async () => {
    const res = await profitStatistics()
    stats.value = res
}

const getMerchantProfit = async () => {
    merchantLoading.value = true
    try {
        const res = await merchantProfit({ merchant_name: merchantName.value })
        merchantList.value = res.lists || []
    } finally {
        merchantLoading.value = false
    }
}

onMounted(() => {
    getStatistics()
    getMerchantProfit()
})
</script>
