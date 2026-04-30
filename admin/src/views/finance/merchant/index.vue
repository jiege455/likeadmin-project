<template>
    <div class="merchant-finance-lists">
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item label="商户ID" prop="merchant_id">
                    <el-input class="w-[280px]" v-model="queryParams.merchant_id" clearable placeholder="请输入商户ID" />
                </el-form-item>
                <el-form-item label="来源类型" prop="source_type">
                    <el-select class="w-[280px]" v-model="queryParams.source_type">
                        <el-option label="全部" value="" />
                        <el-option label="文章" :value="1" />
                        <el-option label="课程" :value="2" />
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
                <el-table-column label="ID" prop="id" min-width="80" />
                <el-table-column label="商户名称" min-width="120">
                    <template #default="{ row }">
                        {{ row.merchant?.name || '-' }}
                    </template>
                </el-table-column>
                <el-table-column label="变动金额" prop="amount" min-width="120">
                    <template #default="{ row }">
                        <span class="text-success">+{{ row.amount }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="平台抽成" prop="platform_ratio" min-width="100">
                    <template #default="{ row }">
                        {{ row.platform_ratio }}%
                    </template>
                </el-table-column>
                <el-table-column label="来源" prop="source_type_desc" min-width="100" />
                <el-table-column label="备注" prop="remark" min-width="200" />
                <el-table-column label="时间" prop="create_time" min-width="160" />
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>
    </div>
</template>

<script setup lang="ts">
import { merchantFinanceLists } from '@/api/finance'
import { usePaging } from '@/hooks/usePaging'

const queryParams = reactive({
    merchant_id: '',
    source_type: ''
})

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: merchantFinanceLists,
    params: queryParams
})

getLists()
</script>
