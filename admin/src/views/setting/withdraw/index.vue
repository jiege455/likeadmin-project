<template>
    <div class="withdraw-setting">
        <!--
        开发者：杰哥网络科技
        qq2711793818 杰哥
        -->
        <el-card class="!border-none" shadow="never">
            <el-form :model="formData" label-width="140px" v-loading="loading">
                <el-divider content-position="left">提现方式设置</el-divider>
                
                <el-form-item label="可用提现方式">
                    <div class="method-list">
                        <div v-for="(method, index) in formData.withdraw_methods" :key="method.type" class="method-item">
                            <div class="method-info">
                                <el-icon v-if="method.type == 1" class="method-icon wechat"><ChatDotRound /></el-icon>
                                <el-icon v-else-if="method.type == 2" class="method-icon alipay"><Wallet /></el-icon>
                                <el-icon v-else class="method-icon bank"><CreditCard /></el-icon>
                                <span class="method-name">{{ method.name }}</span>
                            </div>
                            <el-switch v-model="method.enabled" :active-value="1" :inactive-value="0" />
                        </div>
                    </div>
                    <div class="form-tip">开启后，用户可以选择该方式提现</div>
                </el-form-item>

                <el-divider content-position="left">商家提现设置</el-divider>
                
                <el-form-item label="最低提现金额">
                    <el-input-number v-model="formData.merchant_min_withdraw" :min="0" :precision="2" />
                    <span class="unit-text">元</span>
                </el-form-item>

                <el-form-item label="提现手续费">
                    <el-input-number v-model="formData.merchant_withdraw_fee" :min="0" :max="100" :precision="2" />
                    <span class="unit-text">%</span>
                </el-form-item>

                <el-divider content-position="left">推广员提现设置</el-divider>
                
                <el-form-item label="最低提现金额">
                    <el-input-number v-model="formData.distributor_min_withdraw" :min="0" :precision="2" />
                    <span class="unit-text">元</span>
                </el-form-item>

                <el-form-item label="提现手续费">
                    <el-input-number v-model="formData.distributor_withdraw_fee" :min="0" :max="100" :precision="2" />
                    <span class="unit-text">%</span>
                </el-form-item>

                <el-divider content-position="left">通用设置</el-divider>
                
                <el-form-item label="预计到账天数">
                    <el-input-number v-model="formData.withdraw_arrival_days" :min="1" :max="30" />
                    <span class="unit-text">天</span>
                </el-form-item>

                <el-form-item label="提现说明">
                    <el-input v-model="formData.withdraw_notice" type="textarea" :rows="3" placeholder="请输入提现说明" />
                </el-form-item>

                <el-divider content-position="left">分销设置</el-divider>
                
                <el-form-item label="最小分销比例">
                    <el-input-number v-model="formData.min_distribution_ratio" :min="0" :max="100" :precision="2" />
                    <span class="unit-text">%</span>
                </el-form-item>

                <el-form-item label="最大分销比例">
                    <el-input-number v-model="formData.max_distribution_ratio" :min="0" :max="100" :precision="2" />
                    <span class="unit-text">%</span>
                </el-form-item>

                <el-form-item>
                    <el-button type="primary" @click="handleSave" :loading="saving">保存设置</el-button>
                </el-form-item>
            </el-form>
        </el-card>
    </div>
</template>

<script setup lang="ts">
import { withdrawGetConfig, withdrawSetConfig } from '@/api/setting/withdraw'
import feedback from '@/utils/feedback'
import { ChatDotRound, Wallet, CreditCard } from '@element-plus/icons-vue'

const loading = ref(false)
const saving = ref(false)

const formData = reactive({
    merchant_min_withdraw: 100,
    merchant_withdraw_fee: 0,
    distributor_min_withdraw: 10,
    distributor_withdraw_fee: 0,
    withdraw_arrival_days: 3,
    withdraw_notice: '',
    max_distribution_ratio: 50,
    min_distribution_ratio: 0,
    withdraw_methods: [
        { type: 1, name: '微信零钱', enabled: 0 },
        { type: 2, name: '支付宝', enabled: 1 },
        { type: 3, name: '银行卡', enabled: 1 }
    ] as Array<{ type: number; name: string; enabled: number }>
})

const getConfig = async () => {
    loading.value = true
    try {
        const res = await withdrawGetConfig()
        Object.assign(formData, res)
        if (!res.withdraw_methods || res.withdraw_methods.length === 0) {
            formData.withdraw_methods = [
                { type: 1, name: '微信零钱', enabled: 0 },
                { type: 2, name: '支付宝', enabled: 1 },
                { type: 3, name: '银行卡', enabled: 1 }
            ]
        }
    } finally {
        loading.value = false
    }
}

const handleSave = async () => {
    const enabledCount = formData.withdraw_methods.filter(m => m.enabled).length
    if (enabledCount === 0) {
        feedback.msgError('请至少启用一种提现方式')
        return
    }
    
    saving.value = true
    try {
        await withdrawSetConfig(formData)
        feedback.msgSuccess('保存成功')
    } finally {
        saving.value = false
    }
}

onMounted(() => {
    getConfig()
})
</script>

<style scoped>
.withdraw-setting :deep(.el-divider__text) {
    font-weight: bold;
    color: #303133;
    background-color: #fff;
}

.method-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.method-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px;
    background-color: #f5f7fa;
    border-radius: 8px;
    border: 1px solid #e4e7ed;
}

.method-info {
    display: flex;
    align-items: center;
}

.method-icon {
    font-size: 24px;
    margin-right: 12px;
}

.method-icon.wechat {
    color: #07C160;
}

.method-icon.alipay {
    color: #1677FF;
}

.method-icon.bank {
    color: #FF8C00;
}

.method-name {
    font-size: 15px;
    font-weight: 500;
    color: #303133;
}

.form-tip {
    font-size: 12px;
    color: #909399;
    margin-top: 8px;
}

.unit-text {
    margin-left: 8px;
    color: #606266;
}
</style>
