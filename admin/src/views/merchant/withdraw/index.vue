<template>
    <div class="withdraw-lists">
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item label="商户名称" prop="merchant_name">
                    <el-input class="w-[200px]" v-model="queryParams.merchant_name" clearable placeholder="请输入商户名称" />
                </el-form-item>
                <el-form-item label="状态" prop="status">
                    <el-select class="w-[120px]" v-model="queryParams.status" clearable placeholder="全部">
                        <el-option label="待审核" :value="0" />
                        <el-option label="已拒绝" :value="1" />
                        <el-option label="已通过" :value="2" />
                        <el-option label="已打款" :value="3" />
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
                        <el-icon :size="24" color="#409EFF"><Wallet /></el-icon>
                    </div>
                    <div class="ml-3">
                        <div class="text-2xl font-bold">¥{{ stats.total_amount }}</div>
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
                        <div class="text-2xl font-bold text-orange-500">¥{{ stats.pending_amount }}</div>
                        <div class="text-sm text-gray-400">待审核金额 ({{ stats.pending_count }}笔)</div>
                    </div>
                </div>
            </el-card>
            <el-card class="!border-none" shadow="never">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                        <el-icon :size="24" color="#67C23A"><CircleCheck /></el-icon>
                    </div>
                    <div class="ml-3">
                        <div class="text-2xl font-bold text-green-500">¥{{ stats.success_amount }}</div>
                        <div class="text-sm text-gray-400">已通过金额</div>
                    </div>
                </div>
            </el-card>
            <el-card class="!border-none" shadow="never">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                        <el-icon :size="24" color="#9B59B6"><CreditCard /></el-icon>
                    </div>
                    <div class="ml-3">
                        <div class="text-2xl font-bold text-purple-500">¥{{ stats.success_amount }}</div>
                        <div class="text-sm text-gray-400">已打款金额</div>
                    </div>
                </div>
            </el-card>
        </div>

        <el-card class="!border-none mt-4" shadow="never">
            <el-table size="large" v-loading="pager.loading" :data="pager.lists">
                <el-table-column label="ID" prop="id" width="80" />
                <el-table-column label="商户信息" min-width="150">
                    <template #default="{ row }">
                        <div>
                            <div class="font-medium">{{ row.merchant_name }}</div>
                            <div class="text-xs text-gray-400">{{ row.merchant_mobile }}</div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="提现金额" min-width="120" align="right">
                    <template #default="{ row }">
                        <span class="text-red-500 font-bold text-lg">¥{{ row.money }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="银行信息" min-width="200">
                    <template #default="{ row }">
                        <div class="text-sm">
                            <div>{{ row.bank_name }} {{ row.bank_branch }}</div>
                            <div class="text-gray-400">{{ row.bank_account }} {{ row.bank_user }}</div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="状态" width="100" align="center">
                    <template #default="{ row }">
                        <el-tag :type="getStatusType(row.status)">{{ row.status_text }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="申请时间" prop="create_time" width="160" />
                <el-table-column label="审核时间" prop="audit_time" width="160" />
                <el-table-column label="操作" width="180" fixed="right">
                    <template #default="{ row }">
                        <el-button v-if="row.status === 0" type="success" link @click="handleAudit(row, 2)">通过</el-button>
                        <el-button v-if="row.status === 0" type="danger" link @click="handleAudit(row, 1)">拒绝</el-button>
                        <el-button v-if="row.status === 2" type="primary" link @click="handlePay(row)">确认打款</el-button>
                        <el-button type="primary" link @click="handleDetail(row)">详情</el-button>
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

        <!-- 详情弹窗 -->
        <el-dialog v-model="showDetailDialog" title="提现详情" width="500px">
            <el-descriptions :column="1" border v-if="currentItem">
                <el-descriptions-item label="商户名称">{{ currentItem.merchant_name }}</el-descriptions-item>
                <el-descriptions-item label="商户手机">{{ currentItem.merchant_mobile || '-' }}</el-descriptions-item>
                <el-descriptions-item label="提现金额">
                    <span class="text-red-500 font-bold">¥{{ currentItem.money }}</span>
                </el-descriptions-item>
                <el-descriptions-item label="收款方式">{{ currentItem.type_text || '银行卡' }}</el-descriptions-item>
                <el-descriptions-item label="银行名称">{{ currentItem.bank_name }}</el-descriptions-item>
                <el-descriptions-item label="开户支行">{{ currentItem.bank_branch || '-' }}</el-descriptions-item>
                <el-descriptions-item label="银行账号">{{ currentItem.bank_account }}</el-descriptions-item>
                <el-descriptions-item label="持卡人">{{ currentItem.bank_user }}</el-descriptions-item>
                <el-descriptions-item label="收款码" v-if="currentItem.qrcode">
                    <el-image 
                        :src="currentItem.qrcode" 
                        :preview-src-list="[currentItem.qrcode]"
                        fit="contain"
                        style="width: 150px; height: 150px;"
                    />
                </el-descriptions-item>
                <el-descriptions-item label="状态">
                    <el-tag :type="getStatusType(currentItem.status)">{{ currentItem.status_text }}</el-tag>
                </el-descriptions-item>
                <el-descriptions-item label="申请时间">{{ currentItem.create_time }}</el-descriptions-item>
                <el-descriptions-item label="审核时间">{{ currentItem.audit_time || '-' }}</el-descriptions-item>
                <el-descriptions-item label="拒绝原因" v-if="currentItem.status === 1">{{ currentItem.audit_reason }}</el-descriptions-item>
            </el-descriptions>
            <div v-if="currentItem && currentItem.qrcode" class="mt-4 text-center">
                <el-alert type="info" :closable="false" show-icon>
                    <template #title>
                        <span>请使用{{ currentItem.type_text || '支付宝' }}扫描上方收款码进行打款</span>
                    </template>
                </el-alert>
            </div>
        </el-dialog>
    </div>
</template>

<script setup lang="ts">
import { withdrawLists, withdrawAudit, withdrawStatistics } from '@/api/merchant/withdraw'
import { usePaging } from '@/hooks/usePaging'
import feedback from '@/utils/feedback'
import { Wallet, Clock, CircleCheck, CreditCard } from '@element-plus/icons-vue'

const queryParams = reactive({
    merchant_name: '',
    status: '',
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
    fetchFun: withdrawLists,
    params: queryParams
})

const stats = ref({
    total_amount: '0.00',
    pending_amount: '0.00',
    success_amount: '0.00',
    pending_count: 0
})

const showRejectDialog = ref(false)
const showDetailDialog = ref(false)
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
    const res = await withdrawStatistics()
    stats.value = res
}

const handleAudit = async (row: any, status: number) => {
    if (status === 1) {
        currentItem.value = row
        rejectReason.value = ''
        showRejectDialog.value = true
    } else {
        await feedback.confirm('确定通过该提现申请吗？')
        await withdrawAudit({ id: row.id, status })
        feedback.msgSuccess('审核通过')
        getLists()
        getStatistics()
    }
}

const confirmReject = async () => {
    if (!rejectReason.value.trim()) {
        return feedback.msgError('请输入拒绝原因')
    }
    await withdrawAudit({ id: currentItem.value.id, status: 1, reason: rejectReason.value })
    feedback.msgSuccess('已拒绝')
    showRejectDialog.value = false
    getLists()
    getStatistics()
}

const handlePay = async (row: any) => {
    await feedback.confirm('确认已打款吗？')
    await withdrawAudit({ id: row.id, status: 3 })
    feedback.msgSuccess('已确认打款')
    getLists()
    getStatistics()
}

const handleDetail = async (row: any) => {
    currentItem.value = row
    showDetailDialog.value = true
}

onMounted(() => {
    getStatistics()
})

getLists()
</script>
