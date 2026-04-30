<!--
    文章详情组件
    开发者：杰哥网络科技
    QQ：2711793818 杰哥
    
    ⚠️ 注意：此组件已废弃，请统一使用 /pages/news_detail/news_detail.vue
    原因：
    1. 功能重复，与 news_detail.vue 功能相同
    2. 统一使用 news_detail.vue 可以更好的维护
    3. 支持平台文章和商户文章的统一展示
    
    废弃日期：2026-03-05
    替代方案：使用 /pages/news_detail/news_detail.vue
-->
<template>
    <view class="article-detail-widget">
        <!-- 此组件已废弃，显示提示信息 -->
        <view class="deprecated-notice">
            <text>⚠️ 此组件已废弃，请使用 /pages/news_detail/news_detail.vue</text>
        </view>
        <view v-if="loading" class="loading">
            <view class="loading-spinner"></view>
            <text>加载中...</text>
        </view>
        <view v-else-if="article" class="detail-box">
            <view class="article-header">
                <view class="title">{{ article.title }}</view>
                <view class="meta">
                    <view class="meta-item">
                        <text class="meta-icon">📅</text>
                        <text class="time">{{ article.create_time }}</text>
                    </view>
                    <view class="meta-divider"></view>
                    <view class="meta-item">
                        <text class="meta-icon">👁️</text>
                        <text class="views">阅读 {{ article.visit || 0 }}</text>
                    </view>
                    <view v-if="article.author" class="meta-item">
                        <text class="meta-icon">✍️</text>
                        <text class="author">作者：{{ article.author || '平台' }}</text>
                    </view>
                </view>
            </view>

            <!-- 顶部注意事项提示 -->
            <view v-if="articleTips.top_tips_show && articleTips.top_tips" class="top-tips-box">
                <view class="tips-icon-wrapper">
                    <text class="tips-icon">⚠️</text>
                </view>
                <view class="tips-content">
                    <view class="tips-label">温馨提示</view>
                    <rich-text :nodes="articleTips.top_tips"></rich-text>
                </view>
            </view>

            <view class="content-wrapper">
                <view class="content" :class="{ blur: !article.is_paid && article.price > 0 }">
                    <rich-text :nodes="article.content"></rich-text>
                </view>
            </view>

            <!-- 底部购买须知 -->
            <view
                v-if="articleTips.bottom_tips_show && articleTips.bottom_tips"
                class="bottom-tips-box"
            >
                <view class="tips-header">
                    <text class="tips-title-icon">📌</text>
                    <text class="tips-title">购买须知</text>
                </view>
                <view class="tips-divider"></view>
                <view class="tips-content">
                    <rich-text :nodes="articleTips.bottom_tips"></rich-text>
                </view>
            </view>

            <view v-if="!article.is_paid && article.price > 0" class="pay-mask">
                <view class="pay-mask-content">
                    <view class="pay-info">
                        <view class="lock-icon">🔒</view>
                        <view class="tips">此文章为付费内容</view>
                        <view class="price-wrapper">
                            <text class="price-symbol">¥</text>
                            <text class="price">{{ article.price }}</text>
                        </view>
                    </view>
                    <button class="pay-btn" @click="handlePay">
                        <text>立即购买</text>
                    </button>
                </view>
            </view>

            <view v-if="content.style === 'detail'" class="footer-info">
                <view class="footer-divider"></view>
                <view class="footer-content">
                    <view class="footer-item">
                        <text class="footer-icon">👤</text>
                        <text>作者：{{ article.author || '平台' }}</text>
                    </view>
                    <view class="footer-item">
                        <text class="footer-icon">©️</text>
                        <text>声明：本文版权归原作者所有</text>
                    </view>
                </view>
            </view>
        </view>
        <view v-else class="empty">
            <view class="empty-icon">📄</view>
            <text>文章不存在</text>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import request from '@/utils/request'
import { useUserStore } from '@/stores/user'

const userStore = useUserStore()

const props = defineProps({
    content: {
        type: Object,
        default: () => ({})
    },
    styles: {
        type: Object,
        default: () => ({})
    }
})

const article = ref<any>(null)
const loading = ref(false)
const articleTips = ref({
    top_tips: '',
    top_tips_show: 0,
    bottom_tips: '',
    bottom_tips_show: 0
})

const getArticleTips = async () => {
    try {
        const res = await request.get({
            url: '/system/articleTips'
        })
        articleTips.value = res
    } catch (e) {
        console.error('获取文章提示配置失败', e)
    }
}

const getDetail = async () => {
    const pages = getCurrentPages()
    const currentPage = pages[pages.length - 1]
    // @ts-ignore
    const id = currentPage.options?.id || 1

    loading.value = true
    try {
        const res = await request.get({
            url: '/article/detail',
            data: { id }
        })
        article.value = res
    } catch (e) {
        article.value = {
            id: id,
            title: '示例付费文章',
            create_time: '2023-06-01',
            visit: 999,
            content: '<div>这里是文章正文内容...<br>这里是文章正文内容...</div>',
            price: 9.99,
            is_paid: false
        }
    } finally {
        loading.value = false
    }
}

const handlePay = async () => {
    if (!article.value || !article.value.id) return

    if (!userStore.isLogin) {
        uni.navigateTo({ url: '/pages/login/login' })
        return
    }

    const price = Number(article.value.price)
    const balance = Number(userStore.userInfo.user_money || 0)

    if (balance < price) {
        uni.showModal({
            title: '余额不足',
            content: `当前余额 ¥${balance}，文章价格 ¥${price}，请先充值。`,
            confirmText: '去充值',
            success: (res) => {
                if (res.confirm) {
                    uni.navigateTo({ url: '/packages/pages/recharge/recharge' })
                }
            }
        })
        return
    }

    uni.showModal({
        title: '提示',
        content: `确定支付 ${article.value.price} 元购买此文章吗？`,
        success: async (res) => {
            if (res.confirm) {
                uni.showLoading({ title: '支付中...' })
                try {
                    await request.post({
                        url: '/article/buy',
                        data: { id: article.value.id }
                    })

                    uni.hideLoading()
                    uni.showToast({ title: '购买成功', icon: 'success' })

                    article.value.is_paid = true
                    userStore.getUser()
                } catch (e) {
                    uni.hideLoading()
                }
            }
        }
    })
}

onMounted(() => {
    getDetail()
    getArticleTips()
})
</script>

<style scoped>
.article-detail-widget {
    background-color: #f5f7fa;
    padding: 0;
    min-height: 100vh;
}

.deprecated-notice {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeeba 100%);
    border: 2rpx solid #ffc107;
    border-radius: 16rpx;
    padding: 30rpx;
    margin: 30rpx;
    text-align: center;
}

.deprecated-notice text {
    font-size: 28rpx;
    color: #856404;
    font-weight: 600;
}

.detail-box {
    background-color: #fff;
}

.article-header {
    padding: 20px 16px;
    background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
    border-bottom: 1px solid #f0f0f0;
}

.title {
    font-size: 22px;
    font-weight: 700;
    color: #1a1a1a;
    line-height: 1.4;
    margin-bottom: 12px;
}

.meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 13px;
    color: #999;
}

.meta-icon {
    font-size: 14px;
}

.meta-divider {
    width: 1px;
    height: 12px;
    background-color: #e0e0e0;
}

.loading {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 80px 0;
    gap: 16px;
}

.loading-spinner {
    width: 40px;
    height: 40px;
    border: 3px solid #f0f0f0;
    border-top-color: #1989fa;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 80px 0;
    gap: 12px;
    color: #999;
}

.empty-icon {
    font-size: 48px;
}

/* 顶部提示样式 */
.top-tips-box {
    background: linear-gradient(135deg, #fff9e6 0%, #fff8e1 100%);
    border: 1px solid #ffecb3;
    border-radius: 16px;
    padding: 16px;
    margin: 16px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.tips-icon-wrapper {
    flex-shrink: 0;
    margin-top: 2px;
}

.top-tips-box .tips-icon {
    font-size: 20px;
}

.tips-label {
    font-size: 14px;
    font-weight: 600;
    color: #f57c00;
    margin-bottom: 6px;
}

.top-tips-box .tips-content {
    flex: 1;
    font-size: 14px;
    color: #8b6914;
    line-height: 1.6;
}

.content-wrapper {
    padding: 16px;
}

.content {
    font-size: 16px;
    line-height: 1.8;
    color: #333;
}

.blur {
    filter: blur(4px);
    user-select: none;
    pointer-events: none;
    height: 200px;
    overflow: hidden;
}

/* 底部提示样式 */
.bottom-tips-box {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 16px;
    padding: 16px;
    margin: 16px;
}

.tips-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
}

.tips-title-icon {
    font-size: 18px;
}

.bottom-tips-box .tips-title {
    font-size: 16px;
    font-weight: 700;
    color: #333;
}

.tips-divider {
    height: 1px;
    background-color: #e9ecef;
    margin-bottom: 12px;
}

.bottom-tips-box .tips-content {
    font-size: 14px;
    color: #666;
    line-height: 1.8;
}

.pay-mask {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(10px);
    padding: 16px 16px 32px;
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
    border-top-left-radius: 24px;
    border-top-right-radius: 24px;
}

.pay-mask-content {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
}

.pay-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.lock-icon {
    font-size: 20px;
}

.pay-mask .tips {
    font-size: 12px;
    color: #999;
}

.price-wrapper {
    display: flex;
    align-items: baseline;
    gap: 2px;
}

.price-symbol {
    font-size: 16px;
    color: #ff5500;
    font-weight: 600;
}

.pay-mask .price {
    font-size: 28px;
    color: #ff5500;
    font-weight: 800;
}

.pay-btn {
    background: linear-gradient(135deg, #1989fa 0%, #0d73e0 100%);
    color: #fff;
    border: none;
    border-radius: 24px;
    font-size: 15px;
    font-weight: 600;
    padding: 12px 32px;
    box-shadow: 0 4px 12px rgba(25, 137, 250, 0.3);
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.pay-btn:active {
    transform: scale(0.98);
    box-shadow: 0 2px 6px rgba(25, 137, 250, 0.3);
}

.footer-info {
    margin-top: 24px;
    padding: 20px 16px 32px;
}

.footer-divider {
    height: 1px;
    background: linear-gradient(90deg, transparent, #e0e0e0, transparent);
    margin-bottom: 16px;
}

.footer-content {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.footer-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #999;
}

.footer-icon {
    font-size: 14px;
}
</style>
