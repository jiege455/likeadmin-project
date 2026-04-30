<template>
    <div class="merchant-setting">
        <!--
        开发者：杰哥网络科技
        qq2711793818 杰哥
        -->
        <el-card class="!border-none" shadow="never">
            <el-form :model="formData" label-width="140px" v-loading="loading">
                <el-divider content-position="left">入驻设置</el-divider>
                
                <el-form-item label="开启入驻审核">
                    <el-switch v-model="formData.open_audit" :active-value="1" :inactive-value="0" />
                    <div class="text-xs text-gray-400 mt-1">开启后，商户入驻需要管理员审核</div>
                </el-form-item>

                <el-divider content-position="left">平台抽成</el-divider>
                
                <el-form-item label="平台抽成比例">
                    <el-input-number v-model="formData.platform_ratio" :min="0" :max="100" :precision="2" />
                    <span class="ml-2">%</span>
                    <div class="text-xs text-gray-400 mt-1">商户文章销售时平台抽取的比例</div>
                </el-form-item>

                <el-divider content-position="left">文章价格设置</el-divider>
                
                <el-form-item label="最低文章价格">
                    <el-input-number v-model="formData.min_price" :min="0" :precision="2" />
                    <span class="ml-2">元</span>
                    <div class="text-xs text-gray-400 mt-1">商户发布文章的最低定价</div>
                </el-form-item>

                <el-form-item label="最高文章价格">
                    <el-input-number v-model="formData.max_price" :min="0" :precision="2" />
                    <span class="ml-2">元</span>
                    <div class="text-xs text-gray-400 mt-1">商户发布文章的最高定价</div>
                </el-form-item>

                <el-divider content-position="left">分销设置</el-divider>
                
                <el-form-item label="开启分销">
                    <el-switch v-model="formData.allow_distribution" :active-value="1" :inactive-value="0" />
                    <div class="text-xs text-gray-400 mt-1">开启后商户可设置文章分销</div>
                </el-form-item>

                <el-form-item label="默认分销比例" v-if="formData.allow_distribution">
                    <el-input-number v-model="formData.default_distribution_ratio" :min="0" :max="100" :precision="2" />
                    <span class="ml-2">%</span>
                    <div class="text-xs text-gray-400 mt-1">商户设置分销时的默认比例</div>
                </el-form-item>

                <el-form-item label="最小分销比例" v-if="formData.allow_distribution">
                    <el-input-number v-model="formData.min_distribution_ratio" :min="0" :max="100" :precision="2" />
                    <span class="ml-2">%</span>
                    <div class="text-xs text-gray-400 mt-1">商户可设置的最小分销比例</div>
                </el-form-item>

                <el-form-item label="最大分销比例" v-if="formData.allow_distribution">
                    <el-input-number v-model="formData.max_distribution_ratio" :min="0" :max="100" :precision="2" />
                    <span class="ml-2">%</span>
                    <div class="text-xs text-gray-400 mt-1">商户可设置的最大分销比例</div>
                </el-form-item>

                <el-form-item>
                    <el-button type="primary" @click="handleSave" :loading="saving">保存设置</el-button>
                </el-form-item>
            </el-form>
        </el-card>
    </div>
</template>

<script setup lang="ts">
import { merchantGetConfig, merchantSetConfig } from '@/api/setting/merchant'
import feedback from '@/utils/feedback'

import { useRoute } from 'vue-router'

const route = useRoute()

const loading = ref(false)
const saving = ref(false)

const formData = reactive({
    open_audit: 0,
    platform_ratio: 10,
    min_price: 0,
    max_price: 10000,
    allow_distribution: 1,
    default_distribution_ratio: 10,
    min_distribution_ratio: 0,
    max_distribution_ratio: 50
})

const getConfig = async () => {
    loading.value = true
    try {
        const res = await merchantGetConfig()
        Object.assign(formData, res)
    } finally {
        loading.value = false
    }
}

const handleSave = async () => {
    if (formData.min_distribution_ratio > formData.max_distribution_ratio) {
        feedback.msgError('最小分销比例不能大于最大分销比例')
        return
    }
    saving.value = true
    try {
        await merchantSetConfig(formData)
        feedback.msgSuccess('保存成功')
    } finally {
        saving.value = false
    }
}

onMounted(() => {
    getConfig()
})
</script>
