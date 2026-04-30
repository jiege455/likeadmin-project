<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item label="商户名称" prop="name">
                    <el-input class="w-[200px]" v-model="queryParams.name" clearable placeholder="请输入商户名称" />
                </el-form-item>
                <el-form-item label="手机号" prop="mobile">
                    <el-input class="w-[160px]" v-model="queryParams.mobile" clearable placeholder="请输入联系电话" />
                </el-form-item>
                <el-form-item label="状态" prop="status">
                    <el-select class="w-[140px]" v-model="queryParams.status" clearable placeholder="全部状态">
                        <el-option label="待审核" :value="0" />
                        <el-option label="正常" :value="1" />
                        <el-option label="已拒绝" :value="2" />
                        <el-option label="已禁用" :value="3" />
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
                <el-table-column label="商户信息" min-width="200">
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <el-avatar :size="40" :src="row.logo || row.avatar">
                                <el-icon><User /></el-icon>
                            </el-avatar>
                            <div class="ml-2">
                                <div class="font-medium">{{ row.name }}</div>
                                <div class="text-xs text-gray-400">{{ row.mobile || row.user_mobile || '-' }}</div>
                            </div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="文章数" prop="article_count" width="80" align="center" />
                <el-table-column label="订单数" prop="order_count" width="80" align="center" />
                <el-table-column label="累计收入" min-width="100" align="right">
                    <template #default="{ row }">
                        <span class="text-red-500">¥{{ row.total_income || '0.00' }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="余额" min-width="100" align="right">
                    <template #default="{ row }">
                        <span class="text-green-500">¥{{ row.money || '0.00' }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="状态" width="100" align="center">
                    <template #default="{ row }">
                        <el-tag v-if="row.status === 0" type="warning">待审核</el-tag>
                        <el-tag v-else-if="row.status === 1" type="success">正常</el-tag>
                        <el-tag v-else-if="row.status === 2" type="danger">已拒绝</el-tag>
                        <el-tag v-else type="info">已禁用</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="入驻时间" prop="create_time" width="160" />
                <el-table-column label="操作" width="200" fixed="right">
                    <template #default="{ row }">
                        <el-button v-perms="['merchant.merchant/detail']" type="primary" link>
                            <router-link :to="{ path: '/merchant/detail', query: { id: row.id } }">详情</router-link>
                        </el-button>
                        <el-button v-if="row.status === 0" v-perms="['merchant.merchant/audit']" type="success" link @click="handleAudit(row, 1)">通过</el-button>
                        <el-button v-if="row.status === 0" v-perms="['merchant.merchant/audit']" type="danger" link @click="handleAudit(row, 2)">拒绝</el-button>
                        <el-button v-if="row.status === 1" v-perms="['merchant.merchant/setStatus']" type="warning" link @click="handleSetStatus(row, 3)">禁用</el-button>
                        <el-button v-if="row.status === 3" v-perms="['merchant.merchant/setStatus']" type="success" link @click="handleSetStatus(row, 1)">启用</el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>

        <el-dialog v-model="showRejectDialog" title="拒绝原因" width="400px">
            <el-input v-model="rejectReason" type="textarea" :rows="3" placeholder="请输入拒绝原因" />
            <template #footer>
                <el-button @click="showRejectDialog = false">取消</el-button>
                <el-button type="primary" @click="confirmReject">确定</el-button>
            </template>
        </el-dialog>
    </div>
</template>
<script lang="ts" setup name="merchantLists">
import { merchantLists, merchantAudit, merchantSetStatus } from '@/api/merchant'
import { usePaging } from '@/hooks/usePaging'
import feedback from '@/utils/feedback'
import { User } from '@element-plus/icons-vue'

const queryParams = reactive({
    name: '',
    mobile: '',
    status: ''
})

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: merchantLists,
    params: queryParams
})

const showRejectDialog = ref(false)
const rejectReason = ref('')
const currentMerchant = ref<any>(null)

const handleAudit = async (row: any, status: number) => {
    if (status === 2) {
        currentMerchant.value = row
        rejectReason.value = ''
        showRejectDialog.value = true
    } else {
        await feedback.confirm('确定通过该商户的入驻申请吗？')
        await merchantAudit({ id: row.id, status })
        feedback.msgSuccess('审核通过')
        getLists()
    }
}

const confirmReject = async () => {
    if (!rejectReason.value.trim()) {
        return feedback.msgError('请输入拒绝原因')
    }
    await merchantAudit({ id: currentMerchant.value.id, status: 2, reason: rejectReason.value })
    feedback.msgSuccess('已拒绝')
    showRejectDialog.value = false
    getLists()
}

const handleSetStatus = async (row: any, status: number) => {
    const action = status === 3 ? '禁用' : '启用'
    await feedback.confirm(`确定要${action}该商户吗？`)
    await merchantSetStatus({ id: row.id, status })
    feedback.msgSuccess(`已${action}`)
    getLists()
}

onActivated(() => {
    getLists()
})

getLists()
</script>
