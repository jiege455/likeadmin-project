<template>
    <uni-nav title="申请成为分销员"></uni-nav>

    <view class="promotion-apply-page">
        <!-- 审核中/已通过/被拒绝 状态展示 -->
        <view v-if="applyStatus !== null" class="status-container">
            <view v-if="applyStatus === 0" class="status-box wait">
                <view class="icon text-yellow-500">⏳</view>
                <view class="text">申请审核中，请耐心等待</view>
            </view>
            <view v-if="applyStatus === 1" class="status-box success">
                <view class="icon text-green-500">✅</view>
                <view class="text">恭喜！您已成为分销员</view>
                <view class="btn" @click="goCenter">进入分销中心</view>
            </view>
            <view v-if="applyStatus === 2" class="status-box fail">
                <view class="icon text-red-500">❌</view>
                <view class="text">申请被拒绝：{{ auditRemark }}</view>
                <view class="btn" @click="reApply">重新申请</view>
            </view>
        </view>

        <!-- 申请表单 -->
        <view v-else class="apply-form">
            <view class="form-card">
                <view class="form-item">
                    <text class="label">姓名</text>
                    <input class="input" v-model="formData.name" placeholder="请输入真实姓名" />
                </view>
                <view class="form-item">
                    <text class="label">手机号</text>
                    <input class="input" v-model="formData.mobile" placeholder="请输入手机号码" />
                </view>
                <view class="form-item">
                    <text class="label">申请理由</text>
                    <textarea
                        class="textarea"
                        v-model="formData.reason"
                        placeholder="请输入申请理由（选填）"
                    />
                </view>

                <view class="submit-btn" @click="handleSubmit">立即申请</view>
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { applyDistribution, getApplyDetail } from '@/api/distribution'

const formData = ref({
    name: '',
    mobile: '',
    reason: ''
})

const applyStatus = ref<number | null>(null) // 0:待审核 1:通过 2:拒绝 null:未申请
const auditRemark = ref('')

const getStatus = async () => {
    try {
        const res = await getApplyDetail()
        if (res.is_distributor == 1) {
            applyStatus.value = 1
        } else if (res.apply) {
            applyStatus.value = res.apply.status
            auditRemark.value = res.apply.audit_remark
        } else {
            applyStatus.value = null
        }
    } catch (e) {
        console.error(e)
    }
}

const handleSubmit = async () => {
    if (!formData.value.name) return uni.showToast({ title: '请输入姓名', icon: 'none' })
    if (!formData.value.mobile) return uni.showToast({ title: '请输入手机号', icon: 'none' })

    uni.showLoading({ title: '提交中...' })
    try {
        await applyDistribution(formData.value)
        uni.showToast({ title: '申请提交成功', icon: 'success' })
        getStatus() // 刷新状态
    } catch (e) {
        // error handled by interceptor
    } finally {
        uni.hideLoading()
    }
}

const goCenter = () => {
    uni.reLaunch({ url: '/packages/pages/distribution/distribution' })
}

const reApply = () => {
    applyStatus.value = null
}

onMounted(() => {
    getStatus()
})
</script>

<style lang="scss" scoped>
.promotion-apply-page {
    min-height: 100vh;
    background-color: #f8f8f8;
}
.form-card {
    margin: 30rpx 30rpx 0;
    background-color: #fff;
    border-radius: 16rpx;
    padding: 40rpx;
    box-shadow: 0 4rpx 12rpx rgba(0, 0, 0, 0.05);
}
.form-item {
    margin-bottom: 30rpx;
    .label {
        font-size: 28rpx;
        color: #333;
        margin-bottom: 15rpx;
        display: block;
    }
    .input {
        height: 80rpx;
        background-color: #f5f5f5;
        border-radius: 8rpx;
        padding: 0 20rpx;
        font-size: 28rpx;
    }
    .textarea {
        width: 100%;
        height: 200rpx;
        background-color: #f5f5f5;
        border-radius: 8rpx;
        padding: 20rpx;
        font-size: 28rpx;
        box-sizing: border-box;
    }
}
.submit-btn {
    height: 88rpx;
    display: flex;
    justify-content: center;
    align-items: center;
    background: var(--color-primary); // 使用全局主题色变量
    color: #fff;
    border-radius: 44rpx;
    font-size: 32rpx;
    font-weight: bold;
    margin-top: 50rpx;
}

.status-container {
    padding: 100rpx 40rpx;
    display: flex;
    justify-content: center;
}
.status-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    .icon {
        font-size: 80rpx;
        margin-bottom: 20rpx;
    }
    .text {
        font-size: 32rpx;
        color: #333;
        margin-bottom: 40rpx;
        text-align: center;
    }
    .btn {
        padding: 20rpx 60rpx;
        background: var(--color-primary); // 使用全局主题色变量
        color: #fff;
        border-radius: 40rpx;
    }
}
</style>
