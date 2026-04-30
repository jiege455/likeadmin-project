<template>
    <div class="invite-lists">
        <el-card class="!border-none" shadow="never">
            <el-alert
                type="info"
                title="邀请管理说明"
                :closable="false"
                show-icon
            >
                <template #default>
                    <div>此页面展示所有有邀请人的用户记录，用于查看用户之间的邀请关系和累计佣金情况。</div>
                    <div class="mt-1 text-gray-500">提示：用户列表页面也可查看邀请人信息，本页面侧重于快速筛选有邀请关系的用户。</div>
                </template>
            </el-alert>
            <el-form ref="formRef" class="mb-[-16px] mt-[16px]" :model="queryParams" :inline="true">
                <el-form-item label="用户信息" prop="keyword">
                    <el-input class="w-[280px]" v-model="queryParams.keyword" clearable placeholder="请输入用户昵称/编号/手机号" />
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
                <el-table-column label="用户" min-width="150">
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <el-avatar :size="30" :src="row.avatar" />
                            <div class="ml-2">
                                <div>{{ row.nickname }}</div>
                                <div class="text-xs text-gray-400">{{ row.sn }}</div>
                            </div>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="邀请人" min-width="150">
                    <template #default="{ row }">
                        <div class="flex items-center" v-if="row.inviter">
                            <el-avatar :size="30" :src="row.inviter.avatar" />
                            <div class="ml-2">
                                <div>{{ row.inviter.nickname }}</div>
                                <div class="text-xs text-gray-400">{{ row.inviter.sn }}</div>
                            </div>
                        </div>
                        <span v-else>-</span>
                    </template>
                </el-table-column>
                <el-table-column label="累计佣金" prop="total_commission" min-width="120" />
                <el-table-column label="注册时间" prop="create_time" min-width="160" />
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>
    </div>
</template>

<script setup lang="ts">
import { inviteLists } from '@/api/user'
import { usePaging } from '@/hooks/usePaging'

const queryParams = reactive({
    keyword: ''
})

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: inviteLists,
    params: queryParams
})

getLists()
</script>
