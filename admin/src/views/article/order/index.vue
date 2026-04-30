<!--
    文章订单管理
    开发者：杰哥网络科技 qq2711793818 杰哥
-->
<template>
    <div class="order-lists">
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item label="订单号" prop="order_sn">
                    <el-input class="w-[200px]" v-model="queryParams.order_sn" clearable placeholder="请输入订单号" />
                </el-form-item>
                <el-form-item label="用户信息" prop="user_info">
                    <el-input class="w-[160px]" v-model="queryParams.user_info" clearable placeholder="昵称/手机号" />
                </el-form-item>
                <el-form-item label="支付状态" prop="pay_status">
                    <el-select class="w-[120px]" v-model="queryParams.pay_status" clearable placeholder="全部">
                        <el-option label="待支付" :value="0" />
                        <el-option label="已支付" :value="1" />
                    </el-select>
                </el-form-item>
                <el-form-item label="退款状态" prop="refund_status">
                    <el-select class="w-[120px]" v-model="queryParams.refund_status" clearable placeholder="全部">
                        <el-option label="未退款" :value="0" />
                        <el-option label="已退款" :value="1" />
                    </el-select>
                </el-form-item>
                <el-form-item label="时间" prop="date_range">
                    <el-date-picker
                        v-model="dateRange"
                        type="daterange"
                        range-separator="至"
                        start-placeholder="开始日期"
                        end-placeholder="结束日期"
                        value-format="YYYY-MM-DD"
                        @change="handleDateChange"
                    />
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                </el-form-item>
            </el-form>
        </el-card>

        <!-- 统计卡片 -->
        <div class="grid grid-cols-4 gap-4 mt-4">
            <el-card class="!border-none" shadow="never">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                        <el-icon :size="24" color="#409EFF"><Document /></el-icon>
                    </div>
                    <div class="ml-3">
                        <div class="text-2xl font-bold">{{ stats.total_orders }}</div>
                        <div class="text-sm text-gray-400">总订单数</div>
                    </div>
                </div>
            </el-card>
            <el-card class="!border-none" shadow="never">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                        <el-icon :size="24" color="#67C23A"><Money /></el-icon>
                    </div>
                    <div class="ml-3">
                        <div class="text-2xl font-bold text-green-500">¥{{ stats.total_amount }}</div>
                        <div class="text-sm text-gray-400">总收入金额</div>
                    </div>
                </div>
            </el-card>
            <el-card class="!border-none" shadow="never">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                        <el-icon :size="24" color="#E6A23C"><TrendCharts /></el-icon>
                    </div>
                    <div class="ml-3">
                        <div class="text-2xl font-bold text-orange-500">¥{{ stats.today_amount }}</div>
                        <div class="text-sm text-gray-400">今日收入</div>
                    </div>
                </div>
            </el-card>
            <el-card class="!border-none" shadow="never">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                        <el-icon :size="24" color="#F56C6C"><RefreshLeft /></el-icon>
                    </div>
                    <div class="ml-3">
                        <div class="text-2xl font-bold text-red-500">¥{{ stats.refund_amount }}</div>
                        <div class="text-sm text-gray-400">退款金额</div>
                    </div>
                </div>
            </el-card>
        </div>

        <el-card class="!border-none mt-4" shadow="never">
            <el-table size="large" v-loading="pager.loading" :data="pager.lists">
                <el-table-column label="订单号" prop="order_sn" width="200" />
                <el-table-column label="用户" min-width="150">
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <el-avatar :size="32" :src="row.avatar">
                                <el-icon><User /></el-icon>
                            </el-avatar>
                            <div class="ml-2">
                                <div>{{ row.nickname }}</div>
                                <div class="text-xs text-gray-400">{{ row.user_mobile }}</div>
                            </div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="文章" prop="article_title" min-width="150" show-overflow-tooltip />
                <el-table-column label="商户" min-width="120">
                    <template #default="{ row }">
                        <span>{{ row.merchant_name || '-' }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="金额" width="100" align="right">
                    <template #default="{ row }">
                        <span class="text-red-500 font-bold">¥{{ row.order_amount }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="支付状态" width="100" align="center">
                    <template #default="{ row }">
                        <el-tag :type="row.pay_status === 1 ? 'success' : 'warning'">
                            {{ row.pay_status_text }}
                        </el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="退款状态" width="100" align="center">
                    <template #default="{ row }">
                        <el-tag :type="row.refund_status === 1 ? 'danger' : 'info'">
                            {{ row.refund_status_text }}
                        </el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="支付时间" prop="pay_time" width="160" />
                <el-table-column label="创建时间" prop="create_time" width="160" />
                <el-table-column label="操作" width="120" fixed="right">
                    <template #default="{ row }">
                        <el-button type="primary" link @click="handleDetail(row)">详情</el-button>
                        <el-button v-if="row.pay_status === 1 && row.refund_status !== 1" type="danger" link @click="handleRefund(row)">退款</el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>

        <!-- 详情弹窗 -->
        <el-dialog v-model="showDetail" title="订单详情" width="600px">
            <el-descriptions :column="2" border v-if="currentOrder">
                <el-descriptions-item label="订单号" :span="2">{{ currentOrder.order_sn }}</el-descriptions-item>
                <el-descriptions-item label="用户昵称">{{ currentOrder.nickname }}</el-descriptions-item>
                <el-descriptions-item label="用户手机">{{ currentOrder.user_mobile || '-' }}</el-descriptions-item>
                <el-descriptions-item label="文章标题" :span="2">{{ currentOrder.article_title }}</el-descriptions-item>
                <el-descriptions-item label="商户名称">{{ currentOrder.merchant_name || '-' }}</el-descriptions-item>
                <el-descriptions-item label="商户手机">{{ currentOrder.merchant_mobile || '-' }}</el-descriptions-item>
                <el-descriptions-item label="订单金额">
                    <span class="text-red-500 font-bold">¥{{ currentOrder.order_amount }}</span>
                </el-descriptions-item>
                <el-descriptions-item label="支付状态">
                    <el-tag :type="currentOrder.pay_status === 1 ? 'success' : 'warning'">
                        {{ currentOrder.pay_status_text }}
                    </el-tag>
                </el-descriptions-item>
                <el-descriptions-item label="退款状态">
                    <el-tag :type="currentOrder.refund_status === 1 ? 'danger' : 'info'">
                        {{ currentOrder.refund_status_text }}
                    </el-tag>
                </el-descriptions-item>
                <el-descriptions-item label="退款原因" v-if="currentOrder.refund_status === 1">
                    {{ currentOrder.refund_reason || '-' }}
                </el-descriptions-item>
                <el-descriptions-item label="退款时间" v-if="currentOrder.refund_status === 1">
                    {{ currentOrder.refund_time || '-' }}
                </el-descriptions-item>
                <el-descriptions-item label="支付时间">{{ currentOrder.pay_time || '-' }}</el-descriptions-item>
                <el-descriptions-item label="创建时间">{{ currentOrder.create_time }}</el-descriptions-item>
            </el-descriptions>
        </el-dialog>

        <!-- 退款弹窗 -->
        <el-dialog v-model="showRefund" title="订单退款" width="400px">
            <el-form>
                <el-form-item label="退款原因">
                    <el-input v-model="refundReason" type="textarea" :rows="3" placeholder="请输入退款原因" />
                </el-form-item>
            </el-form>
            <template #footer>
                <el-button @click="showRefund = false">取消</el-button>
                <el-button type="danger" @click="confirmRefund">确认退款</el-button>
            </template>
        </el-dialog>
    </div>
</template>

<script setup lang="ts">
/**
 * 文章订单管理
 * 开发者：杰哥网络科技 qq2711793818 杰哥
 */
import { orderLists, orderDetail, orderStatistics, orderRefund } from '@/api/article/order'
import { usePaging } from '@/hooks/usePaging'
import feedback from '@/utils/feedback'
import { Document, Money, TrendCharts, RefreshLeft, User } from '@element-plus/icons-vue'

const queryParams = reactive({
    order_sn: '',
    user_info: '',
    pay_status: '' as string | number,
    refund_status: '' as string | number,
    start_time: '',
    end_time: ''
})

const dateRange = ref<string[]>([])

const handleDateChange = (val: string[]) => {
    if (val) {
        queryParams.start_time = val[0]
        queryParams.end_time = val[1]
    } else {
        queryParams.start_time = ''
        queryParams.end_time = ''
    }
}

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: orderLists,
    params: queryParams
})

const stats = ref({
    total_orders: 0,
    paid_orders: 0,
    unpaid_orders: 0,
    total_amount: '0.00',
    today_amount: '0.00',
    refund_amount: '0.00'
})

const showDetail = ref(false)
const showRefund = ref(false)
const currentOrder = ref<any>(null)
const refundReason = ref('')

const getStatistics = async () => {
    const res = await orderStatistics()
    stats.value = res
}

const handleDetail = async (row: any) => {
    const res = await orderDetail({ id: row.id })
    currentOrder.value = res
    showDetail.value = true
}

const handleRefund = (row: any) => {
    currentOrder.value = row
    refundReason.value = ''
    showRefund.value = true
}

const confirmRefund = async () => {
    if (!refundReason.value.trim()) {
        return feedback.msgError('请输入退款原因')
    }
    await feedback.confirm('确定要退款吗？退款后金额将退回商户余额。')
    await orderRefund({ id: currentOrder.value.id, reason: refundReason.value })
    feedback.msgSuccess('退款成功')
    showRefund.value = false
    getLists()
    getStatistics()
}

onMounted(() => {
    getStatistics()
})

getLists()
</script>
