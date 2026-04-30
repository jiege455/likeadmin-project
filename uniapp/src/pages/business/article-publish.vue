<!--
    开发者公众号：杰哥网络科技
    QQ: 2711793818 杰哥
-->
<template>
    <view class="article-publish-page">
        <view class="form-container">
            <!-- 标题 -->
            <view class="form-item">
                <text class="label">文章标题</text>
                <input class="input" v-model="formData.title" placeholder="请输入文章标题" />
            </view>

            <!-- 价格设置 -->
            <view class="form-item row-between">
                <text class="label">是否付费</text>
                <switch :checked="formData.is_paid" @change="handlePaidChange" color="#1989fa" />
            </view>

            <view class="form-item" v-if="formData.is_paid">
                <text class="label">价格 (元)</text>
                <input class="input" type="digit" v-model="formData.price" placeholder="0.00" />
            </view>

            <!-- 隐藏内容（付费后可见） -->
            <view class="form-item" v-if="formData.is_paid">
                <text class="label">隐藏内容 (付费后可见)</text>
                <textarea
                    class="textarea"
                    v-model="formData.hidden_content"
                    placeholder="请输入网盘链接、提取码或解压密码等，仅付费用户可见"
                />
            </view>

            <!-- 分销设置（付费文章） -->
            <view class="form-item" v-if="formData.is_paid">
                <view class="row-between">
                    <view>
                        <text class="label">开启分销</text>
                        <text class="sub-label">推广员推广成功后可获得佣金</text>
                    </view>
                    <switch
                        :checked="formData.distribution_switch"
                        @change="handleDistributionChange"
                        color="#1989fa"
                    />
                </view>

                <view v-if="formData.distribution_switch" class="distribution-setting">
                    <view class="ratio-row">
                        <text class="ratio-label">分销比例</text>
                        <view class="ratio-input-box">
                            <input
                                class="ratio-input"
                                type="digit"
                                v-model="formData.commission_ratio"
                                placeholder="10"
                            />
                            <text class="ratio-unit">%</text>
                        </view>
                    </view>
                    <text class="ratio-tip"
                        >比例范围：{{ minRatio }}% - {{ maxRatio }}%，留空则使用店铺默认比例</text
                    >
                </view>
            </view>

            <!-- 内容编辑器 -->
            <view class="editor-container">
                <text class="label">文章内容</text>
                <ArticleEditor v-model:content="formData.content" />
            </view>

            <!-- 提交按钮 -->
            <view class="submit-btn" @click="handleSubmit">
                <text>发布文章</text>
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import ArticleEditor from '@/components/business/ArticleEditor.vue'
import request from '@/utils/request'
import { safeNavigateBack } from '@/utils/util'

const formData = ref({
    title: '',
    is_paid: false,
    price: '',
    hidden_content: '',
    distribution_switch: true,
    commission_ratio: '',
    content: ''
})

const minRatio = ref(0)
const maxRatio = ref(50)
const defaultRatio = ref(10)

const handlePaidChange = (e: any) => {
    formData.value.is_paid = e.detail.value
}

const handleDistributionChange = (e: any) => {
    formData.value.distribution_switch = e.detail.value
}

const getDistributionConfig = async () => {
    try {
        const res = await request.get({ url: '/merchant.distribution/getSetting' })
        defaultRatio.value = res.distribution_ratio || 10
        if (!formData.value.commission_ratio) {
            formData.value.commission_ratio = ''
        }
    } catch (e) {}
}

const handleSubmit = async () => {
    if (!formData.value.title) return uni.showToast({ title: '请输入标题', icon: 'none' })
    if (!formData.value.content) return uni.showToast({ title: '请输入内容', icon: 'none' })

    if (formData.value.is_paid) {
        if (!formData.value.price || parseFloat(formData.value.price) <= 0) {
            return uni.showToast({ title: '请输入正确的价格', icon: 'none' })
        }
    }

    if (formData.value.distribution_switch && formData.value.commission_ratio) {
        const ratio = parseFloat(formData.value.commission_ratio)
        if (ratio < minRatio.value || ratio > maxRatio.value) {
            return uni.showToast({
                title: `分销比例需在${minRatio.value}%-${maxRatio.value}%之间`,
                icon: 'none'
            })
        }
    }

    uni.showLoading({ title: '发布中...' })

    try {
        const submitData: any = {
            title: formData.value.title,
            content: formData.value.content,
            is_show: 1
        }

        if (formData.value.is_paid) {
            submitData.price = parseFloat(formData.value.price)
            submitData.hidden_content = formData.value.hidden_content
            submitData.distribution_switch = formData.value.distribution_switch ? 1 : 0
            if (formData.value.commission_ratio) {
                submitData.commission_ratio = parseFloat(formData.value.commission_ratio)
            }
        }

        await request.post({ url: '/merchant.article/save', data: submitData })
        uni.hideLoading()
        uni.showToast({ title: '发布成功', icon: 'success' })
        setTimeout(() => {
            safeNavigateBack({ defaultUrl: '/pages/index/index' })
        }, 1500)
    } catch (e: any) {
        uni.hideLoading()
        uni.showToast({ title: e?.msg || '发布失败', icon: 'none' })
    }
}

onMounted(() => {
    getDistributionConfig()
})
</script>

<style lang="scss" scoped>
.article-publish-page {
    min-height: 100vh;
    background-color: #f8f8f8;
    padding-bottom: 40rpx;
}
.form-container {
    padding: 20rpx;
}
.form-item {
    background-color: #fff;
    padding: 30rpx;
    border-radius: 10rpx;
    margin-bottom: 20rpx;

    .label {
        font-size: 30rpx;
        font-weight: bold;
        margin-bottom: 20rpx;
        display: block;
    }
    .sub-label {
        font-size: 24rpx;
        color: #999;
        display: block;
        margin-top: 8rpx;
    }
    .input {
        font-size: 28rpx;
        height: 60rpx;
        border-bottom: 1px solid #eee;
    }
    .textarea {
        width: 100%;
        height: 160rpx;
        background-color: #f9f9f9;
        border-radius: 8rpx;
        padding: 20rpx;
        font-size: 28rpx;
        box-sizing: border-box;
        margin-top: 10rpx;
    }
}
.row-between {
    display: flex;
    justify-content: space-between;
    align-items: center;
    .label {
        margin-bottom: 0;
    }
}
.distribution-setting {
    margin-top: 30rpx;
    padding-top: 30rpx;
    border-top: 1px solid #eee;
}
.ratio-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.ratio-label {
    font-size: 28rpx;
    color: #333;
}
.ratio-input-box {
    display: flex;
    align-items: center;
}
.ratio-input {
    width: 120rpx;
    height: 60rpx;
    border: 1px solid #ddd;
    border-radius: 8rpx;
    text-align: center;
    font-size: 28rpx;
}
.ratio-unit {
    margin-left: 10rpx;
    font-size: 28rpx;
    color: #666;
}
.ratio-tip {
    font-size: 24rpx;
    color: #999;
    margin-top: 16rpx;
    display: block;
}
.editor-container {
    background-color: #fff;
    padding: 30rpx;
    border-radius: 10rpx;
    margin-bottom: 40rpx;
    .label {
        font-size: 30rpx;
        font-weight: bold;
        margin-bottom: 20rpx;
        display: block;
    }
}
.submit-btn {
    background-color: #1989fa;
    color: #fff;
    height: 88rpx;
    border-radius: 44rpx;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32rpx;
    font-weight: bold;
}
</style>
