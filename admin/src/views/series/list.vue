<!--
    系列管理列表页
    开发者：杰哥网络科技
    QQ：2711793818 杰哥
-->
<template>
    <div class="series-lists">
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item label="系列名称" prop="keyword">
                    <el-input class="w-[200px]" v-model="queryParams.keyword" clearable placeholder="请输入系列名称" />
                </el-form-item>
                <el-form-item label="彩票类型" prop="lottery_type">
                    <el-select class="w-[120px]" v-model="queryParams.lottery_type" clearable placeholder="全部">
                        <el-option label="福彩3D" value="fc3d" />
                        <el-option label="排列三" value="pl3" />
                        <el-option label="双色球" value="ssq" />
                        <el-option label="大乐透" value="dlt" />
                    </el-select>
                </el-form-item>
                <el-form-item label="状态" prop="series_status">
                    <el-select class="w-[120px]" v-model="queryParams.series_status" clearable placeholder="全部">
                        <el-option label="上架" :value="1" />
                        <el-option label="下架" :value="0" />
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
                添加系列
            </el-button>

            <el-table size="large" v-loading="pager.loading" :data="pager.lists">
                <el-table-column label="ID" prop="id" width="80" />
                <el-table-column label="系列名称" prop="name" min-width="150" />
                <el-table-column label="彩票类型" width="100" align="center">
                    <template #default="{ row }">
                        <el-tag>{{ getLotteryTypeText(row.lottery_type) }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="期次" width="100" align="center">
                    <template #default="{ row }">
                        {{ row.published_issues || 0 }}/{{ row.total_issues || 0 }}
                    </template>
                </el-table-column>
                <el-table-column label="状态" width="100" align="center">
                    <template #default="{ row }">
                        <el-switch
                            v-model="row.series_status"
                            :active-value="1"
                            :inactive-value="0"
                            @change="handleStatusChange(row)"
                        />
                    </template>
                </el-table-column>
                <el-table-column label="创建时间" prop="create_time" width="160" />
                <el-table-column label="操作" width="250" fixed="right">
                    <template #default="{ row }">
                        <el-button type="primary" link @click="handleEdit(row)">编辑</el-button>
                        <el-button
                            v-perms="['series.Issue/lists']"
                            type="success"
                            link
                            @click="handleIssue(row)"
                        >期次</el-button>
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
import { seriesLists, seriesDelete, seriesStatus } from '@/api/series/series'
import { usePaging } from '@/hooks/usePaging'
import feedback from '@/utils/feedback'
import { Plus } from '@element-plus/icons-vue'
import EditPopup from './edit.vue'

const router = useRouter()

const queryParams = reactive({
    keyword: '',
    lottery_type: '',
    series_status: ''
})

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: seriesLists,
    params: queryParams
})

const editRef = ref()

const getLotteryTypeText = (type: string) => {
    const texts: Record<string, string> = {
        fc3d: '福彩3D',
        pl3: '排列三',
        ssq: '双色球',
        dlt: '大乐透'
    }
    return texts[type] || type
}

const handleAdd = () => {
    editRef.value?.open()
}

const handleEdit = (row: any) => {
    editRef.value?.open(row.id)
}

const handleIssue = (row: any) => {
    router.push({
        path: '/series/issue',
        query: { series_id: row.id }
    })
}

const handleDelete = async (row: any) => {
    await feedback.confirm('确定要删除该系列吗？')
    await seriesDelete({ id: row.id })
    feedback.msgSuccess('删除成功')
    getLists()
}

const handleStatusChange = async (row: any) => {
    try {
        await seriesStatus({ id: row.id, status: row.series_status })
        feedback.msgSuccess('操作成功')
    } catch (e) {
        row.series_status = row.series_status == 1 ? 0 : 1
    }
}

getLists()
</script>
