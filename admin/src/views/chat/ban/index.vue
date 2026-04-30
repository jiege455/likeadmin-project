<template>
<!--
  开发者公众号：杰哥网络科技
  QQ: 2711793818 杰哥
  禁言管理页面
-->
<div class="chat-ban-lists">
    <el-card class="!border-none" shadow="never">
        <el-form :inline="true" :model="queryParams" class="mb-4">
            <el-form-item label="用户类型">
                <el-select v-model="queryParams.user_type" placeholder="请选择" clearable>
                    <el-option label="普通用户" :value="1" />
                    <el-option label="商家" :value="2" />
                </el-select>
            </el-form-item>
            <el-form-item label="禁言类型">
                <el-select v-model="queryParams.ban_type" placeholder="请选择" clearable>
                    <el-option label="私聊禁言" :value="1" />
                    <el-option label="公共聊天禁言" :value="2" />
                    <el-option label="全部禁言" :value="3" />
                </el-select>
            </el-form-item>
            <el-form-item label="状态">
                <el-select v-model="queryParams.status" placeholder="请选择" clearable>
                    <el-option label="禁言中" :value="1" />
                    <el-option label="已解除" :value="0" />
                </el-select>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="resetPage">查询</el-button>
                <el-button @click="resetQuery">重置</el-button>
            </el-form-item>
        </el-form>
        <div class="mb-4">
            <el-button type="primary" @click="handleAdd">添加禁言</el-button>
        </div>
        <el-table size="large" v-loading="pager.loading" :data="pager.lists">
            <el-table-column label="ID" prop="id" width="80" />
            <el-table-column label="被禁言用户" min-width="150">
                <template #default="{ row }">
                    <div class="flex items-center" v-if="row.user_info">
                        <el-avatar :size="32" :src="row.user_info.avatar || row.user_info.logo" class="mr-2" />
                        <span>{{ row.user_info.nickname || row.user_info.name }}</span>
                        <span class="text-gray-400 text-xs ml-2">(ID:{{ row.user_id }})</span>
                    </div>
                    <span v-else>用户ID: {{ row.user_id }}</span>
                </template>
            </el-table-column>
            <el-table-column label="用户类型" width="100">
                <template #default="{ row }">
                    <el-tag :type="row.user_type === 1 ? 'primary' : 'warning'">
                        {{ row.user_type_text }}
                    </el-tag>
                </template>
            </el-table-column>
            <el-table-column label="禁言类型" width="120">
                <template #default="{ row }">
                    <el-tag type="danger">{{ row.ban_type_text }}</el-tag>
                </template>
            </el-table-column>
            <el-table-column label="禁言原因" prop="reason" min-width="200" show-overflow-tooltip />
            <el-table-column label="到期时间" width="120">
                <template #default="{ row }">
                    <span>{{ row.expire_time }}</span>
                </template>
            </el-table-column>
            <el-table-column label="状态" width="100">
                <template #default="{ row }">
                    <el-tag :type="row.status === 1 ? 'danger' : 'success'">
                        {{ row.status_text }}
                    </el-tag>
                </template>
            </el-table-column>
            <el-table-column label="禁言时间" prop="create_time" width="160" />
            <el-table-column label="操作" width="100" fixed="right">
                <template #default="{ row }">
                    <el-button 
                        v-if="row.status === 1" 
                        type="primary" 
                        link 
                        @click="handleCancel(row.id)"
                    >
                        解除
                    </el-button>
                    <span v-else class="text-gray-400">已解除</span>
                </template>
            </el-table-column>
        </el-table>
        <div class="flex justify-end mt-4">
            <pagination v-model="pager" @change="getLists" />
        </div>
    </el-card>

    <!-- 添加禁言弹窗 -->
    <el-dialog v-model="showAddDialog" title="添加禁言" width="500px">
        <el-form :model="addForm" label-width="100px">
            <el-form-item label="用户类型" required>
                <el-select v-model="addForm.user_type" placeholder="请选择用户类型">
                    <el-option label="普通用户" :value="1" />
                    <el-option label="商家" :value="2" />
                </el-select>
            </el-form-item>
            <el-form-item label="用户ID" required>
                <el-input v-model="addForm.user_id" placeholder="请输入用户ID" type="number" />
            </el-form-item>
            <el-form-item label="禁言类型" required>
                <el-select v-model="addForm.ban_type" placeholder="请选择禁言类型">
                    <el-option label="私聊禁言" :value="1" />
                    <el-option label="公共聊天禁言" :value="2" />
                    <el-option label="全部禁言" :value="3" />
                </el-select>
            </el-form-item>
            <el-form-item label="禁言原因" required>
                <el-input 
                    v-model="addForm.reason" 
                    type="textarea" 
                    :rows="3" 
                    placeholder="请输入禁言原因"
                    maxlength="200"
                    show-word-limit
                />
            </el-form-item>
            <el-form-item label="禁言时长">
                <el-select v-model="addForm.duration" placeholder="请选择禁言时长">
                    <el-option label="1小时" :value="1" />
                    <el-option label="6小时" :value="6" />
                    <el-option label="12小时" :value="12" />
                    <el-option label="24小时" :value="24" />
                    <el-option label="3天" :value="72" />
                    <el-option label="7天" :value="168" />
                    <el-option label="30天" :value="720" />
                    <el-option label="永久" :value="0" />
                </el-select>
            </el-form-item>
        </el-form>
        <template #footer>
            <el-button @click="showAddDialog = false">取消</el-button>
            <el-button type="primary" @click="submitAdd" :loading="addLoading">确定</el-button>
        </template>
    </el-dialog>
</div>
</template>

<script setup lang="ts">
import { chatBanLists, addChatBan, cancelChatBan } from '@/api/chat'
import { usePaging } from '@/hooks/usePaging'
import feedback from '@/utils/feedback'

const queryParams = reactive({
    user_type: '',
    ban_type: '',
    status: ''
})

const { pager, getLists, resetPage, resetQuery } = usePaging({
    fetchFun: chatBanLists,
    params: queryParams
})

const showAddDialog = ref(false)
const addLoading = ref(false)
const addForm = reactive({
    user_type: 1,
    user_id: '',
    ban_type: 1,
    reason: '',
    duration: 24
})

const handleAdd = () => {
    addForm.user_type = 1
    addForm.user_id = ''
    addForm.ban_type = 1
    addForm.reason = ''
    addForm.duration = 24
    showAddDialog.value = true
}

const submitAdd = async () => {
    if (!addForm.user_id) {
        feedback.msgError('请输入用户ID')
        return
    }
    if (!addForm.reason) {
        feedback.msgError('请输入禁言原因')
        return
    }
    
    addLoading.value = true
    try {
        await addChatBan({
            user_id: parseInt(addForm.user_id),
            user_type: addForm.user_type,
            ban_type: addForm.ban_type,
            reason: addForm.reason,
            duration: addForm.duration
        })
        feedback.msgSuccess('添加成功')
        showAddDialog.value = false
        getLists()
    } catch (e) {
        console.error(e)
    } finally {
        addLoading.value = false
    }
}

const handleCancel = async (id: number) => {
    await feedback.confirm('确定要解除该用户的禁言吗？')
    await cancelChatBan({ id })
    feedback.msgSuccess('解除成功')
    getLists()
}

getLists()
</script>
