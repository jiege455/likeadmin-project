<!--
    系列编辑弹窗
    开发者：杰哥网络科技
    QQ：2711793818 杰哥
-->
<template>
    <popup
        ref="popupRef"
        :title="popupTitle"
        :async="true"
        width="550px"
        @confirm="handleSubmit"
        @close="handleClose"
    >
        <el-form ref="formRef" :model="formData" :rules="formRules" label-width="100px">
            <el-form-item label="系列名称" prop="name">
                <el-input v-model="formData.name" placeholder="请输入系列名称" />
            </el-form-item>
            <el-form-item label="彩票类型" prop="lottery_type">
                <el-select v-model="formData.lottery_type" placeholder="请选择彩票类型" class="w-full">
                    <el-option label="福彩 3D" value="fc3d" />
                    <el-option label="排列三" value="pl3" />
                    <el-option label="双色球" value="ssq" />
                    <el-option label="大乐透" value="dlt" />
                </el-select>
            </el-form-item>
            <el-form-item label="总期数" prop="total_issues">
                <el-input-number v-model="formData.total_issues" :min="0" class="w-full" />
            </el-form-item>
            <el-form-item label="系列介绍" prop="series_desc">
                <el-input v-model="formData.series_desc" type="textarea" :rows="3" placeholder="请输入系列介绍" />
            </el-form-item>
            <el-form-item label="状态">
                <el-switch v-model="formData.series_status" :active-value="1" :inactive-value="0" />
            </el-form-item>
        </el-form>
    </popup>
</template>

<script setup lang="ts">
import type { FormInstance } from 'element-plus'
import Popup from '@/components/popup/index.vue'
import { seriesAdd, seriesEdit, seriesDetail } from '@/api/series/series'
import feedback from '@/utils/feedback'

const emit = defineEmits(['success'])

const popupRef = shallowRef<InstanceType<typeof Popup>>()
const formRef = shallowRef<FormInstance>()

const popupTitle = computed(() => (formData.id ? '编辑系列' : '添加系列'))

const formData = reactive({
    id: '',
    name: '',
    lottery_type: '',
    total_issues: 0,
    series_desc: '',
    auto_publish: 0,
    publish_interval: 0,
    series_status: 1,
    is_show: 1,
    is_series: 1,
    sort: 0
})

const formRules = {
    name: [{ required: true, message: '请输入系列名称', trigger: 'blur' }],
    lottery_type: [{ required: true, message: '请选择彩票类型', trigger: 'change' }]
}

const handleSubmit = async () => {
    await formRef.value?.validate()
    const api = formData.id ? seriesEdit : seriesAdd
    await api(formData)
    feedback.msgSuccess('操作成功')
    emit('success')
    popupRef.value?.close()
}

const handleClose = () => {
    formRef.value?.resetFields()
    Object.assign(formData, {
        id: '',
        name: '',
        lottery_type: '',
        total_issues: 0,
        series_desc: '',
        auto_publish: 0,
        publish_interval: 0,
        series_status: 1,
        is_show: 1,
        is_series: 1,
        sort: 0
    })
}

const open = async (id?: number) => {
    if (id) {
        const res = await seriesDetail({ id })
        Object.assign(formData, res)
    }
    popupRef.value?.open()
}

defineExpose({ open })
</script>
