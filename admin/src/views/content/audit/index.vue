<template>
    <div class="content-audit">
        <el-card shadow="never">
            <el-form :inline="true" :model="queryParams">
                <el-form-item label="状态">
                    <el-select v-model="queryParams.status">
                        <el-option label="全部" value="" />
                        <el-option label="待审核" :value="0" />
                        <el-option label="已通过" :value="1" />
                        <el-option label="已驳回" :value="2" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="handleQuery">查询</el-button>
                </el-form-item>
            </el-form>

            <el-table :data="tableData" v-loading="loading">
                <el-table-column prop="id" label="ID" width="80" />
                <el-table-column prop="title" label="标题" show-overflow-tooltip />
                <el-table-column prop="author" label="作者" width="120" />
                <el-table-column prop="create_time" label="提交时间" width="180" />
                <el-table-column prop="status" label="状态" width="100">
                    <template #default="{ row }">
                        <el-tag v-if="row.status === 0" type="warning">待审核</el-tag>
                        <el-tag v-else-if="row.status === 1" type="success">已通过</el-tag>
                        <el-tag v-else type="danger">已驳回</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="操作" width="200" fixed="right">
                    <template #default="{ row }">
                        <el-button link type="primary" @click="handlePreview(row)">查看</el-button>
                        <template v-if="row.status === 0">
                            <el-button link type="success" @click="handleAudit(row, 1)">通过</el-button>
                            <el-button link type="danger" @click="handleAudit(row, 2)">驳回</el-button>
                        </template>
                    </template>
                </el-table-column>
            </el-table>

            <div class="mt-4 flex justify-end">
                <el-pagination
                    v-model:current-page="queryParams.page_no"
                    v-model:page-size="queryParams.page_size"
                    layout="total, prev, pager, next"
                    :total="total"
                    @current-change="handleQuery"
                />
            </div>
        </el-card>

        <!-- 审核弹窗 -->
        <el-dialog v-model="auditVisible" title="审核处理" width="400px">
            <el-form>
                <el-form-item label="驳回原因" v-if="auditStatus === 2">
                    <el-input type="textarea" v-model="auditReason" />
                </el-form-item>
                <div v-else>确认通过该内容的审核吗？</div>
            </el-form>
            <template #footer>
                <el-button @click="auditVisible = false">取消</el-button>
                <el-button type="primary" @click="submitAudit">确定</el-button>
            </template>
        </el-dialog>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
// import { getArticleList, auditArticle } from '@/api/content'

const queryParams = reactive({
    status: '',
    page_no: 1,
    page_size: 10
})
const tableData = ref([])
const total = ref(0)
const loading = ref(false)
const auditVisible = ref(false)
const auditStatus = ref(1)
const auditReason = ref('')
const currentId = ref(0)

const handleQuery = async () => {
    loading.value = true
    // 模拟API
    setTimeout(() => {
        tableData.value = [
            { id: 1, title: '测试文章1', author: '张三', create_time: '2023-06-01', status: 0 },
            { id: 2, title: '测试文章2', author: '李四', create_time: '2023-06-02', status: 1 }
        ]
        total.value = 2
        loading.value = false
    }, 500)
}

const handleAudit = (row: any, status: number) => {
    currentId.value = row.id
    auditStatus.value = status
    auditVisible.value = true
}

const submitAudit = async () => {
    // await auditArticle({ id: currentId.value, status: auditStatus.value, reason: auditReason.value })
    auditVisible.value = false
    handleQuery()
}

const handlePreview = (row: any) => {
    // 预览逻辑
}

onMounted(() => {
    handleQuery()
})
</script>
