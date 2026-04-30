<!--
    开发者公众号：杰哥网络科技
    QQ: 2711793818 杰哥
-->
<template>
    <view class="merchant-apply-page">
        <!-- 加载中 -->
        <view v-if="loading" class="flex justify-center pt-20">
            <u-loading-icon mode="circle"></u-loading-icon>
        </view>

        <!-- 商家工作台 -->
        <view v-else-if="status === 2" class="merchant-workbench">
            <!-- 头部数据看板 -->
            <view
                class="header-card bg-gradient-to-r from-blue-600 to-blue-500 p-6 text-white mb-4"
            >
                <view class="flex items-center mb-6">
                    <u-avatar :src="merchantInfo.image" size="50"></u-avatar>
                    <view class="ml-3">
                        <view class="text-lg font-bold">{{ merchantInfo.name }}</view>
                        <view class="text-xs opacity-80 mt-1">ID: {{ merchantInfo.id }}</view>
                    </view>
                    <view
                        class="ml-auto bg-white/20 px-3 py-1 rounded-full text-xs"
                        @click="handleEdit"
                    >
                        店铺设置 >
                    </view>
                </view>

                <view class="grid grid-cols-2 gap-4">
                    <view>
                        <view class="text-xs opacity-80 mb-1">今日收入(元)</view>
                        <view class="text-2xl font-bold">{{
                            financeInfo.today_income || '0.00'
                        }}</view>
                    </view>
                    <view>
                        <view class="text-xs opacity-80 mb-1">累计收入(元)</view>
                        <view class="text-2xl font-bold">{{
                            financeInfo.total_income || '0.00'
                        }}</view>
                    </view>
                </view>
            </view>

            <!-- 功能菜单 -->
            <view class="menu-grid bg-white p-4 rounded-lg shadow-sm mx-3">
                <view class="font-bold mb-4 text-gray-800">常用功能</view>
                <view class="grid grid-cols-4 gap-4">
                    <view
                        class="flex flex-col items-center"
                        @click="goPage('/pages/business/article-publish')"
                    >
                        <view
                            class="w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center mb-2"
                        >
                            <u-icon name="edit-pen" color="#2979ff" size="24"></u-icon>
                        </view>
                        <text class="text-xs text-gray-600">发布文章</text>
                    </view>
                    <view class="flex flex-col items-center">
                        <view
                            class="w-10 h-10 bg-green-50 rounded-full flex items-center justify-center mb-2"
                        >
                            <u-icon name="list-dot" color="#19be6b" size="24"></u-icon>
                        </view>
                        <text class="text-xs text-gray-600">文章管理</text>
                    </view>
                    <view class="flex flex-col items-center">
                        <view
                            class="w-10 h-10 bg-orange-50 rounded-full flex items-center justify-center mb-2"
                        >
                            <u-icon name="rmb-circle" color="#ff9900" size="24"></u-icon>
                        </view>
                        <text class="text-xs text-gray-600">提现记录</text>
                    </view>
                    <view class="flex flex-col items-center">
                        <view
                            class="w-10 h-10 bg-purple-50 rounded-full flex items-center justify-center mb-2"
                        >
                            <u-icon name="kefu-ermai" color="#7d33ff" size="24"></u-icon>
                        </view>
                        <text class="text-xs text-gray-600">客户消息</text>
                    </view>
                </view>
            </view>
        </view>

        <!-- 审核中 -->
        <view v-else-if="status === 1" class="pt-20 flex flex-col items-center">
            <u-icon name="clock" size="80" color="#ff9900"></u-icon>
            <view class="mt-4 text-lg font-bold text-gray-800">审核中</view>
            <view class="mt-2 text-gray-500 text-sm px-10 text-center"
                >您的入驻申请正在审核中，请耐心等待，审核结果将通过消息通知您。</view
            >
            <u-button class="mt-10 w-40" shape="circle" @click="uni.navigateBack()">返回</u-button>
        </view>

        <!-- 申请被拒 -->
        <view v-else-if="status === 3" class="pt-20 flex flex-col items-center">
            <u-icon name="close-circle" size="80" color="#fa3534"></u-icon>
            <view class="mt-4 text-lg font-bold text-gray-800">申请未通过</view>
            <view class="mt-2 text-gray-500 text-sm px-10 text-center"
                >原因：{{ applyInfo.audit_remark || '不符合入驻要求' }}</view
            >
            <u-button class="mt-10 w-40" type="primary" shape="circle" @click="reApply"
                >重新申请</u-button
            >
        </view>

        <!-- 未申请/重新申请表单 -->
        <view v-else class="pt-4">
            <MerchantForm mode="create" @submit="handleSubmit" />
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { onShow } from '@dcloudio/uni-app'
import MerchantForm from '@/components/business/MerchantForm.vue'
import {
    merchantApply,
    getApplyDetail,
    getMerchantDetail,
    getMerchantFinance
} from '@/api/merchant'

const loading = ref(true)
const status = ref(0) // 0-未申请, 1-审核中, 2-已通过, 3-已拒绝
const applyInfo = ref<any>({})
const merchantInfo = ref<any>({})
const financeInfo = ref<any>({})

const checkStatus = async () => {
    try {
        loading.value = true
        // 先检查是否有商家详情（判断是否已是商家）
        try {
            const detail = await getMerchantDetail()
            if (detail && detail.id) {
                status.value = 2
                merchantInfo.value = detail
                getFinance() // 获取资金信息
                uni.setNavigationBarTitle({ title: '商家工作台' })
                return
            }
        } catch (e) {
            // 不是商家，继续检查申请状态
        }

        const res = await getApplyDetail()
        if (res) {
            // 后端状态: 0-待审核, 1-已通过, 2-已拒绝
            // 前端状态: 0-未申请, 1-审核中, 2-已通过, 3-已拒绝
            // 需要进行映射转换
            const backendStatus = res.status
            if (backendStatus === 0) {
                status.value = 1 // 待审核 -> 审核中
            } else if (backendStatus === 1) {
                status.value = 2 // 已通过 -> 已通过
            } else if (backendStatus === 2) {
                status.value = 3 // 已拒绝 -> 已拒绝
            } else {
                status.value = 0
            }
            applyInfo.value = res
            if (status.value === 1) {
                uni.setNavigationBarTitle({ title: '审核中' })
            } else if (status.value === 3) {
                uni.setNavigationBarTitle({ title: '申请未通过' })
            }
        } else {
            status.value = 0
            uni.setNavigationBarTitle({ title: '商家入驻' })
        }
    } catch (e) {
        status.value = 0
    } finally {
        loading.value = false
    }
}

const getFinance = async () => {
    try {
        const res = await getMerchantFinance()
        financeInfo.value = res || {}
    } catch (e) {
        console.error(e)
    }
}

const handleSubmit = async (data: any) => {
    uni.showLoading({ title: '提交中...' })
    try {
        const res = await merchantApply(data)
        uni.showToast({ title: res.msg || '提交成功', icon: 'success' })
        setTimeout(() => {
            checkStatus()
        }, 1500)
    } catch (e: any) {
        uni.showToast({ title: e.msg || e.message || '提交失败，请重试', icon: 'none' })
    } finally {
        uni.hideLoading()
    }
}

const reApply = () => {
    status.value = 0
}

const goPage = (url: string) => {
    uni.navigateTo({ url })
}

const handleEdit = () => {
    // TODO: 跳转到店铺设置页
    uni.showToast({ title: '功能开发中', icon: 'none' })
}

onShow(() => {
    checkStatus()
})
</script>

<style lang="scss" scoped>
.merchant-apply-page {
    min-height: 100vh;
    background-color: #f8f8f8;
}
.merchant-workbench {
    .header-card {
        border-radius: 0 0 30rpx 30rpx;
    }
}
</style>
