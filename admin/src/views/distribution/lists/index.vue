<template>
    <div class="distribution-lists">
        <el-card class="!border-none" shadow="never">
            <el-alert
                type="info"
                title="分销记录说明"
                :closable="false"
                show-icon
            >
                <template #default>
                    <div>此页面记录每一笔分销佣金的详细信息，包括推广员、下单用户、佣金金额和结算状态。</div>
                    <div class="mt-1 text-gray-500">说明：当被邀请的用户消费时，系统会自动计算佣金并记录到此列表，结算后佣金会发放到推广员账户。</div>
                </template>
            </el-alert>
            <el-form ref="formRef" class="mb-[-16px] mt-[16px]" :model="queryParams" :inline="true">
                <el-form-item label="推广员ID" prop="user_id">
                    <el-input class="w-[280px]" v-model="queryParams.user_id" clearable placeholder="请输入用户ID" />
                </el-form-item>
                <el-form-item label="状态" prop="status">
                    <el-select class="w-[280px]" v-model="queryParams.status">
                        <el-option label="全部" value="" />
                        <el-option label="待结算" :value="0" />
                        <el-option label="已结算" :value="1" />
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
                <el-table-column label="推广员" min-width="150">
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <el-avatar :size="30" :src="row.user?.avatar" />
                            <span class="ml-2">{{ row.user?.nickname }}</span>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="下单用户" min-width="150">
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <el-avatar :size="30" :src="row.source_user?.avatar" />
                            <span class="ml-2">{{ row.source_user?.nickname }}</span>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="佣金金额" prop="amount" min-width="120">
                    <template #default="{ row }">
                        <span class="text-error">+{{ row.amount }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="状态" prop="status_desc" min-width="100">
                     <template #default="{ row }">
                        <el-tag v-if="row.status == 1" type="success">已结算</el-tag>
                        <el-tag v-else type="warning">待结算</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="时间" prop="create_time" min-width="160" />
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>
    </div>
</template>

<script setup lang="ts">
import { distributionLists } from '@/api/distribution'
import { usePaging } from '@/hooks/usePaging'

const queryParams = reactive({
    user_id: '',
    status: ''
})

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: distributionLists,
    params: queryParams
})

getLists()
</script>
