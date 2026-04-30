<template>
    <div class="distribution-apply">
        <el-card class="!border-none" shadow="never">
            <el-alert
                type="info"
                title="分销申请说明"
                :closable="false"
                show-icon
            >
                <template #default>
                    <div>此页面管理用户提交的分销员申请，审核通过后用户将成为正式分销员。</div>
                    <div class="mt-1 text-gray-500">说明：分销员可获得下级用户消费的佣金奖励，与邀请管理的区别在于需要审核认证身份。</div>
                </template>
            </el-alert>
            <el-form ref="formRef" class="mb-[-16px] mt-[16px]" :model="queryParams" :inline="true">
                <el-form-item label="姓名" prop="name">
                    <el-input class="w-[150px]" v-model="queryParams.name" clearable placeholder="请输入姓名" />
                </el-form-item>
                <el-form-item label="手机号" prop="mobile">
                    <el-input class="w-[150px]" v-model="queryParams.mobile" clearable placeholder="请输入手机号" />
                </el-form-item>
                <el-form-item label="状态" prop="status">
                    <el-select class="w-[120px]" v-model="queryParams.status" clearable placeholder="全部">
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

        <el-card class="!border-none mt-4" shadow="never">
            <el-table size="large" v-loading="pager.loading" :data="pager.lists">
                <el-table-column label="ID" prop="id" width="80" />
                <el-table-column label="用户信息" min-width="150">
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <el-avatar :size="40" :src="row.avatar">
                                <el-icon><User /></el-icon>
                            </el-avatar>
                            <div class="ml-2">
                                <div class="font-medium">{{ row.nickname }}</div>
                                <div class="text-xs text-gray-400">{{ row.user_mobile }}</div>
                            </div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="姓名" prop="name" width="100" />
                <el-table-column label="手机号" prop="mobile" width="120" />
                <el-table-column label="申请理由" prop="reason" min-width="150" show-overflow-tooltip />
                <el-table-column label="状态" width="100" align="center">
                    <template #default="{ row }">
                        <el-tag :type="getStatusType(row.status)">{{ row.status_text }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="申请时间" prop="create_time" width="160" />
                <el-table-column label="操作" width="150" fixed="right">
                    <template #default="{ row }">
                        <el-button v-if="row.status === 0" type="success" link @click="handleAudit(row, 1)">通过</el-button>
                        <el-button v-if="row.status === 0" type="danger" link @click="handleAudit(row, 2)">拒绝</el-button>
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
import { distributionApplyLists, distributionApplyAudit } from '@/api/distribution/distributionApply'
import { usePaging } from '@/hooks/usePaging'
import feedback from '@/utils/feedback'
import { User } from '@element-plus/icons-vue'

const queryParams = reactive({
    name: '',
    mobile: '',
    status: ''
})

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: distributionApplyLists,
    params: queryParams
})

const showRejectDialog = ref(false)
const rejectReason = ref('')
const currentItem = ref<any>(null)

const getStatusType = (status: number) => {
    const types: Record<number, string> = {
        0: 'warning',
        1: 'success',
        2: 'danger'
    }
    return types[status] || 'info'
}

const handleAudit = async (row: any, status: number) => {
    if (status === 2) {
        currentItem.value = row
        rejectReason.value = ''
        showRejectDialog.value = true
    } else {
        await feedback.confirm('确定通过该申请吗？')
        await distributionApplyAudit({ id: row.id, status })
        feedback.msgSuccess('审核通过')
        getLists()
    }
}

const confirmReject = async () => {
    await distributionApplyAudit({ id: currentItem.value.id, status: 2, remark: rejectReason.value })
    feedback.msgSuccess('已拒绝')
    showRejectDialog.value = false
    getLists()
}

getLists()
</script>
