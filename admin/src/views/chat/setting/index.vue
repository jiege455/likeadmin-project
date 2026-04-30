<template>
<!--
  开发者公众号：杰哥网络科技
  QQ: 2711793818 杰哥
  聊天设置页面
-->
<div class="chat-setting">
    <el-card class="!border-none" shadow="never">
        <el-form ref="formRef" :model="formData" :rules="formRules" label-width="140px" class="max-w-[600px]">
            <el-form-item label="开启聊天功能">
                <el-switch v-model="formData.chat_enabled" :active-value="1" :inactive-value="0" />
            </el-form-item>
            <el-form-item label="聊天室公告">
                <el-input 
                    v-model="formData.chat_notice" 
                    type="textarea" 
                    :rows="3" 
                    placeholder="请输入聊天室公告，用户进入聊天室时显示"
                />
            </el-form-item>
            <el-form-item label="消息最大长度">
                <el-input-number v-model="formData.max_message_length" :min="50" :max="1000" />
                <span class="ml-2 text-gray-400">字</span>
            </el-form-item>
            <el-form-item label="消息发送间隔">
                <el-input-number v-model="formData.message_interval" :min="0" :max="10" />
                <span class="ml-2 text-gray-400">秒（0为不限制）</span>
            </el-form-item>
            <el-form-item label="开启违禁词过滤">
                <el-switch v-model="formData.enable_banned_word" :active-value="1" :inactive-value="0" />
            </el-form-item>
            <el-form-item label="开启IP黑名单">
                <el-switch v-model="formData.enable_ip_blacklist" :active-value="1" :inactive-value="0" />
            </el-form-item>
            <el-form-item label="显示在线人数">
                <el-switch v-model="formData.show_online_count" :active-value="1" :inactive-value="0" />
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="handleSave" :loading="loading">保存设置</el-button>
            </el-form-item>
        </el-form>
    </el-card>
</div>
</template>

<script setup lang="ts">
import { getChatSetting, setChatSetting } from '@/api/chat'
import feedback from '@/utils/feedback'
import type { FormInstance, FormRules } from 'element-plus'

const loading = ref(false)
const formRef = ref<FormInstance>()

const formData = reactive({
    chat_enabled: 1,
    chat_notice: '',
    max_message_length: 500,
    message_interval: 1,
    enable_banned_word: 1,
    enable_ip_blacklist: 0,
    show_online_count: 1
})

const formRules: FormRules = {
    chat_notice: [{ max: 200, message: '公告最多200个字符', trigger: 'blur' }]
}

const getConfig = async () => {
    loading.value = true
    try {
        const res = await getChatSetting()
        Object.assign(formData, res)
    } catch (e) {
        console.error(e)
    } finally {
        loading.value = false
    }
}

const handleSave = async () => {
    await formRef.value?.validate()
    loading.value = true
    try {
        await setChatSetting(formData)
        feedback.msgSuccess('保存成功')
    } catch (e) {
        console.error(e)
    } finally {
        loading.value = false
    }
}

getConfig()
</script>
