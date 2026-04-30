<template>
    <div class="smtp-config">
        <el-card class="!border-none" shadow="never">
            <div class="font-medium mb-4">SMTP 邮箱设置</div>
            <el-form ref="formRef" :model="formData" label-width="120px" class="w-[500px]">
                <el-form-item label="SMTP 服务器" prop="smtp_host">
                    <el-input v-model="formData.smtp_host" placeholder="例如: smtp.qq.com" />
                </el-form-item>
                <el-form-item label="SMTP 端口" prop="smtp_port">
                    <el-input v-model="formData.smtp_port" placeholder="例如: 465" />
                </el-form-item>
                <el-form-item label="发件人邮箱" prop="smtp_user">
                    <el-input v-model="formData.smtp_user" placeholder="请输入邮箱账号" />
                </el-form-item>
                <el-form-item label="授权码/密码" prop="smtp_pass">
                    <el-input v-model="formData.smtp_pass" type="password" show-password placeholder="请输入邮箱授权码或密码" />
                </el-form-item>
                <el-form-item label="发件人名称" prop="smtp_from">
                    <el-input v-model="formData.smtp_from" placeholder="例如: likeadmin官方" />
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="handleSubmit">保存</el-button>
                </el-form-item>
            </el-form>
        </el-card>
    </div>
</template>

<script setup lang="ts">
import { getSmtpConfig, setSmtpConfig } from '@/api/setting'
import feedback from '@/utils/feedback'

const formData = reactive({
    smtp_host: '',
    smtp_port: 465,
    smtp_user: '',
    smtp_pass: '',
    smtp_from: ''
})

const getData = async () => {
    const data = await getSmtpConfig()
    Object.assign(formData, data)
}

const handleSubmit = async () => {
    await setSmtpConfig(formData)
    feedback.msgSuccess('保存成功')
}

getData()
</script>
