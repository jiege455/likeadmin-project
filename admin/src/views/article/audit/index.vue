<template>
    <div class="article-audit">
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item label="文章标题" prop="title">
                    <el-input class="w-[200px]" v-model="queryParams.title" clearable placeholder="请输入文章标题" />
                </el-form-item>
                <el-form-item label="审核状态" prop="audit_status">
                    <el-select class="w-[120px]" v-model="queryParams.audit_status" clearable placeholder="全部">
                        <el-option label="待审核" :value="0" />
                        <el-option label="已通过" :value="1" />
                        <el-option label="已拒绝" :value="2" />
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
                        <el-icon :size="24" color="#409EFF"><Document /></el-icon>
                    </div>
                    <div class="ml-3">
                        <div class="text-2xl font-bold">{{ stats.total }}</div>
                        <div class="text-sm text-gray-400">文章总数</div>
                    </div>
                </div>
            </el-card>
            <el-card class="!border-none" shadow="never">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                        <el-icon :size="24" color="#E6A23C"><Clock /></el-icon>
                    </div>
                    <div class="ml-3">
                        <div class="text-2xl font-bold text-orange-500">{{ stats.pending }}</div>
                        <div class="text-sm text-gray-400">待审核</div>
                    </div>
                </div>
            </el-card>
            <el-card class="!border-none" shadow="never">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                        <el-icon :size="24" color="#67C23A"><CircleCheck /></el-icon>
                    </div>
                    <div class="ml-3">
                        <div class="text-2xl font-bold text-green-500">{{ stats.passed }}</div>
                        <div class="text-sm text-gray-400">已通过</div>
                    </div>
                </div>
            </el-card>
            <el-card class="!border-none" shadow="never">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                        <el-icon :size="24" color="#F56C6C"><CircleClose /></el-icon>
                    </div>
                    <div class="ml-3">
                        <div class="text-2xl font-bold text-red-500">{{ stats.rejected }}</div>
                        <div class="text-sm text-gray-400">已拒绝</div>
                    </div>
                </div>
            </el-card>
        </div>

        <el-card class="!border-none mt-4" shadow="never">
            <div class="mb-4" v-if="selectedIds.length > 0">
                <el-button type="success" @click="handleBatchAudit(1)">批量通过</el-button>
                <el-button type="danger" @click="handleBatchAudit(2)">批量拒绝</el-button>
                <span class="ml-2 text-gray-500">已选择 {{ selectedIds.length }} 篇文章</span>
            </div>
            <el-table size="large" v-loading="pager.loading" :data="pager.lists" @selection-change="handleSelectionChange">
                <el-table-column type="selection" width="50" />
                <el-table-column label="ID" prop="id" width="80" />
                <el-table-column label="文章信息" min-width="250">
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <el-image :src="row.image" class="w-16 h-12 rounded" fit="cover">
                                <template #error>
                                    <div class="w-16 h-12 bg-gray-100 rounded flex items-center justify-center">
                                        <el-icon><Picture /></el-icon>
                                    </div>
                                </template>
                            </el-image>
                            <div class="ml-2">
                                <div class="font-medium line-clamp-1">{{ row.title }}</div>
                                <div class="text-xs text-gray-400 mt-1">{{ row.cate_name || '默认分类' }}</div>
                            </div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="商户" min-width="120">
                    <template #default="{ row }">
                        <span>{{ row.merchant_name || '-' }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="价格" width="100" align="right">
                    <template #default="{ row }">
                        <span v-if="row.price > 0" class="text-red-500">¥{{ row.price }}</span>
                        <span v-else class="text-green-500">免费</span>
                    </template>
                </el-table-column>
                <el-table-column label="审核状态" width="100" align="center">
                    <template #default="{ row }">
                        <el-tag :type="getAuditStatusType(row.audit_status)">{{ row.audit_status_text }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="提交时间" prop="create_time" width="160" />
                <el-table-column label="操作" width="200" fixed="right">
                    <template #default="{ row }">
                        <el-button type="primary" link @click="handleDetail(row)">查看</el-button>
                        <el-button v-if="row.audit_status === 0" type="success" link @click="handleAudit(row, 1)">通过</el-button>
                        <el-button v-if="row.audit_status === 0" type="danger" link @click="handleAudit(row, 2)">拒绝</el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>

        <!-- 详情弹窗 -->
        <el-dialog v-model="showDetail" title="文章详情" width="700px">
            <el-descriptions :column="2" border v-if="currentArticle">
                <el-descriptions-item label="文章标题" :span="2">{{ currentArticle.title }}</el-descriptions-item>
                <el-descriptions-item label="所属商户">{{ currentArticle.merchant_name || '-' }}</el-descriptions-item>
                <el-descriptions-item label="文章分类">{{ currentArticle.cate_name || '-' }}</el-descriptions-item>
                <el-descriptions-item label="文章价格">
                    <span v-if="currentArticle.price > 0" class="text-red-500">¥{{ currentArticle.price }}</span>
                    <span v-else class="text-green-500">免费</span>
                </el-descriptions-item>
                <el-descriptions-item label="审核状态">
                    <el-tag :type="getAuditStatusType(currentArticle.audit_status)">{{ currentArticle.audit_status_text }}</el-tag>
                </el-descriptions-item>
                <el-descriptions-item label="提交时间">{{ currentArticle.create_time }}</el-descriptions-item>
                <el-descriptions-item label="拒绝原因" v-if="currentArticle.audit_status === 2">
                    <span class="text-red-500">{{ currentArticle.audit_reason || '-' }}</span>
                </el-descriptions-item>
                <el-descriptions-item label="文章内容" :span="2">
                    <div class="max-h-60 overflow-auto whitespace-pre-wrap">{{ currentArticle.content }}</div>
                </el-descriptions-item>
            </el-descriptions>
            <template #footer v-if="currentArticle?.audit_status === 0">
                <el-button @click="showDetail = false">取消</el-button>
                <el-button type="danger" @click="handleAudit(currentArticle, 2)">拒绝</el-button>
                <el-button type="success" @click="handleAudit(currentArticle, 1)">通过</el-button>
            </template>
        </el-dialog>

        <!-- 拒绝原因弹窗 -->
        <el-dialog v-model="showReject" title="拒绝原因" width="400px">
            <el-input v-model="rejectReason" type="textarea" :rows="3" placeholder="请输入拒绝原因" />
            <template #footer>
                <el-button @click="showReject = false">取消</el-button>
                <el-button type="danger" @click="confirmReject">确定拒绝</el-button>
            </template>
        </el-dialog>
    </div>
</template>

<script setup lang="ts">
import { auditLists, auditDetail, auditArticle, batchAuditArticle, auditStatistics } from '@/api/article/audit'
import { usePaging } from '@/hooks/usePaging'
import feedback from '@/utils/feedback'
import { Document, Clock, CircleCheck, CircleClose, Picture } from '@element-plus/icons-vue'

const queryParams = reactive({
    title: '',
    audit_status: ''
})

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: auditLists,
    params: queryParams
})

const stats = ref({
    total: 0,
    pending: 0,
    passed: 0,
    rejected: 0
})

const selectedIds = ref<number[]>([])
const showDetail = ref(false)
const showReject = ref(false)
const rejectReason = ref('')
const currentArticle = ref<any>(null)

const getAuditStatusType = (status: number) => {
    const types: Record<number, string> = {
        0: 'warning',
        1: 'success',
        2: 'danger'
    }
    return types[status] || 'info'
}

const getStatistics = async () => {
    const res = await auditStatistics()
    stats.value = res
}

const handleSelectionChange = (selection: any[]) => {
    selectedIds.value = selection.map(item => item.id)
}

const handleDetail = async (row: any) => {
    const res = await auditDetail({ id: row.id })
    currentArticle.value = res
    showDetail.value = true
}

const handleAudit = async (row: any, status: number) => {
    if (status === 2) {
        currentArticle.value = row
        rejectReason.value = ''
        showReject.value = true
    } else {
        await feedback.confirm('确定通过该文章吗？')
        await auditArticle({ id: row.id, status })
        feedback.msgSuccess('审核通过')
        showDetail.value = false
        getLists()
        getStatistics()
    }
}

const confirmReject = async () => {
    await auditArticle({ id: currentArticle.value.id, status: 2, reason: rejectReason.value })
    feedback.msgSuccess('已拒绝')
    showReject.value = false
    showDetail.value = false
    getLists()
    getStatistics()
}

const handleBatchAudit = async (status: number) => {
    if (status === 2) {
        currentArticle.value = { id: selectedIds.value }
        rejectReason.value = ''
        showReject.value = true
    } else {
        await feedback.confirm(`确定批量通过 ${selectedIds.value.length} 篇文章吗？`)
        await batchAuditArticle({ ids: selectedIds.value, status })
        feedback.msgSuccess('批量审核成功')
        getLists()
        getStatistics()
    }
}

onMounted(() => {
    getStatistics()
})

getLists()
</script>
