<template>
    <uni-nav title="系列管理">
        <template #right>
            <view class="w-10 h-full flex items-center justify-center" @click="toAdd">
                <u-icon name="plus" size="20"></u-icon>
            </view>
        </template>
    </uni-nav>

    <view class="series-list min-h-screen bg-f5">
        <view class="content-area mx-3 mt-3 relative z-10">
            <view v-if="list.length === 0 && !loading" class="bg-white rounded-xl p-8 text-center">
                <u-icon name="folder" size="48" color="#ccc"></u-icon>
                <view class="text-gray-400 mt-2">暂无系列</view>
                <view class="mt-4">
                    <u-button
                        size="small"
                        type="primary"
                        @click="toAdd"
                        :custom-style="{
                            backgroundColor: themeStore.primaryColor,
                            color: '#ffffff',
                            border: 'none'
                        }"
                        >创建系列</u-button
                    >
                </view>
            </view>

            <view v-else class="space-y-3">
                <view v-for="item in list" :key="item.id" class="bg-white rounded-xl p-4 shadow-sm">
                    <view class="flex justify-between items-start" @click="toDetail(item)">
                        <view class="flex-1">
                            <view class="flex items-center">
                                <text class="font-bold text-base">{{ item.name }}</text>
                                <view
                                    class="ml-2 px-2 py-0.5 text-xs rounded"
                                    :class="
                                        item.series_status == 1
                                            ? 'bg-green-100 text-green-600'
                                            : 'bg-gray-100 text-gray-500'
                                    "
                                >
                                    {{ item.series_status == 1 ? '进行中' : '已结束' }}
                                </view>
                            </view>
                            <view class="text-xs text-gray-400 mt-1">
                                {{ getLotteryTypeName(item.lottery_type) }}
                            </view>
                        </view>
                        <view class="text-right">
                            <view class="text-orange-500 font-bold">¥{{ item.series_price }}</view>
                            <view class="text-xs text-gray-400">系列价格</view>
                        </view>
                    </view>

                    <view
                        class="flex justify-between items-center mt-3 pt-3 border-t border-gray-100"
                    >
                        <view class="flex items-center text-xs text-gray-500">
                            <text
                                >已发布 {{ item.published_issues || 0 }}/{{
                                    item.total_issues || 0
                                }}
                                期</text
                            >
                        </view>
                        <view class="flex items-center space-x-2">
                            <view
                                class="px-3 py-1 rounded-full text-xs"
                                :style="{
                                    backgroundColor: themeStore.primaryColor + '20',
                                    color: themeStore.primaryColor
                                }"
                                @click.stop="toPublish(item)"
                            >
                                发布期次
                            </view>
                            <view
                                class="px-3 py-1 rounded-full text-xs bg-gray-100 text-gray-600"
                                @click.stop="toEdit(item)"
                            >
                                编辑
                            </view>
                            <view
                                class="px-3 py-1 rounded-full text-xs bg-red-50 text-red-500"
                                @click.stop="confirmDelete(item)"
                            >
                                删除
                            </view>
                        </view>
                    </view>
                </view>
            </view>

            <view v-if="hasMore" class="py-4 text-center text-sm text-gray-400" @click="loadMore">
                {{ loading ? '加载中...' : '加载更多' }}
            </view>

            <view v-if="!hasMore && list.length > 0" class="py-4 text-center text-sm text-gray-400">
                没有更多了
            </view>
        </view>
    </view>

    <u-modal
        :show="showDeleteModal"
        title="确认删除"
        content="确定要删除该系列吗？"
        @confirm="handleDelete"
        @cancel="showDeleteModal = false"
    ></u-modal>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue'
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
const showDeleteModal = ref(false)
const deleteItem = ref<any>(null)

const statistics = reactive({
    total: 0,
    active: 0
})

const lotteryTypes: Record<string, string> = {
    fc3d: '福彩3D',
    pl3: '排列三',
    ssq: '双色球',
    dlt: '大乐透'
}

const getLotteryTypeName = (type: string) => {
    return lotteryTypes[type] || type
}

const getList = async (refresh = false) => {
    if (loading.value) return
    if (refresh) {
        page.value = 1
        hasMore.value = true
    }

    loading.value = true
    try {
        const res = await request.get({
            url: '/merchant.series/lists',
            data: {
                page_no: page.value,
                page_size: 10
            }
        })

        if (refresh) {
            list.value = res.lists || []
        } else {
            list.value = [...list.value, ...(res.lists || [])]
        }

        statistics.total = res.count || list.value.length
        statistics.active = list.value.filter((item: any) => item.series_status == 1).length

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
    uni.navigateTo({ url: '/packages/pages/merchant/series_edit' })
}

const toEdit = (item: any) => {
    uni.navigateTo({ url: `/packages/pages/merchant/series_edit?id=${item.id}` })
}

const toDetail = (item: any) => {
    uni.navigateTo({ url: `/packages/pages/merchant/series_detail?id=${item.id}` })
}

const toPublish = (item: any) => {
    uni.navigateTo({ url: `/packages/pages/merchant/issue_edit?series_id=${item.id}` })
}

const confirmDelete = (item: any) => {
    deleteItem.value = item
    showDeleteModal.value = true
}

const handleDelete = async () => {
    if (!deleteItem.value) return

    try {
        await request.post({
            url: '/merchant.series/delete',
            data: { id: deleteItem.value.id }
        })
        uni.showToast({ title: '删除成功', icon: 'success' })
        getList(true)
    } catch (e: any) {
        uni.$u.toast(e?.msg || '删除失败')
    } finally {
        showDeleteModal.value = false
        deleteItem.value = null
    }
}

onShow(() => {
    uni.setNavigationBarTitle({ title: '系列管理' })
    themeStore.getTheme()
    getList(true)
})

onReachBottom(() => {
    loadMore()
})
</script>

<style lang="scss" scoped>
.series-list {
    background-color: #f5f5f5;
}
.bg-f5 {
    background-color: #f5f5f5;
}
</style>
