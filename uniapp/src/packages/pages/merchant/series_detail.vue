<template>
    <uni-nav title="系列详情">
        <template #right>
            <view class="w-10 h-full flex items-center justify-center" @click="toEdit">
                <u-icon name="edit-pen" size="18"></u-icon>
            </view>
        </template>
    </uni-nav>

    <view class="series-detail min-h-screen bg-f5">
        <view class="content-area mx-3 mt-3 relative z-10">
            <view class="bg-white rounded-xl p-4 shadow-sm mb-3">
                <view class="flex justify-between items-center">
                    <view>
                        <view class="text-orange-500 font-bold text-xl"
                            >¥{{ seriesInfo.series_price }}</view
                        >
                        <view class="text-xs text-gray-400">系列价格</view>
                    </view>
                    <view class="text-right">
                        <view class="font-bold text-lg"
                            >{{ seriesInfo.published_issues || 0 }}/{{
                                seriesInfo.total_issues || 0
                            }}</view
                        >
                        <view class="text-xs text-gray-400">已发布期次</view>
                    </view>
                </view>
            </view>

            <view class="bg-white rounded-xl p-4 shadow-sm mb-3" v-if="seriesInfo.series_desc">
                <view class="text-gray-700 font-bold mb-2">系列介绍</view>
                <view class="text-sm text-gray-500 leading-relaxed">{{
                    seriesInfo.series_desc
                }}</view>
            </view>

            <view class="bg-white rounded-xl p-4 shadow-sm">
                <view class="flex justify-between items-center mb-3">
                    <view class="text-gray-700 font-bold">期次列表</view>
                    <view
                        class="px-3 py-1 rounded-full text-xs"
                        :style="{
                            backgroundColor: themeStore.primaryColor + '20',
                            color: themeStore.primaryColor
                        }"
                        @click="toAddIssue"
                    >
                        发布期次
                    </view>
                </view>

                <view v-if="issues.length === 0" class="py-8 text-center text-gray-400">
                    <u-icon name="file-text" size="40" color="#ccc"></u-icon>
                    <view class="mt-2">暂无期次</view>
                </view>

                <view v-else class="space-y-3">
                    <view
                        v-for="item in issues"
                        :key="item.id"
                        class="border-b border-gray-100 pb-3 last:border-0 last:pb-0"
                    >
                        <view class="flex justify-between items-start" @click="toEditIssue(item)">
                            <view class="flex-1">
                                <view class="flex items-center">
                                    <text class="font-bold">{{ item.issue_no }}期</text>
                                    <view
                                        class="ml-2 px-1.5 py-0.5 text-xs rounded"
                                        :class="getIssueStatusClass(item.issue_status)"
                                    >
                                        {{ getIssueStatusText(item.issue_status) }}
                                    </view>
                                    <view
                                        v-if="item.is_opened == 1"
                                        class="ml-1 px-1.5 py-0.5 text-xs rounded bg-red-50 text-red-500"
                                    >
                                        已开奖
                                    </view>
                                </view>
                                <view class="text-sm text-gray-500 mt-1 line-clamp-1">{{
                                    item.title
                                }}</view>
                            </view>
                            <view class="flex items-center space-x-2">
                                <view
                                    class="px-2 py-1 rounded text-xs bg-gray-100 text-gray-600"
                                    @click.stop="toEditIssue(item)"
                                >
                                    编辑
                                </view>
                                <view
                                    class="px-2 py-1 rounded text-xs bg-red-50 text-red-500"
                                    @click.stop="confirmDeleteIssue(item)"
                                >
                                    删除
                                </view>
                            </view>
                        </view>
                        <view
                            v-if="item.is_opened == 1 && item.open_code"
                            class="mt-2 text-xs text-gray-400"
                        >
                            开奖号码：{{ item.open_code }}
                        </view>
                    </view>
                </view>
            </view>
        </view>
    </view>

    <u-modal
        :show="showDeleteModal"
        title="确认删除"
        content="确定要删除该期次吗？"
        @confirm="handleDeleteIssue"
        @cancel="showDeleteModal = false"
    ></u-modal>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue'
import request from '@/utils/request'
import { onLoad, onShow } from '@dcloudio/uni-app'
import { useThemeStore } from '@/stores/theme'
import { safeNavigateBack } from '@/utils/util'

const themeStore = useThemeStore()
const themeStyle = computed(() => `--color-primary: ${themeStore.primaryColor};`)

const seriesId = ref('')
const seriesInfo = reactive<any>({
    id: 0,
    name: '',
    lottery_type: '',
    series_price: 0,
    total_issues: 0,
    published_issues: 0,
    series_desc: '',
    series_status: 1
})
const issues = ref<any[]>([])
const showDeleteModal = ref(false)
const deleteItem = ref<any>(null)

const lotteryTypes: Record<string, string> = {
    fc3d: '福彩3D',
    pl3: '排列三',
    ssq: '双色球',
    dlt: '大乐透'
}

const getLotteryTypeName = (type: string) => {
    return lotteryTypes[type] || type
}

const getIssueStatusText = (status: number) => {
    const statusMap: Record<number, string> = {
        0: '草稿',
        1: '已发布',
        2: '已开奖'
    }
    return statusMap[status] || '未知'
}

const getIssueStatusClass = (status: number) => {
    const classMap: Record<number, string> = {
        0: 'bg-gray-100 text-gray-500',
        1: 'bg-green-100 text-green-600',
        2: 'bg-blue-100 text-blue-600'
    }
    return classMap[status] || 'bg-gray-100 text-gray-500'
}

const getDetail = async () => {
    if (!seriesId.value) return

    try {
        const res = await request.get({
            url: '/merchant.series/detail',
            data: { id: seriesId.value }
        })

        if (res) {
            Object.assign(seriesInfo, res)
            issues.value = res.issues || []
        }
    } catch (e) {
        uni.$u.toast('获取详情失败')
    }
}

const goBack = () => {
    safeNavigateBack({ defaultUrl: '/pages/index/index' })
}

const toEdit = () => {
    uni.navigateTo({ url: `/packages/pages/merchant/series_edit?id=${seriesId.value}` })
}

const toAddIssue = () => {
    uni.navigateTo({ url: `/packages/pages/merchant/issue_edit?series_id=${seriesId.value}` })
}

const toEditIssue = (item: any) => {
    uni.navigateTo({
        url: `/packages/pages/merchant/issue_edit?id=${item.id}&series_id=${seriesId.value}`
    })
}

const confirmDeleteIssue = (item: any) => {
    deleteItem.value = item
    showDeleteModal.value = true
}

const handleDeleteIssue = async () => {
    if (!deleteItem.value) return

    try {
        await request.post({
            url: '/merchant.issue/delete',
            data: { id: deleteItem.value.id }
        })
        uni.showToast({ title: '删除成功', icon: 'success' })
        getDetail()
    } catch (e: any) {
        uni.$u.toast(e?.msg || '删除失败')
    } finally {
        showDeleteModal.value = false
        deleteItem.value = null
    }
}

onShow(() => {
    uni.setNavigationBarTitle({ title: '系列详情' })
    themeStore.getTheme()
    getDetail()
})

onLoad((options: any) => {
    if (options.id) {
        seriesId.value = options.id
    }
})
</script>

<style lang="scss" scoped>
.series-detail {
    background-color: #f5f5f5;
}
.bg-f5 {
    background-color: #f5f5f5;
}
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
