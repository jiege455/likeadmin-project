<template>
<!--
  开发者公众号：杰哥网络科技
  QQ: 2711793818 杰哥
  违禁词管理页面
-->
<div class="banned-word-lists">
    <el-card class="!border-none" shadow="never">
        <el-form :inline="true" :model="queryParams" class="mb-4">
            <el-form-item label="违禁词">
                <el-input v-model="queryParams.word" placeholder="请输入违禁词" clearable @keyup.enter="resetPage" />
            </el-form-item>
            <el-form-item label="类型">
                <el-select v-model="queryParams.type" placeholder="请选择类型" clearable>
                    <el-option label="违禁词" :value="1" />
                    <el-option label="敏感词" :value="2" />
                </el-select>
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
            <el-button type="primary" @click="handleAdd">添加违禁词</el-button>
        </div>
        <el-table size="large" v-loading="pager.loading" :data="pager.lists">
            <el-table-column label="ID" prop="id" width="80" />
            <el-table-column label="违禁词" prop="word" min-width="150" />
            <el-table-column label="类型" width="100">
                <template #default="{ row }">
                    <el-tag :type="row.type == 1 ? 'danger' : 'warning'">{{ row.type_text }}</el-tag>
                </template>
            </el-table-column>
            <el-table-column label="替换词" prop="replace_word" min-width="150">
                <template #default="{ row }">
                    {{ row.replace_word || '直接拦截' }}
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
                    <el-button type="danger" link @click="handleDelete(row.id)">删除</el-button>
                </template>
            </el-table-column>
        </el-table>
        <div class="flex justify-end mt-4">
            <pagination v-model="pager" @change="getLists" />
        </div>
    </el-card>

    <popup
        ref="popupRef"
        :title="formData.id ? '编辑违禁词' : '添加违禁词'"
        :async="true"
        width="500px"
        @confirm="handleSubmit"
    >
        <el-form ref="formRef" :model="formData" :rules="formRules" label-width="100px">
            <el-form-item label="违禁词" prop="word">
                <el-input v-model="formData.word" placeholder="请输入违禁词" />
            </el-form-item>
            <el-form-item label="类型" prop="type">
                <el-radio-group v-model="formData.type">
                    <el-radio :label="1">违禁词</el-radio>
                    <el-radio :label="2">敏感词</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="替换词" prop="replace_word">
                <el-input v-model="formData.replace_word" placeholder="为空则直接拦截消息" />
            </el-form-item>
            <el-form-item label="状态" prop="status">
                <el-radio-group v-model="formData.status">
                    <el-radio :label="1">启用</el-radio>
                    <el-radio :label="0">禁用</el-radio>
                </el-radio-group>
            </el-form-item>
        </el-form>
    </popup>
</div>
</template>

<script setup lang="ts">
import { bannedWordLists, addBannedWord, editBannedWord, deleteBannedWord, changeBannedWordStatus } from '@/api/chat'
import { usePaging } from '@/hooks/usePaging'
import feedback from '@/utils/feedback'
import Popup from '@/components/popup/index.vue'
import type { FormInstance, FormRules } from 'element-plus'

const queryParams = reactive({
    word: '',
    type: '',
    status: ''
})

const { pager, getLists, resetPage, resetQuery } = usePaging({
    fetchFun: bannedWordLists,
    params: queryParams
})

const popupRef = ref<InstanceType<typeof Popup>>()
const formRef = ref<FormInstance>()
const formData = reactive({
    id: '',
    word: '',
    type: 1,
    replace_word: '',
    status: 1
})

const formRules: FormRules = {
    word: [{ required: true, message: '请输入违禁词', trigger: 'blur' }]
}

const handleAdd = () => {
    formData.id = ''
    formData.word = ''
    formData.type = 1
    formData.replace_word = ''
    formData.status = 1
    popupRef.value?.open()
}

const handleEdit = (row: any) => {
    formData.id = row.id
    formData.word = row.word
    formData.type = row.type
    formData.replace_word = row.replace_word
    formData.status = row.status
    popupRef.value?.open()
}

const handleSubmit = async () => {
    await formRef.value?.validate()
    if (formData.id) {
        await editBannedWord(formData)
        feedback.msgSuccess('编辑成功')
    } else {
        await addBannedWord(formData)
        feedback.msgSuccess('添加成功')
    }
    popupRef.value?.close()
    getLists()
}

const handleDelete = async (id: number) => {
    await feedback.confirm('确定要删除该违禁词吗？')
    await deleteBannedWord({ id })
    feedback.msgSuccess('删除成功')
    getLists()
}

const handleStatusChange = async (row: any) => {
    try {
        await changeBannedWordStatus({ id: row.id, status: row.status })
        feedback.msgSuccess('操作成功')
    } catch (e) {
        row.status = row.status == 1 ? 0 : 1
    }
}

getLists()
</script>
