<template>
    <el-card shadow="never" class="!border-none">
        <el-form class="mb-[-16px]" :model="queryParams" :inline="true">
            <el-form-item label="用户信息">
                <el-input v-model="queryParams.keyword" placeholder="姓名/身份证/手机号" clearable @keyup.enter="resetPage" />
            </el-form-item>
            <el-form-item label="状态">
                <el-select v-model="queryParams.status">
                    <el-option label="全部" value="" />
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

        <el-table size="large" class="mt-4" v-loading="pager.loading" :data="pager.lists">
            <el-table-column label="ID" prop="id" width="80" />
            <el-table-column label="真实姓名" prop="real_name" min-width="100" />
            <el-table-column label="身份证号" prop="id_card" min-width="180" />
            <el-table-column label="手机号" prop="mobile" min-width="120" />
            <el-table-column label="状态" prop="status_desc" min-width="100">
                <template #default="{ row }">
                    <el-tag v-if="row.status == 0" type="warning">{{ row.status_desc }}</el-tag>
                    <el-tag v-else-if="row.status == 1" type="success">{{ row.status_desc }}</el-tag>
                    <el-tag v-else type="danger">{{ row.status_desc }}</el-tag>
                </template>
            </el-table-column>
            <el-table-column label="提交时间" prop="create_time" min-width="160" />
            <el-table-column label="拒绝原因" prop="fail_reason" min-width="150" show-overflow-tooltip />
            <el-table-column label="操作" width="150" fixed="right">
                <template #default="{ row }">
                    <el-button v-if="row.status == 0" type="primary" link @click="handleAudit(row, 1)">通过</el-button>
                    <el-button v-if="row.status == 0" type="danger" link @click="handleAudit(row, 2)">拒绝</el-button>
                </template>
            </el-table-column>
        </el-table>

        <div class="flex justify-end mt-4">
            <pagination v-model="pager" @change="getLists" />
        </div>
    </el-card>
</template>

<script setup lang="ts">
import { reactive } from 'vue'
import { realnameLists, realnameAudit } from '@/api/user_realname'
import { usePaging } from '@/hooks/usePaging'
import feedback from '@/utils/feedback'

const queryParams = reactive({
    keyword: '',
    status: ''
})

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: realnameLists,
    params: queryParams
})

const handleAudit = async (row: any, status: number) => {
    if (status == 1) {
        await feedback.confirm('确认通过审核吗？')
        await realnameAudit({ id: row.id, status: 1 })
        feedback.msgSuccess('操作成功')
        getLists()
    } else {
        const { value } = await feedback.prompt('请输入拒绝原因', '拒绝审核')
        await realnameAudit({ id: row.id, status: 2, fail_reason: value })
        feedback.msgSuccess('操作成功')
        getLists()
    }
}

getLists()
</script>
