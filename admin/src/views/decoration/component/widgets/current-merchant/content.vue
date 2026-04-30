<!--
  开发者公众号：杰哥网络科技。
  qq2711793818 杰哥
-->
<template>
    <div class="current-merchant-content">
        <!-- 无商家状态 -->
        <div v-if="!content.show_has_merchant" class="no-merchant-container">
            <div class="no-merchant-content">
                <div class="no-merchant-icon">
                    <el-icon :size="60" :color="styles.primary_color || '#FF2D3A'"><Shop /></el-icon>
                </div>
                <div class="no-merchant-title">暂无关注的商家</div>
                <div class="no-merchant-desc">关注商家后可查看最新动态和资料</div>
                <div class="no-merchant-btns" :class="{ 'single-btn': !content.show_apply_btn }">
                    <div v-if="content.show_apply_btn" class="no-merchant-btn apply-btn" :style="{ color: styles.primary_color || '#FF2D3A', borderColor: styles.primary_color || '#FF2D3A' }">
                        <el-icon :size="18"><Plus /></el-icon>
                        <span>申请做商家</span>
                    </div>
                    <div class="no-merchant-btn follow-btn" :style="{ backgroundColor: styles.primary_color || '#FF2D3A' }">
                        <el-icon :size="18" color="#fff"><Star /></el-icon>
                        <span>新增关注</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 有商家状态 -->
        <div v-else class="has-merchant-container">
            <!-- 顶部导航栏 -->
            <div class="header-bar" :style="{ background: styles.primary_color || '#FF2D3A' }">
                <div class="header-left">
                    <div class="logo-placeholder">
                        <el-icon :size="20" color="#fff"><Shop /></el-icon>
                    </div>
                    <div class="header-title">平台名称</div>
                </div>
                <div v-if="content.show_switch" class="switch-btn">切换商家</div>
            </div>
            
            <!-- 商家信息区域 -->
            <div class="merchant-header-bg" :style="{ background: styles.primary_color || '#FF2D3A' }">
                <div class="merchant-card">
                    <div class="merchant-info">
                        <div class="avatar">
                            <el-icon :size="40" color="#999"><User /></el-icon>
                        </div>
                        <div class="info">
                            <div class="name" :style="{ color: styles.title_color || '#333' }">商家名称</div>
                            <div class="ratio-tag" :style="{ background: styles.primary_color || '#FF2D3A' }">
                                推广员10%分成
                            </div>
                            <div class="desc" :style="{ color: styles.desc_color || '#666' }">
                                这是商家的简介信息，展示商家的特色和优势，让用户更好地了解商家。
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 操作按钮 -->
                <div v-if="content.show_actions" class="action-bar">
                    <div class="action-item">
                        <div class="action-icon" :style="{ background: (styles.primary_color || '#FF2D3A') + '40' }">
                            <el-icon :size="20" color="#fff"><ChatDotRound /></el-icon>
                        </div>
                        <span>商家微信</span>
                    </div>
                    <div class="action-item">
                        <div class="action-icon" :style="{ background: (styles.primary_color || '#FF2D3A') + '40' }">
                            <el-icon :size="20" color="#fff"><Share /></el-icon>
                        </div>
                        <span>推广TA</span>
                    </div>
                    <div class="action-item">
                        <div class="action-icon" :style="{ background: (styles.primary_color || '#FF2D3A') + '40' }">
                            <el-icon :size="20" color="#fff"><ChatLineRound /></el-icon>
                        </div>
                        <span>私聊</span>
                    </div>
                    <div class="action-item">
                        <div class="action-icon" :style="{ background: (styles.primary_color || '#FF2D3A') + '40' }">
                            <el-icon :size="20" color="#fff"><Service /></el-icon>
                        </div>
                        <span>投诉反馈</span>
                    </div>
                </div>
            </div>
            
            <!-- Tab栏 -->
            <div v-if="content.show_tabs" class="tabs-bar">
                <div 
                    v-for="(tab, index) in tabs" 
                    :key="index"
                    class="tab-item"
                    :class="{ 'active': currentTab === index }"
                    @click="currentTab = index"
                >
                    <span :style="{ color: currentTab === index ? (styles.primary_color || '#FF2D3A') : '#666' }">{{ tab }}</span>
                    <div v-if="currentTab === index" class="tab-line" :style="{ background: styles.primary_color || '#FF2D3A' }"></div>
                </div>
            </div>
            
            <!-- 搜索栏 -->
            <div v-if="content.show_tabs && currentTab === 0 && content.show_search" class="search-bar">
                <div class="filter-btn">
                    <el-icon :size="14"><Filter /></el-icon>
                    <span>筛选</span>
                </div>
                <div class="search-input">
                    <el-icon :size="14" color="#999"><Search /></el-icon>
                    <span>请输入关键词搜索资料</span>
                </div>
            </div>
            
            <!-- 内容列表 -->
            <div v-if="content.show_tabs && content.show_content_list && currentTab === 0" class="content-list">
                <div class="content-item" v-for="i in 2" :key="i">
                    <div class="content-info">
                        <div class="content-title">资料标题示例，这是一条优质的资料内容标题</div>
                        <div class="content-desc">资料简介描述信息，展示资料的主要内容和价值点</div>
                        <div class="content-meta">
                            <span>2024-01-01</span>
                            <span class="merchant-tag">商家名称</span>
                        </div>
                    </div>
                    <div class="content-right">
                        <div class="price-tag" :style="{ color: styles.primary_color || '#FF2D3A' }">
                            <span class="price-unit">¥</span>
                            <span class="price-value">38</span>
                        </div>
                        <div class="buy-btn" :style="{ background: styles.primary_color || '#FF2D3A' }">立即查看</div>
                    </div>
                </div>
            </div>
            
            <!-- 优惠券列表 -->
            <div v-if="content.show_tabs && currentTab === 1 && content.show_coupon" class="coupon-list">
                <div class="coupon-item" v-for="i in 2" :key="i">
                    <div class="coupon-left">
                        <div class="coupon-amount">¥10</div>
                        <div class="coupon-condition">满50可用</div>
                    </div>
                    <div class="coupon-right">
                        <div class="coupon-name">新人专享优惠券</div>
                        <div class="coupon-desc">适用于指定商品</div>
                        <div class="coupon-btn" :style="{ color: styles.primary_color || '#FF2D3A' }">立即领取</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { User, ChatDotRound, Star, ChatLineRound, Service, Filter, Search, Shop, Share, Plus } from '@element-plus/icons-vue'

const props = defineProps({
    content: {
        type: Object,
        default: () => ({
            show_switch: true,
            show_actions: true,
            show_tabs: true,
            show_search: true,
            show_content_list: true,
            show_coupon: true,
            show_apply_btn: true,
            show_has_merchant: true
        })
    },
    styles: {
        type: Object,
        default: () => ({
            primary_color: '#FF2D3A',
            title_color: '#333',
            desc_color: '#666',
            margin_top: 0,
            margin_bottom: 10,
            padding_horizontal: 12,
            border_radius: 12
        })
    }
})

const currentTab = ref(0)
const tabs = ['TA的料', '优惠券']
</script>

<style scoped>
.current-merchant-content {
    width: 375px;
    background: #f5f5f5;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    overflow: hidden;
}

/* 无商家状态 */
.no-merchant-container {
    width: 100%;
    min-height: 400px;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #fff;
}

.no-merchant-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 60px 40px;
}

.no-merchant-icon {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(255, 45, 58, 0.1) 0%, rgba(255, 107, 107, 0.1) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
}

.no-merchant-title {
    font-size: 18px;
    font-weight: bold;
    color: #333;
    margin-bottom: 10px;
}

.no-merchant-desc {
    font-size: 14px;
    color: #999;
    margin-bottom: 30px;
}

.no-merchant-btns {
    display: flex;
    gap: 12px;
    width: 100%;
    justify-content: center;
}

.no-merchant-btns.single-btn {
    justify-content: center;
}

.no-merchant-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 12px 30px;
    border-radius: 25px;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
}

.no-merchant-btn.apply-btn {
    background: #fff;
    border: 1px solid;
}

.no-merchant-btn.follow-btn {
    color: #fff;
}

/* 有商家状态 */
.has-merchant-container {
    background: #f5f5f5;
}

.header-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 15px;
    color: #fff;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 8px;
}

.logo-placeholder {
    width: 28px;
    height: 28px;
    background: rgba(255, 255, 255, 0.25);
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.header-title {
    font-weight: bold;
    font-size: 16px;
}

.switch-btn {
    font-size: 12px;
    background: rgba(255, 255, 255, 0.25);
    padding: 6px 12px;
    border-radius: 15px;
}

.merchant-header-bg {
    padding-bottom: 20px;
}

.merchant-card {
    margin: 0 12px;
    background: #fff;
    border-radius: 12px;
    padding: 16px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
}

.merchant-info {
    display: flex;
    align-items: flex-start;
}

.avatar {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border: 3px solid rgba(255, 45, 58, 0.2);
}

.info {
    flex: 1;
    margin-left: 12px;
}

.name {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 6px;
}

.ratio-tag {
    display: inline-block;
    font-size: 11px;
    color: #fff;
    padding: 3px 10px;
    border-radius: 10px;
    margin-bottom: 6px;
}

.desc {
    font-size: 13px;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.action-bar {
    display: flex;
    justify-content: space-around;
    padding: 16px 10px;
}

.action-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
}

.action-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 6px;
}

.action-item span {
    font-size: 12px;
    color: #fff;
}

.tabs-bar {
    display: flex;
    background: #fff;
    border-bottom: 1px solid #f0f0f0;
}

.tab-item {
    flex: 1;
    padding: 14px 0;
    text-align: center;
    cursor: pointer;
    position: relative;
}

.tab-item span {
    font-size: 14px;
}

.tab-item.active span {
    font-weight: bold;
}

.tab-line {
    width: 24px;
    height: 3px;
    border-radius: 2px;
    margin: 6px auto 0;
}

.search-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px;
    background: #fff;
}

.filter-btn {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 8px 14px;
    background: #f5f5f5;
    border-radius: 18px;
    font-size: 13px;
    color: #666;
}

.search-input {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    background: #f5f5f5;
    border-radius: 18px;
    font-size: 13px;
    color: #999;
}

.content-list {
    padding: 12px;
    background: #f5f5f5;
}

.content-item {
    display: flex;
    justify-content: space-between;
    background: #fff;
    border-radius: 12px;
    padding: 14px;
    margin-bottom: 10px;
    border: 1px solid #f0f0f0;
}

.content-info {
    flex: 1;
    padding-right: 12px;
}

.content-title {
    font-size: 15px;
    font-weight: bold;
    color: #333;
    margin-bottom: 6px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.4;
}

.content-desc {
    font-size: 13px;
    color: #999;
    margin-bottom: 8px;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.content-meta {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: #999;
}

.merchant-tag {
    background: rgba(255, 45, 58, 0.1);
    color: #FF2D3A;
    padding: 2px 8px;
    border-radius: 10px;
}

.content-right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    justify-content: space-between;
    min-width: 60px;
}

.price-tag {
    display: flex;
    align-items: baseline;
}

.price-unit {
    font-size: 12px;
}

.price-value {
    font-size: 20px;
    font-weight: bold;
}

.buy-btn {
    color: #fff;
    font-size: 12px;
    padding: 5px 14px;
    border-radius: 15px;
}

.coupon-list {
    padding: 12px;
    background: #f5f5f5;
}

.coupon-item {
    display: flex;
    background: linear-gradient(135deg, #FF2D3A 0%, #FF6B6B 100%);
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 10px;
    color: #fff;
}

.coupon-left {
    min-width: 80px;
    text-align: center;
    border-right: 1px dashed rgba(255, 255, 255, 0.3);
    padding-right: 12px;
}

.coupon-amount {
    font-size: 28px;
    font-weight: bold;
}

.coupon-condition {
    font-size: 12px;
    opacity: 0.9;
    margin-top: 4px;
}

.coupon-right {
    flex: 1;
    padding-left: 12px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.coupon-name {
    font-size: 15px;
    font-weight: bold;
}

.coupon-desc {
    font-size: 12px;
    opacity: 0.8;
    margin-top: 4px;
}

.coupon-btn {
    display: inline-block;
    padding: 5px 14px;
    background: #fff;
    border-radius: 15px;
    font-size: 12px;
    font-weight: bold;
    margin-top: 8px;
    align-self: flex-end;
}
</style>