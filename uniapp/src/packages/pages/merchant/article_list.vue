<template>
    <uni-nav title="文章管理">
        <template #right>
            <view class="w-10 h-full flex items-center justify-center" @click="toAdd">
                <u-icon name="plus" size="20"></u-icon>
            </view>
        </template>
    </uni-nav>

    <view class="article-list min-h-screen bg-f5">
        <!-- 筛选 -->
        <view
            class="filter bg-white mx-3 mt-3 rounded-2xl p-2 relative z-10 shadow-md flex items-center"
        >
            <view
                class="filter-item"
                :class="filterType === '' ? 'active' : ''"
                :style="
                    filterType === ''
                        ? { backgroundColor: themeStore.primaryColor, color: '#ffffff' }
                        : {}
                "
                @click="filterType = ''"
                >全部</view
            >
            <view
                class="filter-item"
                :class="filterType === 'series' ? 'active' : ''"
                :style="
                    filterType === 'series'
                        ? { backgroundColor: themeStore.primaryColor, color: '#ffffff' }
                        : {}
                "
                @click="filterType = 'series'"
                >系列</view
            >
            <view
                class="filter-item"
                :class="filterType === 'single' ? 'active' : ''"
                :style="
                    filterType === 'single'
                        ? { backgroundColor: themeStore.primaryColor, color: '#ffffff' }
                        : {}
                "
                @click="filterType = 'single'"
                >单篇</view
            >
        </view>

        <!-- 列表区域 -->
        <view class="content-area mx-3 mt-4 relative z-10">
            <view
                v-if="list.length === 0 && !loading"
                class="empty-state bg-white rounded-2xl p-10 text-center"
            >
                <view class="empty-icon">📝</view>
                <view class="empty-text">暂无文章</view>
                <view class="empty-desc">快来发布您的第一篇文章吧~</view>
                <view class="empty-btn">
                    <u-button
                        size="small"
                        type="primary"
                        @click="toAdd"
                        :custom-style="{
                            backgroundColor: themeStore.primaryColor,
                            color: '#ffffff',
                            border: 'none',
                            borderRadius: '24px',
                            padding: '8px 24px'
                        }"
                        >发布文章</u-button
                    >
                </view>
            </view>

            <view v-else class="space-y-3">
                <view
                    v-for="item in list"
                    :key="item.id"
                    class="article-card bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all"
                >
                    <view class="card-content">
                        <view class="card-header">
                            <view class="title-row">
                                <text class="article-title line-clamp-2">{{ item.title }}</text>
                                <view v-if="item.series_id > 0" class="series-badge">
                                    <text class="badge-text">系列</text>
                                </view>
                            </view>
                            <view class="issue-no" v-if="item.series_id > 0 && item.issue_no">
                                <text>第 {{ item.issue_no }} 期</text>
                            </view>
                        </view>

                        <view class="card-footer">
                            <view class="footer-left">
                                <view class="price-tag">
                                    <text class="price-symbol">¥</text>
                                    <text class="price-value">{{ item.price || 0 }}</text>
                                </view>
                                <view
                                    class="status-badge"
                                    :class="item.is_show == 1 ? 'published' : 'hidden'"
                                >
                                    {{ item.is_show == 1 ? '已发布' : '已隐藏' }}
                                </view>
                            </view>
                            <view class="footer-right">
                                <text class="create-time">{{ item.create_time }}</text>
                            </view>
                        </view>
                    </view>

                    <!-- 操作按钮 -->
                    <view class="action-bar">
                        <view
                            class="action-btn preview-btn"
                            @click="toPreview(item)"
                        >
                            <u-icon name="eye" size="14"></u-icon>
                            <text>预览</text>
                        </view>
                        <view
                            class="action-btn edit-btn"
                            :style="{
                                backgroundColor: themeStore.primaryColor + '15',
                                color: themeStore.primaryColor,
                                borderColor: themeStore.primaryColor + '30'
                            }"
                            @click="toEdit(item)"
                        >
                            <u-icon name="edit" size="14"></u-icon>
                            <text>编辑</text>
                        </view>
                        <view class="action-btn delete-btn" @click="handleDelete(item)">
                            <u-icon name="trash" size="14"></u-icon>
                            <text>删除</text>
                        </view>
                    </view>
                </view>
            </view>

            <!-- 加载更多 -->
            <view v-if="hasMore" class="load-more" @click="loadMore">
                {{ loading ? '加载中...' : '加载更多' }}
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch } from 'vue'
import request from '@/utils/request'
import { onShow, onReachBottom } from '@dcloudio/uni-app'
import { useThemeStore } from '@/stores/theme'
import { safeNavigateBack } from '@/utils/util'

const themeStore = useThemeStore()
const themeStyle = computed(() => `--color-primary: ${themeStore.primaryColor};`)

const list = ref<any[]>([])
const loading = ref(false)
const hasMore = ref(true)
const page = ref(1)
const filterType = ref('')

const statistics = reactive({
    total: 0,
    show: 0
})

watch(filterType, () => {
    getList(true)
})

const getList = async (refresh = false) => {
    if (loading.value) return
    if (refresh) {
        page.value = 1
        hasMore.value = true
    }

    loading.value = true
    try {
        const data: any = {
            page_no: page.value,
            page_size: 10
        }

        if (filterType.value === 'series') {
            data.series_id = 1
        } else if (filterType.value === 'single') {
            data.series_id = 0
        }

        const res = await request.get({
            url: '/merchant.article/lists',
            data
        })

        if (refresh) {
            list.value = res.lists || []
        } else {
            list.value = [...list.value, ...(res.lists || [])]
        }

        statistics.total = res.count || list.value.length
        statistics.show = list.value.filter((item: any) => item.is_show == 1).length

        hasMore.value = (res.lists || []).length >= 10
    } catch (e) {
        list.value = []
    } finally {
        loading.value = false
    }
}

const loadMore = () => {
    if (!hasMore.value || loading.value) return
    page.value++
    getList()
}

const goBack = () => {
    safeNavigateBack({ defaultUrl: '/pages/index/index' })
}

const toAdd = () => {
    uni.navigateTo({ url: '/packages/pages/merchant/article_add' })
}

const toEdit = (item: any) => {
    uni.navigateTo({ url: `/packages/pages/merchant/article_add?id=${item.id}` })
}

const toPreview = (item: any) => {
    uni.navigateTo({ url: `/pages/news_detail/news_detail?id=${item.id}` })
}

const handleDelete = (item: any) => {
    uni.showModal({
        title: '提示',
        content: '确定要删除该文章吗？',
        success: async (res) => {
            if (res.confirm) {
                try {
                    await request.post({ url: '/merchant.article/delete', data: { id: item.id } })
                    uni.$u.toast('删除成功')
                    getList(true)
                } catch (e) {}
            }
        }
    })
}

onShow(() => {
    uni.setNavigationBarTitle({ title: '文章管理' })
    themeStore.getTheme()
    getList(true)
})

onReachBottom(() => {
    loadMore()
})
</script>

<style lang="scss" scoped>
.article-list {
    background-color: #f5f7fa;
}

.bg-f5 {
    background-color: #f5f7fa;
}

.header {
    border-bottom-left-radius: 24px;
    border-bottom-right-radius: 24px;
}

.stat-card {
    flex: 1;
    text-align: center;
}

.stat-value {
    font-size: 32px;
    font-weight: 800;
    line-height: 1.2;
}

.stat-label {
    font-size: 13px;
    opacity: 0.9;
    margin-top: 4px;
}

.stat-divider {
    width: 1px;
    height: 40px;
    background-color: rgba(255, 255, 255, 0.3);
}

.filter {
    gap: 4px;
}

.filter-item {
    flex: 1;
    text-align: center;
    padding: 10px 0;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 500;
    color: #666;
    transition: all 0.3s ease;
    cursor: pointer;
}

.filter-item:hover {
    background-color: #f5f7fa;
}

.filter-item.active {
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.empty-state {
    border: 1px solid #f0f0f0;
}

.empty-icon {
    font-size: 64px;
    margin-bottom: 16px;
}

.empty-text {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
}

.empty-desc {
    font-size: 14px;
    color: #999;
    margin-bottom: 24px;
}

.empty-btn {
    margin-top: 8px;
}

.article-card {
    border: 1px solid #f0f0f0;
    transition: all 0.3s ease;
}

.card-content {
    padding: 16px;
}

.card-header {
    margin-bottom: 12px;
}

.title-row {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    margin-bottom: 6px;
}

.article-title {
    flex: 1;
    font-size: 16px;
    font-weight: 600;
    color: #1a1a1a;
    line-height: 1.4;
}

.series-badge {
    flex-shrink: 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 3px 10px;
    border-radius: 10px;
}

.badge-text {
    font-size: 11px;
    color: #fff;
    font-weight: 600;
}

.issue-no {
    font-size: 12px;
    color: #999;
}

.card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.footer-left {
    display: flex;
    align-items: center;
    gap: 10px;
}

.price-tag {
    display: flex;
    align-items: baseline;
}

.price-symbol {
    font-size: 14px;
    color: #ff6b35;
    font-weight: 600;
}

.price-value {
    font-size: 20px;
    color: #ff6b35;
    font-weight: 700;
}

.status-badge {
    padding: 3px 10px;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 500;
}

.status-badge.published {
    background-color: #e8f5e9;
    color: #2e7d32;
}

.status-badge.hidden {
    background-color: #f5f5f5;
    color: #999;
}

.create-time {
    font-size: 12px;
    color: #b0b0b0;
}

.action-bar {
    display: flex;
    gap: 10px;
    padding: 12px 16px;
    background-color: #fafafa;
    border-top: 1px solid #f0f0f0;
}

.action-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    padding: 8px 12px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.edit-btn {
    border: 1px solid;
}

.edit-btn:active {
    transform: scale(0.98);
    opacity: 0.8;
}

.preview-btn {
    background-color: #e8f4fd;
    color: #1890ff;
    border: 1px solid #d6e9f8;
}

.preview-btn:active {
    transform: scale(0.98);
    opacity: 0.8;
}

.delete-btn {
    background-color: #fff;
    color: #ff4757;
    border: 1px solid #ffeef0;
}

.delete-btn:hover {
    background-color: #fff5f5;
}

.delete-btn:active {
    transform: scale(0.98);
    opacity: 0.8;
}

.load-more {
    padding: 20px 0;
    text-align: center;
    font-size: 14px;
    color: #999;
    cursor: pointer;
}

.load-more:hover {
    color: #666;
}

.line-clamp-1 {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
