<!--
    期次管理列表页
    开发者：杰哥网络科技
    QQ：2711793818 杰哥
-->
<template>
    <div class="issue-lists">
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item label="期号" prop="issue_no">
                    <el-input class="w-[200px]" v-model="queryParams.issue_no" clearable placeholder="请输入期号" />
                </el-form-item>
                <el-form-item label="状态" prop="issue_status">
                    <el-select class="w-[120px]" v-model="queryParams.issue_status" clearable placeholder="全部">
                        <el-option label="草稿" :value="0" />
                        <el-option label="已发布" :value="1" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                </el-form-item>
            </el-form>
        </el-card>

        <el-card class="!border-none mt-4" shadow="never">
            <el-button type="primary" class="mb-4" @click="handleAdd">
                <el-icon class="mr-1"><Plus /></el-icon>
                添加期次
            </el-button>

            <el-table size="large" v-loading="pager.loading" :data="pager.lists">
                <el-table-column label="ID" prop="id" width="80" />
                <el-table-column label="期号" prop="issue_no" width="120" />
                <el-table-column label="系列" prop="series_name" width="150" />
                <el-table-column label="标题" prop="title" min-width="200" />
                <el-table-column label="状态" width="100" align="center">
                    <template #default="{ row }">
                        <el-tag :type="getStatusType(row.issue_status)">
                            {{ getStatusText(row.issue_status) }}
                        </el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="创建时间" prop="create_time" width="160" />
                <el-table-column label="操作" width="200" fixed="right">
                    <template #default="{ row }">
                        <el-button type="primary" link @click="handleEdit(row)">编辑</el-button>
                        <el-button type="success" link @click="handlePublish(row)" v-if="row.issue_status == 0">发布</el-button>
                        <el-button type="danger" link @click="handleDelete(row)">删除</el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>

        <edit-popup ref="editRef" @success="getLists" />
    </div>
</template>

<script setup lang="ts">
import { issueLists, issueDelete, issuePublish } from '@/api/series/issue'
import { usePaging } from '@/hooks/usePaging'
import feedback from '@/utils/feedback'
import { Plus } from '@element-plus/icons-vue'
import EditPopup from './edit.vue'

const route = useRoute()

const queryParams = reactive({
    series_id: '',
    issue_no: '',
    issue_status: ''
})

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: issueLists,
    params: queryParams
})

const editRef = ref()

const getStatusType = (status: number) => {
    const types: Record<number, string> = {
        0: 'info',
        1: 'success'
    }
    return types[status] || 'info'
}

const getStatusText = (status: number) => {
    const texts: Record<number, string> = {
        0: '草稿',
        1: '已发布'
    }
    return texts[status] || '未知'
}

const handleAdd = () => {
    editRef.value?.open()
}

const handleEdit = (row: any) => {
    editRef.value?.open(row.id)
}

const handlePublish = async (row: any) => {
    try {
        await issuePublish({ id: row.id })
        feedback.msgSuccess('发布成功')
        getLists()
    } catch (e) {
        feedback.msgError('发布失败')
    }
}

const handleDelete = async (row: any) => {
    await feedback.confirm('确定要删除该期次吗？')
    try {
        await issueDelete({ id: row.id })
        feedback.msgSuccess('删除成功')
        getLists()
    } catch (e) {
        feedback.msgError('删除失败')
    }
}

onMounted(() => {
    queryParams.series_id = route.query.series_id as string
    getLists()
})
</script>
