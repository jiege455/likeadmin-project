<template>
    <div class="commission-config">
        <el-card class="!border-none" shadow="never">
            <div class="font-medium mb-4">平台抽成设置</div>
            <el-form ref="formRef" :model="formData" label-width="120px" class="w-[500px]">
                <el-form-item label="平台抽成比例" prop="platform_ratio">
                    <el-input v-model="formData.platform_ratio" placeholder="请输入百分比">
                        <template #append>%</template>
                    </el-input>
                    <div class="form-tips">设置平台在每笔交易中抽取的佣金比例</div>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="handleSubmit">保存</el-button>
                </el-form-item>
            </el-form>
        </el-card>
    </div>
</template>

<script setup lang="ts">
import { getCommissionConfig, setCommissionConfig } from '@/api/setting'
import feedback from '@/utils/feedback'

const formData = reactive({
    platform_ratio: ''
})

const getData = async () => {
    const data = await getCommissionConfig()
    formData.platform_ratio = data.platform_ratio
}

const handleSubmit = async () => {
    await setCommissionConfig(formData)
    feedback.msgSuccess('保存成功')
}

getData()
</script>
