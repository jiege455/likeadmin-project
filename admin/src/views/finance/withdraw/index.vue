<template>
    <div class="finance-withdraw">
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item label="关键词" prop="keyword">
                    <el-input class="w-[200px]" v-model="queryParams.keyword" clearable placeholder="商户/用户/手机号" />
                </el-form-item>
                <el-form-item label="来源" prop="source">
                    <el-select class="w-[120px]" v-model="queryParams.source" clearable placeholder="全部">
                        <el-option label="商户收益" :value="1" />
                        <el-option label="推广佣金" :value="2" />
                    </el-select>
                </el-form-item>
                <el-form-item label="状态" prop="status">
                    <el-select class="w-[120px]" v-model="queryParams.status" clearable placeholder="全部">
                        <el-option label="待审核" :value="0" />
                        <el-option label="已拒绝" :value="1" />
                        <el-option label="已通过" :value="2" />
                        <el-option label="已打款" :value="3" />
                    </el-select>
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
                        <el-icon :size="24" color="#409EFF"><Wallet /></el-icon>
                    </div>
                    <div class="ml-3">
                        <div class="text-2xl font-bold">¥{{ stats.total }}</div>
                        <div class="text-sm text-gray-400">提现总金额</div>
                    </div>
                </div>
            </el-card>
            <el-card class="!border-none" shadow="never">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                        <el-icon :size="24" color="#E6A23C"><Clock /></el-icon>
                    </div>
                    <div class="ml-3">
                        <div class="text-2xl font-bold text-orange-500">¥{{ stats.pending }}</div>
                        <div class="text-sm text-gray-400">待审核 ({{ stats.pending_count }}笔)</div>
                    </div>
                </div>
            </el-card>
            <el-card class="!border-none" shadow="never">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                        <el-icon :size="24" color="#67C23A"><Coin /></el-icon>
                    </div>
                    <div class="ml-3">
                        <div class="text-2xl font-bold text-green-500">¥{{ stats.merchant_total }}</div>
                        <div class="text-sm text-gray-400">商户提现</div>
                    </div>
                </div>
            </el-card>
            <el-card class="!border-none" shadow="never">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                        <el-icon :size="24" color="#9B59B6"><Money /></el-icon>
                    </div>
                    <div class="ml-3">
                        <div class="text-2xl font-bold text-purple-500">¥{{ stats.commission_total }}</div>
                        <div class="text-sm text-gray-400">佣金提现</div>
                    </div>
                </div>
            </el-card>
        </div>

        <el-card class="!border-none mt-4" shadow="never">
            <el-table size="large" v-loading="pager.loading" :data="pager.lists">
                <el-table-column label="ID" prop="id" width="80" />
                <el-table-column label="来源" width="100" align="center">
                    <template #default="{ row }">
                        <el-tag :type="row.source == 1 ? 'success' : 'warning'">{{ row.source_text }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="申请人" min-width="150">
                    <template #default="{ row }">
                        <div v-if="row.source == 1">
                            <div class="font-medium">{{ row.merchant_name }}</div>
                            <div class="text-xs text-gray-400">商户</div>
                        </div>
                        <div v-else>
                            <div class="font-medium">{{ row.nickname }}</div>
                            <div class="text-xs text-gray-400">{{ row.user_mobile }}</div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="提现金额" min-width="120" align="right">
                    <template #default="{ row }">
                        <span class="text-red-500 font-bold text-lg">¥{{ row.money }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="提现方式" prop="type_text" width="100" />
                <el-table-column label="银行信息" min-width="200">
                    <template #default="{ row }">
                        <div v-if="row.type == 3">
                            <div>{{ row.bank_name }} {{ row.bank_account }}</div>
                            <div class="text-xs text-gray-400">{{ row.bank_user }}</div>
                        </div>
                        <div v-else class="text-gray-400">-</div>
                    </template>
                </el-table-column>
                <el-table-column label="状态" width="100" align="center">
                    <template #default="{ row }">
                        <el-tag :type="getStatusType(row.status)">{{ row.status_text }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="申请时间" prop="create_time" width="160" />
                <el-table-column label="操作" width="180" fixed="right">
                    <template #default="{ row }">
                        <el-button v-if="row.status === 0" type="success" link @click="handleAudit(row, 2)">通过</el-button>
                        <el-button v-if="row.status === 0" type="danger" link @click="handleAudit(row, 1)">拒绝</el-button>
                        <el-button v-if="row.status === 2" type="primary" link @click="handlePay(row)">确认打款</el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>

        <!-- 拒绝原因弹窗 -->
        <el-dialog v-model="showRejectDialog" title="拒绝原因" width="400px">
            <el-input v-model="rejectReason" type="textarea" :rows="3" placeholder="请输入拒绝原因" />
            <template #footer>
                <el-button @click="showRejectDialog = false">取消</el-button>
                <el-button type="danger" @click="confirmReject">确定拒绝</el-button>
            </template>
        </el-dialog>
    </div>
</template>

<script setup lang="ts">
import { merchantWithdrawLists, merchantWithdrawAudit, merchantWithdrawStatistics } from '@/api/finance/merchantWithdraw'
import { usePaging } from '@/hooks/usePaging'
import feedback from '@/utils/feedback'
import { Wallet, Clock, Coin, Money } from '@element-plus/icons-vue'

const queryParams = reactive({
    keyword: '',
    source: '',
    status: ''
})

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: merchantWithdrawLists,
    params: queryParams
})

const stats = ref({
    total: '0.00',
    pending: '0.00',
    success: '0.00',
    pending_count: 0,
    merchant_total: '0.00',
    commission_total: '0.00'
})

const showRejectDialog = ref(false)
const rejectReason = ref('')
const currentItem = ref<any>(null)

const getStatusType = (status: number) => {
    const types: Record<number, string> = {
        0: 'warning',
        1: 'danger',
        2: 'success',
        3: 'primary'
    }
    return types[status] || 'info'
}

const getStatistics = async () => {
    try {
        const res = await merchantWithdrawStatistics()
        stats.value = res
    } catch (error) {
        feedback.msgError('获取统计数据失败')
    }
}

const handleAudit = async (row: any, status: number) => {
    if (status === 1) {
        currentItem.value = row
        rejectReason.value = ''
        showRejectDialog.value = true
    } else {
        try {
            await feedback.confirm('确定通过该提现申请吗？')
            await merchantWithdrawAudit({ id: row.id, status })
            feedback.msgSuccess('审核通过')
            getLists()
            getStatistics()
        } catch (error) {
            console.error('审核失败:', error)
        }
    }
}

const confirmReject = async () => {
    try {
        await merchantWithdrawAudit({ id: currentItem.value.id, status: 1, remark: rejectReason.value })
        feedback.msgSuccess('已拒绝')
        showRejectDialog.value = false
        getLists()
        getStatistics()
    } catch (error) {
        console.error('拒绝操作失败:', error)
    }
}

const handlePay = async (row: any) => {
    try {
        await feedback.confirm('确认已打款吗？')
        await merchantWithdrawAudit({ id: row.id, status: 3 })
        feedback.msgSuccess('已确认打款')
        getLists()
        getStatistics()
    } catch (error) {
        console.error('打款确认失败:', error)
    }
}

onMounted(() => {
    getStatistics()
})

getLists()
</script>
