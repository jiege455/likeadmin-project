<template>
    <uni-nav :title="isEdit ? '编辑文章' : '发布文章'"></uni-nav>

    <view class="article-add min-h-screen bg-f5">
        <!-- 表单区域 -->
        <view class="content-area bg-white mx-3 mt-3 rounded-xl p-4 relative z-10 shadow-sm">
            <!-- 文章标题 -->
            <view class="border-b border-gray-100 py-3">
                <view class="text-gray-700 mb-2">文章标题 <text class="text-red-500">*</text></view>
                <textarea
                    class="w-full p-3 bg-gray-50 rounded-lg text-sm leading-relaxed"
                    v-model="form.title"
                    placeholder="请输入文章标题"
                    :auto-height="true"
                    style="min-height: 60px"
                ></textarea>
            </view>

            <!-- 是否系列文章 -->
            <view class="border-b border-gray-100 py-3">
                <view class="flex items-center justify-between">
                    <view class="text-gray-700">系列文章</view>
                    <switch
                        :checked="form.is_series == 1"
                        @change="onSeriesChange"
                        :color="themeStore.primaryColor"
                    />
                </view>
                <view class="text-xs text-gray-400 mt-1">开启后可选择所属系列和设置期次</view>
            </view>

            <!-- 选择系列 -->
            <view class="border-b border-gray-100 py-3" v-if="form.is_series == 1">
                <view class="flex items-center justify-between">
                    <text class="text-gray-700">所属系列 <text class="text-red-500">*</text></text>
                    <picker
                        mode="selector"
                        :range="seriesList"
                        range-key="name"
                        @change="onSeriesSelect"
                        class="flex-1 text-right"
                    >
                        <view class="flex justify-end items-center">
                            <text :class="form.series_id ? 'text-gray-800' : 'text-gray-400'">{{
                                seriesName || '请选择系列'
                            }}</text>
                            <u-icon name="arrow-right" size="14" color="#999" class="ml-1"></u-icon>
                        </view>
                    </picker>
                </view>
            </view>

            <!-- 期次号 -->
            <view class="border-b border-gray-100 py-3" v-if="form.is_series == 1">
                <view class="text-gray-700 mb-2">期次号 <text class="text-red-500">*</text></view>
                <input
                    class="w-full p-3 bg-gray-50 rounded-lg text-sm"
                    v-model="form.issue_no"
                    placeholder="请输入期次号，如2024001"
                />
                <view class="text-xs text-gray-400 mt-1">期次号用于区分同一系列的不同期</view>
            </view>

            <!-- 文章分类 -->
            <view class="border-b border-gray-100 py-3 flex items-center">
                <text class="text-gray-700 shrink-0">文章分类</text>
                <picker
                    mode="selector"
                    :range="categoryList"
                    range-key="name"
                    @change="onCategoryChange"
                    class="flex-1 text-right"
                >
                    <view class="flex justify-end items-center">
                        <text :class="form.cid ? 'text-gray-800' : 'text-gray-400'">{{
                            categoryName || '请选择分类'
                        }}</text>
                        <u-icon name="arrow-right" size="14" color="#999" class="ml-1"></u-icon>
                    </view>
                </picker>
            </view>

            <!-- 文章价格 -->
            <view class="border-b border-gray-100 py-3">
                <view class="flex items-center justify-between">
                    <text class="text-gray-700">文章价格</text>
                    <view class="flex items-center">
                        <input
                            class="w-24 text-right p-2 bg-gray-50 rounded-lg text-sm"
                            v-model="form.price"
                            type="digit"
                            placeholder="0"
                        />
                        <text class="text-gray-500 ml-2">元</text>
                    </view>
                </view>
                <view class="text-xs text-gray-400 mt-1">
                    {{ Number(form.price) > 0 ? '付费文章，用户购买后可查看完整内容' : '免费文章，所有用户可直接查看' }}
                </view>
            </view>

            <!-- 文章标签 -->
            <view class="border-b border-gray-100 py-3">
                <view class="flex items-center justify-between mb-2">
                    <text class="text-gray-700">文章标签</text>
                    <text class="text-xs text-gray-400">最多选择5个</text>
                </view>
                <view class="flex flex-wrap gap-2 mb-2">
                    <view
                        v-for="tag in selectedTags"
                        :key="tag.id"
                        class="flex items-center px-3 py-1.5 rounded-full text-sm"
                        :style="{
                            backgroundColor: themeStore.primaryColor + '20',
                            color: themeStore.primaryColor
                        }"
                    >
                        <text>{{ tag.name }}</text>
                        <u-icon
                            name="close"
                            size="12"
                            :color="themeStore.primaryColor"
                            class="ml-1"
                            @click="removeTag(tag)"
                        ></u-icon>
                    </view>
                    <view
                        v-if="selectedTags.length < 5"
                        class="px-3 py-1.5 rounded-full text-sm border border-dashed border-gray-300 text-gray-500"
                        @click="showTagPicker = true"
                    >
                        + 添加标签
                    </view>
                </view>
                <view class="text-xs text-gray-400">标签有助于文章分类和搜索</view>
            </view>

            <!-- 普通文章内容（非系列文章） -->
            <view class="py-3 border-b border-gray-100" v-if="form.is_series != 1">
                <view class="flex items-center justify-between mb-2">
                    <view class="flex items-center">
                        <text class="text-gray-700">文章内容</text>
                        <view 
                            class="ml-2 px-2 py-0.5 text-xs rounded"
                            :class="Number(form.price) > 0 ? 'bg-orange-100 text-orange-600' : 'bg-green-100 text-green-600'"
                        >
                            {{ Number(form.price) > 0 ? '付费' : '免费' }}
                        </view>
                    </view>
                    <text class="text-xs text-gray-400">
                        {{ Number(form.price) > 0 ? '购买后可见' : '所有用户可见' }}
                    </text>
                </view>
                <view class="editor-container">
                    <ArticleEditor v-model:content="form.content" />
                </view>
                <view class="flex justify-between mt-1">
                    <text class="text-xs text-gray-400">支持图文混排</text>
                    <view :class="Number(form.price) > 0 ? 'text-xs text-orange-500' : 'text-xs text-green-500'">
                        {{ Number(form.price) > 0 ? '🔒 付费后可见' : '✓ 所有用户可见' }}
                    </view>
                </view>
            </view>

            <!-- 系列文章：上一期内容（免费预览） -->
            <view class="py-3 border-b border-gray-100" v-if="form.is_series == 1">
                <view class="flex items-center justify-between mb-2">
                    <view class="flex items-center">
                        <text class="text-gray-700">上一期内容</text>
                        <view class="ml-2 px-2 py-0.5 bg-green-100 text-green-600 text-xs rounded"
                            >免费预览</view
                        >
                    </view>
                </view>
                <view class="mb-3 p-3 bg-blue-50 rounded-lg">
                    <view class="flex items-center mb-2">
                        <u-icon name="info-circle" color="#3b82f6" size="28"></u-icon>
                        <text class="ml-2 text-xs text-blue-600"
                            >输入上一期期次号后点击获取按钮，自动填充对应内容</text
                        >
                    </view>
                </view>
                <view class="mb-3 flex gap-2">
                    <view class="flex-1 flex items-center gap-2 bg-gray-50 rounded-lg p-2">
                        <text class="text-xs text-gray-500 shrink-0">上一期期次号：</text>
                        <input
                            class="flex-1 text-sm"
                            v-model="form.prev_issue_no"
                            placeholder="如2024001"
                        />
                    </view>
                    <button
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg text-sm font-medium flex items-center justify-center shrink-0"
                        @click="refreshLastIssue"
                        :disabled="!form.series_id"
                        :style="{ opacity: form.series_id ? 1 : 0.5 }"
                    >
                        <u-icon name="search" color="#fff" size="28"></u-icon>
                        <text class="ml-1">获取</text>
                    </button>
                </view>
                <view class="editor-container editor-green">
                    <ArticleEditor v-model:content="form.prev_issue_content" />
                </view>
                <view class="flex justify-between mt-1">
                    <text class="text-xs text-gray-400">支持图文混排</text>
                    <view class="text-xs text-green-500">✓ 免费可见，展示给用户参考</view>
                </view>
            </view>

            <!-- 系列文章：当前期内容（付费可见） -->
            <view class="py-3 border-b border-gray-100" v-if="form.is_series == 1">
                <view class="flex items-center justify-between mb-2">
                    <view class="flex items-center">
                        <text class="text-gray-700">当前期内容</text>
                        <view 
                            class="ml-2 px-2 py-0.5 text-xs rounded"
                            :class="Number(form.price) > 0 ? 'bg-orange-100 text-orange-600' : 'bg-green-100 text-green-600'"
                        >
                            {{ Number(form.price) > 0 ? '付费' : '免费' }}
                        </view>
                    </view>
                    <text class="text-xs text-gray-400">
                        {{ Number(form.price) > 0 ? '仅付费用户可见' : '所有用户可见' }}
                    </text>
                </view>
                <view class="editor-container">
                    <ArticleEditor v-model:content="form.hidden_content" />
                </view>
                <view class="flex justify-between mt-1">
                    <text class="text-xs text-gray-400">支持图文混排</text>
                    <view :class="Number(form.price) > 0 ? 'text-xs text-orange-500' : 'text-xs text-green-500'">
                        {{ Number(form.price) > 0 ? '🔒 付费后可见' : '✓ 所有用户可见' }}
                    </view>
                </view>
            </view>

            <!-- 文章状态 -->
            <view class="border-b border-gray-100 py-3">
                <view class="flex items-center justify-between">
                    <text class="text-gray-700">文章状态</text>
                    <view class="flex items-center">
                        <text
                            class="text-sm mr-2"
                            :class="form.is_show == 1 ? 'text-green-500' : 'text-gray-400'"
                            >{{ form.is_show == 1 ? '显示' : '隐藏' }}</text
                        >
                        <switch
                            :checked="form.is_show == 1"
                            @change="onStatusChange"
                            :color="themeStore.primaryColor"
                        />
                    </view>
                </view>
            </view>

            <!-- 提交按钮 -->
            <view class="mt-6 space-y-3">
                <u-button
                    type="primary"
                    shape="circle"
                    @click="handleSubmit"
                    :loading="submitting"
                    :disabled="submitting"
                    :custom-style="{
                        backgroundColor: themeStore.primaryColor,
                        color: '#ffffff',
                        border: 'none'
                    }"
                >
                    {{ submitting ? '发布中...' : isEdit ? '保存修改' : '发布文章' }}
                </u-button>
            </view>
        </view>

        <!-- 标签选择弹窗 -->
        <u-popup :show="showTagPicker" mode="bottom" round="16" @close="showTagPicker = false">
            <view class="tag-picker p-4">
                <view class="flex items-center justify-between mb-4">
                    <text class="font-bold text-base">选择标签</text>
                    <u-icon name="close" size="20" @click="showTagPicker = false"></u-icon>
                </view>

                <!-- 搜索/创建标签 -->
                <view class="flex items-center gap-2 mb-4">
                    <input
                        class="flex-1 p-2 bg-gray-50 rounded-lg text-sm"
                        v-model="tagSearchKeyword"
                        placeholder="搜索或创建新标签"
                        @input="filterTags"
                    />
                    <u-button
                        type="primary"
                        size="small"
                        @click="createNewTag"
                        :disabled="!tagSearchKeyword.trim()"
                        :custom-style="{ backgroundColor: themeStore.primaryColor, color: '#fff' }"
                    >
                        创建
                    </u-button>
                </view>

                <!-- 标签列表 -->
                <scroll-view scroll-y class="max-h-60">
                    <view class="flex flex-wrap gap-2">
                        <view
                            v-for="tag in filteredTagList"
                            :key="tag.id"
                            class="tag-item-container"
                        >
                            <view
                                class="px-3 py-1.5 rounded-full text-sm flex items-center"
                                :class="
                                    isTagSelected(tag.id) ? 'text-white' : 'bg-gray-100 text-gray-700'
                                "
                                :style="
                                    isTagSelected(tag.id)
                                        ? { backgroundColor: themeStore.primaryColor }
                                        : {}
                                "
                                @click="toggleTag(tag)"
                            >
                                {{ tag.name }}
                            </view>
                            <view class="tag-delete-btn" @click.stop="handleDeleteTag(tag)">
                                <u-icon name="close" size="12" color="#999"></u-icon>
                            </view>
                        </view>
                        <view
                            v-if="filteredTagList.length === 0"
                            class="text-gray-400 text-sm py-4 text-center w-full"
                        >
                            暂无标签，请创建新标签
                        </view>
                    </view>
                </scroll-view>

                <!-- 确认按钮 -->
                <view class="mt-4">
                    <u-button
                        type="primary"
                        @click="showTagPicker = false"
                        :custom-style="{ backgroundColor: themeStore.primaryColor, color: '#fff' }"
                    >
                        确定
                    </u-button>
                </view>
            </view>
        </u-popup>
    </view>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue'
import request from '@/utils/request'
import { onLoad, onShow } from '@dcloudio/uni-app'
import ArticleEditor from '@/components/business/ArticleEditor.vue'
import { useThemeStore } from '@/stores/theme'
import { useUserStore } from '@/stores/user'
import { getArticleTags, createArticleTag, deleteArticleTag } from '@/api/news'
import { safeNavigateBack } from '@/utils/util'

const themeStore = useThemeStore()
const userStore = useUserStore()
const themeStyle = computed(() => `--color-primary: ${themeStore.primaryColor};`)

const isEdit = ref(false)
const submitting = ref(false)
const categoryList = ref<{ id: number; name: string }[]>([])
const seriesList = ref<{ id: number; name: string; series_price: number }[]>([])

const showTagPicker = ref(false)
const tagList = ref<{ id: number; name: string }[]>([])
const filteredTagList = ref<{ id: number; name: string }[]>([])
const selectedTags = ref<{ id: number; name: string }[]>([])
const tagSearchKeyword = ref('')

const form = reactive({
    id: '',
    title: '',
    cid: '',
    is_series: 0,
    series_id: '',
    issue_no: '',
    price: '',
    content: '',
    hidden_content: '',
    prev_issue_content: '',
    prev_issue_no: '',
    is_show: 1,
    tag_ids: ''
})

const categoryName = computed(() => {
    const cat = categoryList.value.find((c) => c.id == form.cid)
    return cat ? cat.name : ''
})

const seriesName = computed(() => {
    const s = seriesList.value.find((s) => s.id == form.series_id)
    return s ? s.name : ''
})

const onSeriesChange = (e: any) => {
    form.is_series = e.detail.value ? 1 : 0
    if (form.is_series == 0) {
        form.series_id = ''
        form.issue_no = ''
        form.prev_issue_content = ''
        form.prev_issue_no = ''
        form.hidden_content = ''
    }
}

const onSeriesSelect = (e: any) => {
    const index = e.detail.value
    const newSeriesId = seriesList.value[index]?.id || ''

    if (form.series_id != newSeriesId) {
        form.prev_issue_content = ''
        form.prev_issue_no = ''
    }
    form.series_id = newSeriesId
}

const onCategoryChange = (e: any) => {
    const index = e.detail.value
    form.cid = categoryList.value[index]?.id || ''
}

const refreshLastIssue = async () => {
    if (!form.series_id) {
        uni.showToast({ title: '请先选择所属系列', icon: 'none' })
        return
    }

    uni.showLoading({ title: '获取中...' })
    try {
        const params: any = { series_id: form.series_id }
        if (form.prev_issue_no) {
            params.issue_no = form.prev_issue_no
        }

        const res = await request.get({
            url: '/merchant.series/lastIssue',
            data: params
        })
        if (res) {
            if (res.prev_issue_content) {
                if (!form.prev_issue_no) {
                    form.prev_issue_no = res.prev_issue_no || ''
                }
                form.prev_issue_content = res.prev_issue_content || ''
                uni.showToast({ title: '已填充内容', icon: 'success' })
            } else {
                uni.showToast({
                    title: form.prev_issue_no ? '未找到该期次' : '该系列暂无历史期次',
                    icon: 'none'
                })
            }
        }
    } catch (e) {
        uni.showToast({ title: '获取失败，请重试', icon: 'none' })
    } finally {
        uni.hideLoading()
    }
}

const onStatusChange = (e: any) => {
    form.is_show = e.detail.value ? 1 : 0
}

const getCategoryList = async () => {
    try {
        const res = await request.get({ url: '/article/cate' })
        categoryList.value = res.lists || res || []
    } catch (e) {
        categoryList.value = []
    }
}

const getSeriesList = async () => {
    try {
        const res = await request.get({ url: '/merchant.series/lists' })
        seriesList.value = res.lists || []
    } catch (e) {
        seriesList.value = []
    }
}

const getTagList = async () => {
    try {
        const res = await getArticleTags()
        tagList.value = res || []
        filteredTagList.value = tagList.value
    } catch (e) {
        tagList.value = []
        filteredTagList.value = []
    }
}

const filterTags = () => {
    const keyword = tagSearchKeyword.value.trim().toLowerCase()
    if (!keyword) {
        filteredTagList.value = tagList.value
    } else {
        filteredTagList.value = tagList.value.filter((tag) =>
            tag.name.toLowerCase().includes(keyword)
        )
    }
}

const isTagSelected = (tagId: number) => {
    return selectedTags.value.some((t) => t.id === tagId)
}

const toggleTag = (tag: { id: number; name: string }) => {
    const index = selectedTags.value.findIndex((t) => t.id === tag.id)
    if (index > -1) {
        selectedTags.value.splice(index, 1)
    } else if (selectedTags.value.length < 5) {
        selectedTags.value.push(tag)
    } else {
        uni.showToast({ title: '最多选择5个标签', icon: 'none' })
    }
}

const removeTag = (tag: { id: number; name: string }) => {
    const index = selectedTags.value.findIndex((t) => t.id === tag.id)
    if (index > -1) {
        selectedTags.value.splice(index, 1)
    }
}

const handleDeleteTag = async (tag: { id: number; name: string }) => {
    uni.showModal({
        title: '提示',
        content: `确定删除标签"${tag.name}"吗？`,
        success: async (res) => {
            if (res.confirm) {
                try {
                    await deleteArticleTag({ id: tag.id })
                    // 从列表中移除
                    const listIndex = tagList.value.findIndex((t) => t.id === tag.id)
                    if (listIndex > -1) {
                        tagList.value.splice(listIndex, 1)
                    }
                    const filteredIndex = filteredTagList.value.findIndex((t) => t.id === tag.id)
                    if (filteredIndex > -1) {
                        filteredTagList.value.splice(filteredIndex, 1)
                    }
                    // 从已选择中移除
                    const selectedIndex = selectedTags.value.findIndex((t) => t.id === tag.id)
                    if (selectedIndex > -1) {
                        selectedTags.value.splice(selectedIndex, 1)
                    }
                    uni.showToast({ title: '删除成功', icon: 'success' })
                } catch (e: any) {
                    uni.showToast({ title: e?.msg || '删除失败', icon: 'none' })
                }
            }
        }
    })
}

const createNewTag = async () => {
    const name = tagSearchKeyword.value.trim()
    if (!name) return

    if (name.length < 2 || name.length > 10) {
        return uni.showToast({ title: '标签名称需要2-10个字符', icon: 'none' })
    }

    const specialCharRegex = /[<>\"\'\\\/\[\]\{\}\(\)\;\:\!\@\#\$\%\^\&\*\+\=\|\~\`]/
    if (specialCharRegex.test(name)) {
        return uni.showToast({ title: '标签名称不能包含特殊字符', icon: 'none' })
    }

    if (!userStore.isLogin) {
        return uni.showToast({ title: '请先登录', icon: 'none' })
    }

    try {
        uni.showLoading({ title: '创建中...' })
        const res = await createArticleTag({ name })
        uni.hideLoading()
        if (res && res.id) {
            const existsInList = tagList.value.some((t) => t.id === res.id)
            if (!existsInList) {
                tagList.value.push(res)
            }
            filteredTagList.value = [...tagList.value]

            const existsInSelected = selectedTags.value.some((t) => t.id === res.id)
            if (!existsInSelected && selectedTags.value.length < 5) {
                selectedTags.value.push(res)
            }
            tagSearchKeyword.value = ''
            uni.showToast({ title: '创建成功', icon: 'success' })
        }
    } catch (e: any) {
        uni.hideLoading()
        const errorMsg = typeof e === 'string' ? e : e?.msg || '创建失败'
        uni.showToast({ title: errorMsg, icon: 'none' })
    }
}

const goBack = () => {
    safeNavigateBack({ defaultUrl: '/pages/index/index' })
}

const handleSubmit = async () => {
    if (!form.title.trim()) {
        return uni.$u.toast('请输入文章标题')
    }
    if (form.is_series == 1 && !form.series_id) {
        return uni.$u.toast('请选择所属系列')
    }
    if (form.is_series == 1 && !form.issue_no.trim()) {
        return uni.$u.toast('请输入期次号')
    }
    
    // 普通文章内容验证
    if (form.is_series != 1) {
        const isPaid = Number(form.price) > 0
        if (isPaid) {
            // 付费文章：必须有内容（会存到hidden_content）
            if (!form.content.trim()) {
                return uni.$u.toast('请输入文章内容')
            }
        } else {
            // 免费文章：必须有内容
            if (!form.content.trim()) {
                return uni.$u.toast('请输入文章内容')
            }
        }
    }
    
    // 系列文章当前期内容验证
    if (form.is_series == 1 && !form.hidden_content.trim()) {
        return uni.$u.toast('请输入当前期内容')
    }

    submitting.value = true

    try {
        const isPaid = Number(form.price) > 0
        const submitData: any = {
            id: form.id || undefined,
            title: form.title,
            cid: form.cid || 1,
            price: form.price || 0,
            content: form.is_series == 1 ? '' : (isPaid ? '' : form.content),
            hidden_content: form.is_series == 1 ? form.hidden_content : (isPaid ? form.content : ''),
            prev_issue_content: form.prev_issue_content,
            prev_issue_no: form.prev_issue_no,
            is_show: form.is_show,
            is_series: form.is_series,
            series_id: form.series_id || 0,
            issue_no: form.issue_no,
            tag_ids: selectedTags.value.map((t) => t.id).join(',')
        }

        await request.post({
            url: '/merchant.article/save',
            data: submitData
        })

        uni.showToast({ title: isEdit.value ? '修改成功' : '发布成功', icon: 'success' })
        setTimeout(() => {
            uni.redirectTo({ url: '/packages/pages/merchant/index' })
        }, 1500)
    } catch (e: any) {
        uni.$u.toast(e?.msg || '操作失败，请重试')
    } finally {
        submitting.value = false
    }
}

const getArticleDetail = async (id: string) => {
    try {
        const res = await request.get({ url: '/merchant.article/detail', data: { id } })
        if (res) {
            form.id = res.id
            form.title = res.title || ''
            form.cid = res.cid || ''
            form.is_series = res.is_series || 0
            form.series_id = res.series_id || ''
            form.issue_no = res.issue_no || ''
            form.price = res.price || ''
            form.content = res.content || ''
            form.hidden_content = res.hidden_content || ''
            form.prev_issue_content = res.prev_issue_content || ''
            form.prev_issue_no = res.prev_issue_no || ''
            form.is_show = res.is_show ?? 1
            if (res.tag_list && res.tag_list.length > 0) {
                selectedTags.value = res.tag_list
            }
        }
    } catch (e) {}
}

onShow(() => {
    uni.setNavigationBarTitle({ title: isEdit.value ? '编辑文章' : '发布文章' })
    themeStore.getTheme()
})

onLoad((options: any) => {
    getCategoryList()
    getSeriesList()
    getTagList()
    if (options.id) {
        isEdit.value = true
        getArticleDetail(options.id)
    }
})
</script>

<style lang="scss" scoped>
.article-add {
    background-color: #f5f5f5;
}
.bg-f5 {
    background-color: #f5f5f5;
}
.editor-container {
    background-color: #fff;
    border: 1px solid #e5e5e5;
    border-radius: 8rpx;
    overflow: hidden;
}
.editor-green {
    border-color: #22c55e;
}
.tag-picker {
    background-color: #fff;
}
.tag-item-container {
    position: relative;
    display: inline-flex;
    align-items: center;
}
.tag-delete-btn {
    position: absolute;
    top: -8rpx;
    right: -8rpx;
    width: 32rpx;
    height: 32rpx;
    border-radius: 50%;
    background-color: #f5f5f5;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
}
.tag-delete-btn:active {
    background-color: #e5e5e5;
}
</style>
