<template>
<!--
  开发者公众号：杰哥网络科技
  QQ: 2711793818 杰哥
  系统公告管理页面
-->
    <div class="system-notice-lists">
        <el-card class="!border-none" shadow="never">
             <div class="mb-4">
                <el-button type="primary" @click="handleAdd">发布公告</el-button>
            </div>
            <el-table size="large" v-loading="pager.loading" :data="pager.lists">
                <el-table-column label="ID" prop="id" min-width="80" />
                <el-table-column label="封面" min-width="80">
                    <template #default="{ row }">
                        <el-image 
                            v-if="row.cover" 
                            :src="row.cover" 
                            style="width: 50px; height: 50px;"
                            fit="cover"
                        />
                        <span v-else class="text-gray-400">无</span>
                    </template>
                </el-table-column>
                <el-table-column label="标题" prop="title" min-width="180" show-overflow-tooltip />
                <el-table-column label="类型" min-width="80">
                    <template #default="{ row }">
                        <el-tag v-if="row.type == 2" type="danger">重要</el-tag>
                        <el-tag v-else-if="row.type == 3" type="warning">活动</el-tag>
                        <el-tag v-else>普通</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="置顶" min-width="80">
                    <template #default="{ row }">
                        <el-tag v-if="row.is_top == 1" type="success">是</el-tag>
                        <el-tag v-else type="info">否</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="接收对象" min-width="100">
                    <template #default="{ row }">
                        <el-tag v-if="row.recipient == 2" type="warning">仅商户</el-tag>
                        <el-tag v-else type="primary">全员</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="阅读量" prop="views" min-width="80" />
                <el-table-column label="状态" min-width="80">
                    <template #default="{ row }">
                        <el-tag v-if="row.is_show == 1" type="success">显示</el-tag>
                        <el-tag v-else type="info">隐藏</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="发布时间" prop="create_time" min-width="160" />
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
            :title="formData.id ? '编辑公告' : '发布公告'"
            :async="true"
            width="600px"
            @confirm="handleSubmit"
        >
            <el-form ref="formRef" :model="formData" :rules="formRules" label-width="100px">
                <el-form-item label="标题" prop="title">
                    <el-input v-model="formData.title" placeholder="请输入公告标题" />
                </el-form-item>
                <el-form-item label="封面图片" prop="cover">
                    <div>
                        <material-picker v-model="formData.cover" :limit="1" />
                        <div class="text-xs text-gray-400 mt-1">建议尺寸: 750x400px</div>
                    </div>
                </el-form-item>
                <el-form-item label="内容" prop="content">
                    <el-input v-model="formData.content" type="textarea" :rows="5" placeholder="请输入公告内容" />
                </el-form-item>
                <el-form-item label="公告类型" prop="type">
                    <el-radio-group v-model="formData.type">
                        <el-radio :label="1">普通</el-radio>
                        <el-radio :label="2">重要</el-radio>
                        <el-radio :label="3">活动</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="是否置顶" prop="is_top">
                    <el-radio-group v-model="formData.is_top">
                        <el-radio :label="1">是</el-radio>
                        <el-radio :label="0">否</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="弹出频率" prop="popup_type">
                    <el-radio-group v-model="formData.popup_type">
                        <el-radio :label="1">每天一次</el-radio>
                        <el-radio :label="2">每次弹出</el-radio>
                    </el-radio-group>
                    <div class="text-xs text-gray-400 mt-1">仅对"重要"类型公告生效，首页弹窗展示</div>
                </el-form-item>
                <el-form-item label="接收对象" prop="recipient">
                    <el-radio-group v-model="formData.recipient">
                        <el-radio :label="1">全员</el-radio>
                        <el-radio :label="2">仅商户</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="状态" prop="is_show">
                    <el-radio-group v-model="formData.is_show">
                        <el-radio :label="1">显示</el-radio>
                        <el-radio :label="0">隐藏</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="排序" prop="sort">
                    <el-input-number v-model="formData.sort" :min="0" :max="999" />
                    <div class="text-xs text-gray-400 mt-1">数字越大越靠前</div>
                </el-form-item>
            </el-form>
        </popup>
    </div>
</template>

<script setup lang="ts">
import { systemNoticeLists, systemNoticeDetail, addSystemNotice, editSystemNotice, deleteSystemNotice } from '@/api/notice'
import { usePaging } from '@/hooks/usePaging'
import feedback from '@/utils/feedback'
import Popup from '@/components/popup/index.vue'
import type { FormInstance, FormRules } from 'element-plus'

const { pager, getLists } = usePaging({
    fetchFun: systemNoticeLists
})

const popupRef = ref<InstanceType<typeof Popup>>()
const formRef = ref<FormInstance>()
const formData = reactive({
    id: null as number | null,
    title: '',
    content: '',
    cover: '',
    type: 1,
    is_top: 0,
    popup_type: 1,
    is_show: 1,
    recipient: 1,
    sort: 0
})

const formRules: FormRules = {
    title: [{ required: true, message: '请输入标题', trigger: 'blur' }],
    content: [{ required: true, message: '请输入内容', trigger: 'blur' }]
}

const resetForm = () => {
    formData.id = null
    formData.title = ''
    formData.content = ''
    formData.cover = ''
    formData.type = 1
    formData.is_top = 0
    formData.popup_type = 1
    formData.is_show = 1
    formData.recipient = 1
    formData.sort = 0
}

const handleAdd = () => {
    resetForm()
    popupRef.value?.open()
}

const handleEdit = async (row: any) => {
    resetForm()
    const res = await systemNoticeDetail({ id: row.id })
    Object.assign(formData, res)
    popupRef.value?.open()
}

const handleSubmit = async () => {
    await formRef.value?.validate()
    if (formData.id) {
        await editSystemNotice(formData)
        feedback.msgSuccess('编辑成功')
    } else {
        await addSystemNotice(formData)
        feedback.msgSuccess('发布成功')
    }
    popupRef.value?.close()
    getLists()
}

const handleDelete = async (id: number) => {
    await feedback.confirm('确定要删除该公告吗？')
    await deleteSystemNotice({ id })
    feedback.msgSuccess('删除成功')
    getLists()
}

getLists()
</script>
