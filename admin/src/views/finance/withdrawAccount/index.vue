<template>
    <div class="withdraw-account">
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item label="关键词" prop="keyword">
                    <el-input class="w-[200px]" v-model="queryParams.keyword" clearable placeholder="账号/商户/用户" />
                </el-form-item>
                <el-form-item label="账户类型" prop="type">
                    <el-select class="w-[120px]" v-model="queryParams.type" clearable placeholder="全部">
                        <el-option label="支付宝" :value="2" />
                        <el-option label="银行卡" :value="3" />
                    </el-select>
                </el-form-item>
                <el-form-item label="来源" prop="source">
                    <el-select class="w-[120px]" v-model="queryParams.source" clearable placeholder="全部">
                        <el-option label="商户" :value="1" />
                        <el-option label="推广员" :value="2" />
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
                <el-table-column label="来源" width="100" align="center">
                    <template #default="{ row }">
                        <el-tag :type="row.source_text == '商户' ? 'success' : 'warning'">{{ row.source_text }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="账户持有人" min-width="150">
                    <template #default="{ row }">
                        <div v-if="row.merchant_id > 0">
                            <div class="font-medium">{{ row.merchant_name }}</div>
                            <div class="text-xs text-gray-400">商户</div>
                        </div>
                        <div v-else>
                            <div class="font-medium">{{ row.nickname }}</div>
                            <div class="text-xs text-gray-400">{{ row.user_mobile }}</div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="账户类型" prop="type_text" width="100" />
                <el-table-column label="账号" min-width="180">
                    <template #default="{ row }">
                        <div>{{ row.account }}</div>
                        <div class="text-xs text-gray-400">{{ row.real_name }}</div>
                    </template>
                </el-table-column>
                <el-table-column label="银行信息" min-width="180">
                    <template #default="{ row }">
                        <div v-if="row.type == 3">
                            <div>{{ row.bank_name }}</div>
                            <div class="text-xs text-gray-400">{{ row.bank_branch }}</div>
                        </div>
                        <div v-else class="text-gray-400">-</div>
                    </template>
                </el-table-column>
                <el-table-column label="默认" width="80" align="center">
                    <template #default="{ row }">
                        <el-tag v-if="row.is_default == 1" type="success" size="small">是</el-tag>
                        <span v-else class="text-gray-400">否</span>
                    </template>
                </el-table-column>
                <el-table-column label="状态" width="100" align="center">
                    <template #default="{ row }">
                        <el-switch
                            v-model="row.status"
                            :active-value="1"
                            :inactive-value="0"
                            @change="handleStatusChange(row)"
                        />
                    </template>
                </el-table-column>
                <el-table-column label="创建时间" prop="create_time" width="160" />
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>
    </div>
</template>

<script setup lang="ts">
import { withdrawAccountLists, withdrawAccountSetStatus } from '@/api/finance/withdrawAccount'
import { usePaging } from '@/hooks/usePaging'
import feedback from '@/utils/feedback'

const queryParams = reactive({
    keyword: '',
    type: '',
    source: ''
})

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: withdrawAccountLists,
    params: queryParams
})

const handleStatusChange = async (row: any) => {
    try {
        await withdrawAccountSetStatus({ id: row.id, status: row.status })
        feedback.msgSuccess('操作成功')
    } catch (e) {
        row.status = row.status == 1 ? 0 : 1
    }
}

getLists()
</script>
