<!--
  投诉管理页面
  开发者：杰哥网络科技。
  qq2711793818 杰哥
-->
<template>
    <div class="merchant-complaint">
        <el-card shadow="never" class="!border-none">
            <el-form :model="formData" class="mb-[-16px]" inline>
                <el-form-item label="举报类型">
                    <el-select v-model="formData.type" class="w-[150px]" clearable>
                        <el-option label="商家" :value="1" />
                        <el-option label="文章" :value="2" />
                    </el-select>
                </el-form-item>
                <el-form-item label="举报用户">
                    <el-input v-model="formData.user_id" placeholder="请输入用户ID" clearable />
                </el-form-item>
                <el-form-item label="被诉商家">
                    <el-input v-model="formData.merchant_id" placeholder="请输入商家ID" clearable />
                </el-form-item>
                <el-form-item label="处理状态">
                    <el-select v-model="formData.status" class="w-[150px]" clearable>
                        <el-option label="待处理" :value="0" />
                        <el-option label="已处理" :value="1" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                </el-form-item>
            </el-form>
        </el-card>

        <el-card shadow="never" class="!border-none mt-4">
            <el-table size="large" v-loading="pager.loading" :data="pager.lists">
                <el-table-column label="ID" prop="id" width="80" />
                <el-table-column label="举报类型" width="100">
                    <template #default="{ row }">
                        <el-tag :type="row.type == 2 ? 'success' : 'primary'" size="small">
                            {{ row.type_text || (row.type == 2 ? '文章' : '商家') }}
                        </el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="举报用户" min-width="150">
                    <template #default="{ row }">
                        <div class="flex items-center">
                            <el-avatar :src="row.user_avatar" :size="40" />
                            <span class="ml-2">{{ row.user_nickname }} ({{ row.user_id }})</span>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="举报对象" min-width="150">
                    <template #default="{ row }">
                        <span v-if="row.type == 2">文章ID: {{ row.target_id }}</span>
                        <span v-else>{{ row.merchant_name }} ({{ row.merchant_id }})</span>
                    </template>
                </el-table-column>
                <el-table-column label="举报原因" prop="reason" width="120">
                    <template #default="{ row }">
                        <span>{{ row.reason || '-' }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="详细说明" prop="content" min-width="200" show-overflow-tooltip />
                <el-table-column label="图片凭证" width="120">
                    <template #default="{ row }">
                        <div class="flex" v-if="row.images && row.images.length">
                             <el-image
                                v-for="(img, idx) in row.images"
                                :key="idx"
                                :src="img"
                                :preview-src-list="row.images"
                                class="w-[40px] h-[40px] mr-1"
                            />
                        </div>
                        <span v-else>-</span>
                    </template>
                </el-table-column>
                <el-table-column label="联系方式" prop="contact" width="120" />
                <el-table-column label="状态" width="100">
                    <template #default="{ row }">
                        <el-tag :type="row.status ? 'success' : 'warning'">{{ row.status ? '已处理' : '待处理' }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="提交时间" prop="create_time" width="180" />
                <el-table-column label="操作" width="120" fixed="right">
                    <template #default="{ row }">
                        <el-button
                            v-if="row.status == 0"
                            type="primary"
                            link
                            @click="handleProcess(row)"
                            v-perms="['merchant:complaint:handle']"
                        >
                            处理
                        </el-button>
                        <el-button
                            type="danger"
                            link
                            @click="handleDelete(row.id)"
                            v-perms="['merchant:complaint:del']"
                        >
                            删除
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>
    </div>
</template>

<script lang="ts" setup name="merchantComplaint">
import { usePaging } from '@/hooks/usePaging'
import request from '@/utils/request'
import { ElMessage, ElMessageBox } from 'element-plus'

const formData = reactive({
    type: '',
    user_id: '',
    merchant_id: '',
    status: ''
})

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: (params) => request.get({ url: '/merchant.complaint/lists', params }),
    params: formData
})

const handleProcess = (row: any) => {
    ElMessageBox.confirm('确定标记为已处理吗？', '提示', {
        type: 'warning'
    }).then(async () => {
        await request.post({ url: '/merchant.complaint/handle', params: { id: row.id } })
        ElMessage.success('操作成功')
        getLists()
    })
}

const handleDelete = (id: number) => {
    ElMessageBox.confirm('确定删除该记录吗？', '提示', {
        type: 'warning'
    }).then(async () => {
        await request.post({ url: '/merchant.complaint/del', params: { id } })
        ElMessage.success('删除成功')
        getLists()
    })
}

getLists()
</script>
