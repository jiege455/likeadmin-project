<template>
<!--
  开发者公众号：杰哥网络科技
  QQ: 2711793818 杰哥
  聊天室管理页面
-->
<div class="chat-room-lists">
    <el-card class="!border-none" shadow="never">
        <el-form :inline="true" :model="queryParams" class="mb-4">
            <el-form-item label="聊天室名称">
                <el-input v-model="queryParams.name" placeholder="请输入聊天室名称" clearable @keyup.enter="resetPage" />
            </el-form-item>
            <el-form-item label="状态">
                <el-select v-model="queryParams.status" placeholder="请选择状态" clearable>
                    <el-option label="启用" :value="1" />
                    <el-option label="禁用" :value="0" />
                </el-select>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="resetPage">查询</el-button>
                <el-button @click="resetQuery">重置</el-button>
            </el-form-item>
        </el-form>
        <div class="mb-4">
            <el-button type="primary" @click="handleAdd">添加聊天室</el-button>
        </div>
        <el-table size="large" v-loading="pager.loading" :data="pager.lists">
            <el-table-column label="ID" prop="id" width="80" />
            <el-table-column label="聊天室名称" prop="name" min-width="120" />
            <el-table-column label="聊天室ID" prop="room_id" min-width="100" />
            <el-table-column label="描述" prop="description" min-width="200" show-overflow-tooltip />
            <el-table-column label="最大用户数" prop="max_users" width="100" />
            <el-table-column label="公开状态" width="100">
                <template #default="{ row }">
                    <el-tag v-if="row.is_public == 1" type="success">公开</el-tag>
                    <el-tag v-else type="info">私密</el-tag>
                </template>
            </el-table-column>
            <el-table-column label="状态" width="100">
                <template #default="{ row }">
                    <el-switch
                        v-model="row.status"
                        :active-value="1"
                        :inactive-value="0"
                        @change="handleStatusChange(row)"
                    />
                </template>
            </el-table-column>
            <el-table-column label="创建时间" prop="create_time" width="160" />
            <el-table-column label="操作" width="150" fixed="right">
                <template #default="{ row }">
                    <el-button type="primary" link @click="handleEdit(row)">编辑</el-button>
                    <el-button type="danger" link @click="handleDelete(row.id)" v-if="row.id != 1">删除</el-button>
                </template>
            </el-table-column>
        </el-table>
        <div class="flex justify-end mt-4">
            <pagination v-model="pager" @change="getLists" />
        </div>
    </el-card>

    <popup
        ref="popupRef"
        :title="formData.id ? '编辑聊天室' : '添加聊天室'"
        :async="true"
        width="500px"
        @confirm="handleSubmit"
    >
        <el-form ref="formRef" :model="formData" :rules="formRules" label-width="100px">
            <el-form-item label="聊天室名称" prop="name">
                <el-input v-model="formData.name" placeholder="请输入聊天室名称" />
            </el-form-item>
            <el-form-item label="聊天室ID" prop="room_id" v-if="!formData.id">
                <el-input v-model="formData.room_id" placeholder="请输入聊天室ID（英文）" />
            </el-form-item>
            <el-form-item label="描述" prop="description">
                <el-input v-model="formData.description" type="textarea" :rows="3" placeholder="请输入描述" />
            </el-form-item>
            <el-form-item label="最大用户数" prop="max_users">
                <el-input-number v-model="formData.max_users" :min="1" :max="100000" />
            </el-form-item>
            <el-form-item label="公开状态" prop="is_public">
                <el-radio-group v-model="formData.is_public">
                    <el-radio :label="1">公开</el-radio>
                    <el-radio :label="0">私密</el-radio>
                </el-radio-group>
            </el-form-item>
        </el-form>
    </popup>
</div>
</template>

<script setup lang="ts">
import { chatRoomLists, addChatRoom, editChatRoom, deleteChatRoom, changeChatRoomStatus } from '@/api/chat'
import { usePaging } from '@/hooks/usePaging'
import feedback from '@/utils/feedback'
import Popup from '@/components/popup/index.vue'
import type { FormInstance, FormRules } from 'element-plus'

const queryParams = reactive({
    name: '',
    status: ''
})

const { pager, getLists, resetPage, resetQuery } = usePaging({
    fetchFun: chatRoomLists,
    params: queryParams
})

const popupRef = ref<InstanceType<typeof Popup>>()
const formRef = ref<FormInstance>()
const formData = reactive({
    id: '',
    name: '',
    room_id: '',
    description: '',
    max_users: 1000,
    is_public: 1
})

const formRules: FormRules = {
    name: [{ required: true, message: '请输入聊天室名称', trigger: 'blur' }],
    room_id: [{ required: true, message: '请输入聊天室ID', trigger: 'blur' }]
}

const handleAdd = () => {
    formData.id = ''
    formData.name = ''
    formData.room_id = ''
    formData.description = ''
    formData.max_users = 1000
    formData.is_public = 1
    popupRef.value?.open()
}

const handleEdit = (row: any) => {
    formData.id = row.id
    formData.name = row.name
    formData.room_id = row.room_id
    formData.description = row.description
    formData.max_users = row.max_users
    formData.is_public = row.is_public
    popupRef.value?.open()
}

const handleSubmit = async () => {
    await formRef.value?.validate()
    if (formData.id) {
        await editChatRoom(formData)
        feedback.msgSuccess('编辑成功')
    } else {
        await addChatRoom(formData)
        feedback.msgSuccess('添加成功')
    }
    popupRef.value?.close()
    getLists()
}

const handleDelete = async (id: number) => {
    await feedback.confirm('确定要删除该聊天室吗？')
    await deleteChatRoom({ id })
    feedback.msgSuccess('删除成功')
    getLists()
}

const handleStatusChange = async (row: any) => {
    try {
        await changeChatRoomStatus({ id: row.id, status: row.status })
        feedback.msgSuccess('操作成功')
    } catch (e) {
        row.status = row.status == 1 ? 0 : 1
    }
}

getLists()
</script>
