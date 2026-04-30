<!--
    待处理审批管理页面
    开发者：杰哥网络科技 qq2711793818 杰哥
-->
<template>
    <div class="pending-approval">
        <el-card class="!border-none" shadow="never">
            <el-alert
                type="info"
                title="待处理审批说明"
                :closable="false"
                show-icon
            >
                <template #default>
                    <div>此页面集中展示所有待处理的审批事项，包括分销员申请、商家入驻申请、文章审核、提现申请等。</div>
                    <div class="mt-1 text-gray-500">提示：您可以直接在此页面进行快捷审批操作，也可以点击详情跳转到对应的管理页面。</div>
                </template>
            </el-alert>

            <div class="statistics-cards mt-4">
                <el-row :gutter="16">
                    <el-col :span="4">
                        <el-card class="stat-card" :class="{ active: queryParams.type === 'all' }" @click="handleTypeChange('all')">
                            <div class="stat-value">{{ statistics.total || 0 }}</div>
                            <div class="stat-label">全部待处理</div>
                        </el-card>
                    </el-col>
                    <el-col :span="4">
                        <el-card class="stat-card success" :class="{ active: queryParams.type === 'distribution_apply' }" @click="handleTypeChange('distribution_apply')">
                            <div class="stat-value">{{ statistics.distribution_apply || 0 }}</div>
                            <div class="stat-label">分销员申请</div>
                        </el-card>
                    </el-col>
                    <el-col :span="4">
                        <el-card class="stat-card warning" :class="{ active: queryParams.type === 'merchant_apply' }" @click="handleTypeChange('merchant_apply')">
                            <div class="stat-value">{{ statistics.merchant_apply || 0 }}</div>
                            <div class="stat-label">商家入驻</div>
                        </el-card>
                    </el-col>
                    <el-col :span="4">
                        <el-card class="stat-card primary" :class="{ active: queryParams.type === 'article_audit' }" @click="handleTypeChange('article_audit')">
                            <div class="stat-value">{{ statistics.article_audit || 0 }}</div>
                            <div class="stat-label">文章审核</div>
                        </el-card>
                    </el-col>
                    <el-col :span="4">
                        <el-card class="stat-card danger" :class="{ active: queryParams.type === 'withdraw_apply' }" @click="handleTypeChange('withdraw_apply')">
                            <div class="stat-value">{{ statistics.withdraw_apply || 0 }}</div>
                            <div class="stat-label">提现申请</div>
                        </el-card>
                    </el-col>
                </el-row>
            </div>

            <el-form ref="formRef" class="mb-[-16px] mt-[16px]" :model="queryParams" :inline="true">
                <el-form-item label="申请类型" prop="type">
                    <el-select class="w-[150px]" v-model="queryParams.type" clearable placeholder="全部类型" @change="resetPage">
                        <el-option label="全部" value="all" />
                        <el-option label="分销员申请" value="distribution_apply" />
                        <el-option label="商家入驻申请" value="merchant_apply" />
                        <el-option label="文章审核" value="article_audit" />
                        <el-option label="提现申请" value="withdraw_apply" />
                    </el-select>
                </el-form-item>
                <el-form-item label="排序方式" prop="order_by">
                    <el-select class="w-[150px]" v-model="queryParams.order_by" @change="resetPage">
                        <el-option label="按时间排序" value="create_time" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                </el-form-item>
            </el-form>
        </el-card>

        <el-card class="!border-none mt-4" shadow="never">
            <el-table size="large" v-loading="loading" :data="tableData">
                <el-table-column label="申请类型" width="120" align="center">
                    <template #default="{ row }">
                        <el-tag :type="getTypeTagType(row.type)">{{ row.type_name }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="申请人" min-width="150">
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <el-avatar :size="40" :src="row.applicant_avatar">
                                <el-icon><User /></el-icon>
                            </el-avatar>
                            <div class="ml-2">
                                <div class="font-medium">{{ row.applicant_name }}</div>
                                <div class="text-xs text-gray-400" v-if="row.applicant_mobile">{{ row.applicant_mobile }}</div>
                            </div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="申请内容摘要" min-width="200" show-overflow-tooltip>
                    <template #default="{ row }">
                        <span>{{ row.summary }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="申请时间" width="160" prop="create_time_text" />
                <el-table-column label="操作" width="200" fixed="right">
                    <template #default="{ row }">
                        <el-button type="success" link @click="handleAudit(row, 1)">批准</el-button>
                        <el-button type="danger" link @click="handleAudit(row, 2)">驳回</el-button>
                        <el-button type="primary" link @click="handleDetail(row)">详情</el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex justify-end mt-4">
                <el-pagination
                    v-model:current-page="queryParams.page_no"
                    v-model:page-size="queryParams.page_size"
                    :page-sizes="[10, 15, 20, 30, 50]"
                    :total="total"
                    layout="total, sizes, prev, pager, next, jumper"
                    @size-change="getLists"
                    @current-change="getLists"
                />
            </div>
        </el-card>

        <el-dialog v-model="showRejectDialog" title="驳回原因" width="400px">
            <el-input v-model="rejectReason" type="textarea" :rows="3" placeholder="请输入驳回原因" />
            <template #footer>
                <el-button @click="showRejectDialog = false">取消</el-button>
                <el-button type="danger" @click="confirmReject">确定驳回</el-button>
            </template>
        </el-dialog>

        <el-dialog v-model="showDetailDialog" title="审批详情" width="600px">
            <el-descriptions :column="2" border v-if="detailData">
                <el-descriptions-item label="申请类型">{{ detailData.type_name }}</el-descriptions-item>
                <el-descriptions-item label="申请时间">{{ detailData.create_time_text }}</el-descriptions-item>
                <el-descriptions-item label="申请人">{{ detailData.applicant_name }}</el-descriptions-item>
                <el-descriptions-item label="联系电话">{{ detailData.applicant_mobile || '-' }}</el-descriptions-item>
                <el-descriptions-item label="申请内容" :span="2">{{ detailData.summary }}</el-descriptions-item>
                <el-descriptions-item label="详细信息" :span="2">
                    <pre class="whitespace-pre-wrap text-sm">{{ JSON.stringify(detailData, null, 2) }}</pre>
                </el-descriptions-item>
            </el-descriptions>
            <template #footer>
                <el-button @click="showDetailDialog = false">关闭</el-button>
                <el-button type="success" @click="handleAuditFromDetail(1)">批准</el-button>
                <el-button type="danger" @click="handleAuditFromDetail(2)">驳回</el-button>
            </template>
        </el-dialog>
    </div>
</template>

<script setup lang="ts">
import { pendingApprovalLists, pendingApprovalStatistics, pendingApprovalAudit, pendingApprovalDetail } from '@/api/pendingApproval'
import feedback from '@/utils/feedback'
import { User } from '@element-plus/icons-vue'

const queryParams = reactive({
    type: 'all',
    order_by: 'create_time',
    order_dir: 'desc',
    page_no: 1,
    page_size: 15
})

const loading = ref(false)
const tableData = ref<any[]>([])
const total = ref(0)
const statistics = ref<any>({
    total: 0,
    distribution_apply: 0,
    merchant_apply: 0,
    article_audit: 0,
    withdraw_apply: 0
})

const showRejectDialog = ref(false)
const rejectReason = ref('')
const currentItem = ref<any>(null)

const showDetailDialog = ref(false)
const detailData = ref<any>(null)

const getTypeTagType = (type: string) => {
    const types: Record<string, string> = {
        'distribution_apply': 'success',
        'merchant_apply': 'warning',
        'article_audit': '',
        'withdraw_apply': 'danger'
    }
    return types[type] || 'info'
}

const getStatistics = async () => {
    try {
        const res = await pendingApprovalStatistics()
        statistics.value = res
    } catch (e) {
        console.error('获取统计失败', e)
    }
}

const getLists = async () => {
    loading.value = true
    try {
        const res = await pendingApprovalLists(queryParams)
        tableData.value = res.lists || []
        total.value = res.count || 0
    } catch (e) {
        console.error('获取列表失败', e)
    } finally {
        loading.value = false
    }
}

const handleTypeChange = (type: string) => {
    queryParams.type = type
    resetPage()
}

const resetPage = () => {
    queryParams.page_no = 1
    getLists()
}

const resetParams = () => {
    queryParams.type = 'all'
    queryParams.order_by = 'create_time'
    queryParams.order_dir = 'desc'
    queryParams.page_no = 1
    getLists()
}

const handleAudit = async (row: any, status: number) => {
    if (status === 2) {
        currentItem.value = row
        rejectReason.value = ''
        showRejectDialog.value = true
    } else {
        await feedback.confirm('确定批准该申请吗？')
        await doAudit(row, status)
    }
}

const doAudit = async (row: any, status: number, remark?: string) => {
    try {
        await pendingApprovalAudit({
            type: row.type,
            id: row.id,
            status: status,
            remark: remark || ''
        })
        feedback.msgSuccess(status === 1 ? '审批通过' : '已驳回')
        getLists()
        getStatistics()
    } catch (e: any) {
        feedback.msgError(e.msg || '审批失败')
    }
}

const confirmReject = async () => {
    if (!rejectReason.value.trim()) {
        feedback.msgWarning('请输入驳回原因')
        return
    }
    await doAudit(currentItem.value, 2, rejectReason.value)
    showRejectDialog.value = false
}

const handleDetail = async (row: any) => {
    try {
        const res = await pendingApprovalDetail({ type: row.type, id: row.id })
        detailData.value = {
            ...row,
            ...res,
            type_name: row.type_name
        }
        showDetailDialog.value = true
    } catch (e: any) {
        feedback.msgError(e.msg || '获取详情失败')
    }
}

const handleAuditFromDetail = async (status: number) => {
    if (status === 2) {
        currentItem.value = detailData.value
        rejectReason.value = ''
        showDetailDialog.value = false
        showRejectDialog.value = true
    } else {
        await feedback.confirm('确定批准该申请吗？')
        await doAudit(detailData.value, status)
        showDetailDialog.value = false
    }
}

onMounted(() => {
    getStatistics()
    getLists()
})
</script>

<style lang="scss" scoped>
.statistics-cards {
    .stat-card {
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        border: 2px solid transparent;

        &:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        &.active {
            border-color: var(--el-color-primary);
            background-color: var(--el-color-primary-light-9);
        }

        &.success {
            .stat-value {
                color: var(--el-color-success);
            }
        }

        &.warning {
            .stat-value {
                color: var(--el-color-warning);
            }
        }

        &.danger {
            .stat-value {
                color: var(--el-color-danger);
            }
        }

        &.primary {
            .stat-value {
                color: var(--el-color-primary);
            }
        }

        .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: var(--el-color-primary);
        }

        .stat-label {
            font-size: 14px;
            color: #666;
            margin-top: 8px;
        }
    }
}
</style>
