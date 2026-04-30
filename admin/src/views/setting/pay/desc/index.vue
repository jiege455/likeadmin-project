<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-alert
                type="warning"
                title="温馨提示：设置支付时显示的商品描述，建议使用通用名称避免风控"
                :closable="false"
                show-icon
            />
        </el-card>
        <el-card shadow="never" class="mt-4 !border-none">
            <el-form ref="formRef" :model="formData" label-width="120px" v-loading="loading">
                <el-form-item label="充值描述">
                    <el-input
                        v-model="formData.recharge_desc"
                        placeholder="请输入充值支付描述"
                        style="max-width: 400px"
                    />
                    <div class="form-tips">用户充值时，支付平台显示的商品名称</div>
                </el-form-item>
                <el-form-item label="文章购买描述">
                    <el-input
                        v-model="formData.article_desc"
                        placeholder="请输入文章购买支付描述"
                        style="max-width: 400px"
                    />
                    <div class="form-tips">用户购买文章时，支付平台显示的商品名称</div>
                </el-form-item>
                <el-form-item label="商品订单描述">
                    <el-input
                        v-model="formData.order_desc"
                        placeholder="请输入商品订单支付描述"
                        style="max-width: 400px"
                    />
                    <div class="form-tips">用户购买商品时，支付平台显示的商品名称</div>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" :loading="isLock" @click="lockSubmit">保存配置</el-button>
                </el-form-item>
            </el-form>
        </el-card>
    </div>
</template>

<script lang="ts" setup>
import { getPayDesc, setPayDesc } from '@/api/setting/payDesc'
import { useLockFn } from '@/hooks/useLockFn'
import feedback from '@/utils/feedback'

const loading = ref(false)
const formData = ref({
    recharge_desc: '',
    article_desc: '',
    order_desc: ''
})

const getConfig = async () => {
    loading.value = true
    try {
        const data = await getPayDesc()
        formData.value = {
            recharge_desc: data.recharge_desc || '会员充值',
            article_desc: data.article_desc || '会员服务',
            order_desc: data.order_desc || '商品订单'
        }
    } finally {
        loading.value = false
    }
}

const submitEdit = async () => {
    await setPayDesc(formData.value)
    feedback.msgSuccess('保存成功')
}

const { isLock, lockFn: lockSubmit } = useLockFn(submitEdit)

getConfig()
</script>
