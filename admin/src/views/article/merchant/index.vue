<template>
    <div class="merchant-article-lists">
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item label="标题" prop="title">
                    <el-input class="w-[280px]" v-model="queryParams.title" clearable placeholder="请输入文章标题" />
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
                <el-table-column label="标题" prop="title" min-width="200" show-overflow-tooltip />
                <el-table-column label="所属商户" min-width="120">
                    <template #default="{ row }">
                        {{ row.merchant?.name || '-' }}
                    </template>
                </el-table-column>
                <el-table-column label="价格" prop="price" min-width="100" />
                <el-table-column label="审核状态" min-width="100">
                    <template #default="{ row }">
                        <el-tag v-if="row.audit_status == 0" type="warning">待审核</el-tag>
                        <el-tag v-else-if="row.audit_status == 1" type="success">已通过</el-tag>
                        <el-tag v-else type="danger">已拒绝</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="发布时间" prop="create_time" min-width="160" />
                <el-table-column label="操作" width="180" fixed="right">
                    <template #default="{ row }">
                        <el-button v-if="row.audit_status == 0" type="primary" link @click="handleAudit(row, 1)">通过</el-button>
                        <el-button v-if="row.audit_status == 0" type="danger" link @click="handleAudit(row, 2)">拒绝</el-button>
                        <el-button type="danger" link @click="handleDelete(row.id)">删除</el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>
    </div>
</template>

<script setup lang="ts">
import { merchantArticleLists, auditArticle, deleteArticle } from '@/api/merchantArticle'
import { usePaging } from '@/hooks/usePaging'
import feedback from '@/utils/feedback'

const queryParams = reactive({
    title: '',
    audit_status: ''
})

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: merchantArticleLists,
    params: queryParams
})

const handleAudit = async (row: any, status: number) => {
    await feedback.confirm(`确定要${status === 1 ? '通过' : '拒绝'}该文章吗？`)
    await auditArticle({ id: row.id, audit_status: status })
    feedback.msgSuccess('操作成功')
    getLists()
}

const handleDelete = async (id: number) => {
    await feedback.confirm('确定要删除该文章吗？')
    await deleteArticle({ id })
    feedback.msgSuccess('删除成功')
    getLists()
}

getLists()
</script>
