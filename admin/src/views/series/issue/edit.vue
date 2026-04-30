<!--
    期次编辑弹窗
    开发者：杰哥网络科技
    QQ：2711793818 杰哥
-->
<template>
    <popup
        ref="popupRef"
        :title="popupTitle"
        :async="true"
        width="800px"
        @confirm="handleSubmit"
        @close="handleClose"
    >
        <el-form ref="formRef" :model="formData" :rules="formRules" label-width="100px">
            <el-row :gutter="20">
                <el-col :span="12">
                    <el-form-item label="期号" prop="issue_no">
                        <el-input v-model="formData.issue_no" placeholder="请输入期号，如2024001" />
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="标题" prop="title">
                        <el-input v-model="formData.title" placeholder="请输入标题" />
                    </el-form-item>
                </el-col>
            </el-row>
            <el-form-item label="简介" prop="desc">
                <el-input v-model="formData.desc" type="textarea" :rows="2" placeholder="请输入简介" />
            </el-form-item>
            <el-form-item label="正文内容" prop="content">
                <wangeditor v-model="formData.content" :height="300" />
            </el-form-item>
            <el-form-item label="隐藏内容" prop="hidden_content">
                <el-input v-model="formData.hidden_content" type="textarea" :rows="3" placeholder="付费后可见的隐藏内容，如预测号码等" />
            </el-form-item>
        </el-form>
    </popup>
</template>

<script setup lang="ts">
import type { FormInstance } from 'element-plus'
import Popup from '@/components/popup/index.vue'
import wangeditor from '@/components/editor/index.vue'
import { issueAdd, issueEdit, issueDetail } from '@/api/series/issue'
import feedback from '@/utils/feedback'

const route = useRoute()
const emit = defineEmits(['success'])

const popupRef = shallowRef<InstanceType<typeof Popup>>()
const formRef = shallowRef<FormInstance>()

const popupTitle = computed(() => (formData.id ? '编辑期次' : '添加期次'))

const formData = reactive({
    id: '',
    series_id: '',
    issue_no: '',
    title: '',
    desc: '',
    content: '',
    hidden_content: '',
    cid: 0,
    is_show: 1,
    issue_status: 0
})

const formRules = {
    issue_no: [{ required: true, message: '请输入期号', trigger: 'blur' }],
    title: [{ required: true, message: '请输入标题', trigger: 'blur' }],
    content: [{ required: true, message: '请输入正文内容', trigger: 'blur' }]
}

const handleSubmit = async () => {
    await formRef.value?.validate()
    const api = formData.id ? issueEdit : issueAdd
    await api(formData)
    feedback.msgSuccess('操作成功')
    emit('success')
    popupRef.value?.close()
}

const handleClose = () => {
    formRef.value?.resetFields()
    Object.assign(formData, {
        id: '',
        series_id: '',
        issue_no: '',
        title: '',
        desc: '',
        content: '',
        hidden_content: '',
        cid: 0,
        is_show: 1,
        issue_status: 0
    })
}

const open = async (id?: number) => {
    formData.series_id = route.query.series_id as string
    if (id) {
        const res = await issueDetail({ id })
        Object.assign(formData, res)
    }
    popupRef.value?.open()
}

defineExpose({ open })
</script>
