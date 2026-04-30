<!--
  举报页面
  开发者：杰哥网络科技。
  qq2711793818 杰哥
-->
<template>
    <view class="complaint-page">
        <!-- 顶部信息卡片 -->
        <view class="info-card bg-white">
            <view class="card-header">
                <view
                    class="header-icon"
                    :style="{ backgroundColor: themeStore.primaryColor + '15' }"
                >
                    <u-icon
                        :name="formData.type === 1 ? 'home' : 'file-text'"
                        size="24"
                        :color="themeStore.primaryColor"
                    ></u-icon>
                </view>
                <view class="header-content">
                    <view class="header-label">举报对象</view>
                    <view class="header-value">{{ targetTypeText }}</view>
                </view>
            </view>
            <view class="card-body" v-if="targetTitle">
                <text class="body-text">{{ targetTitle }}</text>
            </view>
        </view>

        <!-- 表单区域 -->
        <view class="form-section bg-white mt-3">
            <!-- 举报原因 -->
            <view class="form-item">
                <view class="form-label">
                    <text class="label-text">举报原因</text>
                    <text class="label-required">*</text>
                </view>
                <view class="reason-tags">
                    <view
                        v-for="(item, index) in reasonList"
                        :key="index"
                        class="reason-tag"
                        :class="formData.reason === item ? 'active' : ''"
                        :style="
                            formData.reason === item
                                ? { backgroundColor: themeStore.primaryColor, color: '#fff' }
                                : {}
                        "
                        @click="formData.reason = item"
                    >
                        {{ item }}
                    </view>
                </view>
            </view>

            <!-- 详细说明 -->
            <view class="form-item">
                <view class="form-label">
                    <text class="label-text">详细说明</text>
                    <text class="label-required">*</text>
                </view>
                <view class="textarea-wrapper">
                    <u-input
                        v-model="formData.content"
                        type="textarea"
                        height="240"
                        placeholder="请详细描述举报原因，我们将尽快核实处理"
                        :border="false"
                    />
                </view>
            </view>

            <!-- 图片凭证 -->
            <view class="form-item">
                <view class="form-label">
                    <text class="label-text">图片凭证</text>
                    <text class="label-required">*</text>
                </view>
                <view class="upload-wrapper">
                    <u-upload
                        ref="uUpload"
                        :action="action"
                        :header="header"
                        :max-count="3"
                        @on-list-change="handleImageChange"
                    ></u-upload>
                    <view class="upload-tip">
                        <u-icon name="info-circle" size="14" color="#999"></u-icon>
                        <text>必须上传至少 1 张图片，最多 3 张</text>
                    </view>
                </view>
            </view>

            <!-- 联系方式 -->
            <view class="form-item">
                <view class="form-label">
                    <text class="label-text">联系方式</text>
                    <text class="label-optional">（选填）</text>
                </view>
                <view class="input-wrapper">
                    <u-input
                        v-model="formData.contact"
                        placeholder="请输入您的手机号或微信号"
                        :border="false"
                    />
                </view>
            </view>
        </view>

        <!-- 提交按钮 -->
        <view class="submit-section">
            <u-button
                class="submit-btn"
                :style="{ backgroundColor: themeStore.primaryColor, color: '#fff' }"
                @click="handleSubmit"
                :loading="loading"
            >
                提交举报
            </u-button>
            <view class="submit-tip">提交后我们会尽快核实处理</view>
        </view>
    </view>
</template>

<script setup lang="ts">
import { reactive, ref, computed } from 'vue'
import { onLoad } from '@dcloudio/uni-app'
import { addMerchantComplaint } from '@/api/merchant'
import { useUserStore } from '@/stores/user'
import { useThemeStore } from '@/stores/theme'
import appConfig from '@/config'
import { safeNavigateBack } from '@/utils/util'

const userStore = useUserStore()
const themeStore = useThemeStore()
const loading = ref(false)
const targetTitle = ref('')

const formData = reactive({
    type: 1,
    target_id: 0,
    reason: '',
    content: '',
    images: [] as string[],
    contact: ''
})

const reasonList = computed(() => {
    const reasons: Record<number, string[]> = {
        1: ['虚假宣传', '服务态度差', '欺诈行为', '侵权内容', '其他原因'],
        2: ['内容虚假', '质量低劣', '侵权内容', '违法违规', '其他原因']
    }
    return reasons[formData.type] || reasons[1]
})

const targetTypeText = computed(() => {
    return formData.type === 1 ? '商家' : '文章'
})

const action = `${appConfig.baseUrl}/api/upload/image`
const header = {
    token: userStore.token
}

const handleImageChange = (lists: any[]) => {
    formData.images = lists.map((item) => item.response?.data?.url || item.url)
}

const handleSubmit = async () => {
    if (!formData.reason) {
        uni.showToast({ title: '请选择举报原因', icon: 'none' })
        return
    }
    if (!formData.content) {
        uni.showToast({ title: '请输入详细说明', icon: 'none' })
        return
    }
    if (!formData.images || formData.images.length === 0) {
        uni.showToast({ title: '请上传至少 1 张图片凭证', icon: 'none' })
        return
    }

    loading.value = true
    try {
        await addMerchantComplaint({
            type: formData.type,
            target_id: formData.target_id,
            reason: formData.reason,
            content: formData.content,
            images: formData.images,
            contact: formData.contact
        })
        uni.showToast({ title: '提交成功' })
        setTimeout(() => {
            safeNavigateBack({ defaultUrl: '/pages/index/index' })
        }, 1500)
    } catch (e: any) {
        uni.showToast({ title: e || '提交失败，请重试', icon: 'none' })
    } finally {
        loading.value = false
    }
}

onLoad((options: any) => {
    if (options.type) {
        formData.type = parseInt(options.type)
    }
    if (options.target_id) {
        formData.target_id = parseInt(options.target_id)
    }
    if (options.title) {
        targetTitle.value = decodeURIComponent(options.title)
    }
    if (options.merchant_id) {
        formData.type = 1
        formData.target_id = parseInt(options.merchant_id)
    }
})
</script>

<style lang="scss" scoped>
.complaint-page {
    min-height: 100vh;
    background-color: #f5f5f5;
    padding-bottom: calc(40rpx + env(safe-area-inset-bottom));
}

/* 信息卡片 */
.info-card {
    padding: 30rpx;
    margin: 20rpx;
    border-radius: 20rpx;
    box-shadow: 0 2rpx 12rpx rgba(0, 0, 0, 0.05);
}

.card-header {
    display: flex;
    align-items: center;
}

.header-icon {
    width: 80rpx;
    height: 80rpx;
    border-radius: 20rpx;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 24rpx;
}

.header-content {
    flex: 1;
}

.header-label {
    font-size: 24rpx;
    color: #999;
    margin-bottom: 8rpx;
}

.header-value {
    font-size: 30rpx;
    font-weight: bold;
    color: #333;
}

.card-body {
    margin-top: 20rpx;
    padding-top: 20rpx;
    border-top: 1rpx solid #f0f0f0;
}

.body-text {
    font-size: 26rpx;
    color: #666;
    line-height: 1.6;
}

/* 表单区域 */
.form-section {
    padding: 30rpx;
    margin: 0 20rpx 20rpx;
    border-radius: 20rpx;
    box-shadow: 0 2rpx 12rpx rgba(0, 0, 0, 0.05);
}

.form-item {
    margin-bottom: 40rpx;

    &:last-child {
        margin-bottom: 0;
    }
}

.form-label {
    display: flex;
    align-items: center;
    margin-bottom: 20rpx;
}

.label-text {
    font-size: 30rpx;
    font-weight: bold;
    color: #333;
}

.label-required {
    color: #ff4d4f;
    margin-left: 8rpx;
    font-size: 30rpx;
}

.label-optional {
    color: #999;
    margin-left: 8rpx;
    font-size: 26rpx;
}

/* 举报原因标签 */
.reason-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 16rpx;
}

.reason-tag {
    padding: 16rpx 28rpx;
    border-radius: 50rpx;
    font-size: 26rpx;
    background-color: #f5f5f5;
    color: #666;
    transition: all 0.3s ease;

    &.active {
        color: #fff;
        box-shadow: 0 4rpx 12rpx rgba(0, 0, 0, 0.15);
    }
}

/* 输入框 */
.textarea-wrapper,
.input-wrapper {
    background-color: #f8f9fa;
    border-radius: 16rpx;
    padding: 20rpx 24rpx;
}

/* 上传组件 */
.upload-wrapper {
    background-color: #f8f9fa;
    border-radius: 16rpx;
    padding: 20rpx 24rpx;
}

.upload-tip {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 16rpx;
    gap: 8rpx;
}

.upload-tip text {
    font-size: 24rpx;
    color: #999;
}

/* 提交按钮区域 */
.submit-section {
    padding: 40rpx 20rpx;
    text-align: center;
}

.submit-btn {
    width: 100%;
    height: 96rpx;
    font-size: 32rpx;
    font-weight: bold;
    border-radius: 50rpx;
    box-shadow: 0 8rpx 24rpx rgba(0, 0, 0, 0.12);
}

.submit-tip {
    margin-top: 20rpx;
    font-size: 24rpx;
    color: #999;
}
</style>
