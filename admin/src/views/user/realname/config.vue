<template>
    <el-card shadow="never" class="!border-none">
        <el-form ref="formRef" :model="formData" label-width="120px">
            <el-form-item label="实名认证功能">
                <el-switch v-model="formData.status" :active-value="1" :inactive-value="0" />
                <div class="form-tip">开启后，用户在特定场景（如推广）下必须先完成实名认证</div>
            </el-form-item>
            <el-form-item label="认证方式">
                <el-radio-group v-model="formData.auth_type">
                    <el-radio label="manual">人工审核</el-radio>
                    <el-radio label="aliyun">阿里云接口</el-radio>
                    <el-radio label="umeng">友盟认证</el-radio>
                </el-radio-group>
            </el-form-item>
            <template v-if="formData.auth_type == 'aliyun'">
                <el-form-item label="AppCode">
                    <el-input v-model="formData.aliyun_appcode" class="w-[400px]" placeholder="请输入阿里云AppCode" />
                    <div class="form-tip">请前往阿里云云市场购买身份证实名认证服务</div>
                </el-form-item>
                <el-form-item label="接口地址">
                    <el-input v-model="formData.aliyun_url" class="w-[400px]" placeholder="请输入API完整地址" />
                    <div class="form-tip">
                        默认使用: https://idenauthen.market.alicloudapi.com/idenAuthentication<br>
                        支持阿里云市场大多数身份证二要素API (Header认证方式)
                    </div>
                </el-form-item>
            </template>
            <template v-if="formData.auth_type == 'umeng'">
                <el-form-item label="AppKey">
                    <el-input v-model="formData.umeng_appkey" class="w-[400px]" placeholder="请输入友盟AppKey" />
                </el-form-item>
                <el-form-item label="AppSecret">
                    <el-input v-model="formData.umeng_appsecret" class="w-[400px]" placeholder="请输入友盟AppSecret" />
                </el-form-item>
            </template>
            <el-form-item>
                <el-button type="primary" @click="handleSubmit">保存</el-button>
            </el-form-item>
        </el-form>
    </el-card>
</template>

<script setup lang="ts">
import { ref, onMounted, reactive } from 'vue'
import { getRealnameConfig, setRealnameConfig } from '@/api/user_realname'
import feedback from '@/utils/feedback'

const formData = reactive({
    status: 0,
    auth_type: 'manual',
    aliyun_appcode: '',
    aliyun_url: '',
    umeng_appkey: '',
    umeng_appsecret: ''
})

const getData = async () => {
    const res = await getRealnameConfig()
    Object.assign(formData, res)
}

const handleSubmit = async () => {
    await setRealnameConfig(formData)
    feedback.msgSuccess('保存成功')
}

onMounted(() => {
    getData()
})
</script>
