<template>
    <uni-nav :title="isEdit ? '编辑期次' : '发布期次'"></uni-nav>

    <view class="issue-edit min-h-screen bg-f5">
        <view class="content-area bg-white mx-3 mt-3 rounded-xl p-4 relative z-10 shadow-sm">
            <view class="border-b border-gray-100 py-3">
                <view class="text-gray-700 mb-2">期号 <text class="text-red-500">*</text></view>
                <input
                    class="w-full p-3 bg-gray-50 rounded-lg text-sm"
                    v-model="form.issue_no"
                    placeholder="请输入期号，如：2024001"
                    maxlength="20"
                />
            </view>

            <view class="border-b border-gray-100 py-3">
                <view class="text-gray-700 mb-2">标题 <text class="text-red-500">*</text></view>
                <input
                    class="w-full p-3 bg-gray-50 rounded-lg text-sm"
                    v-model="form.title"
                    placeholder="请输入标题"
                    maxlength="255"
                />
            </view>

            <view class="border-b border-gray-100 py-3">
                <view class="flex items-center justify-between mb-2">
                    <text class="text-gray-700">内容 <text class="text-red-500">*</text></text>
                </view>
                <textarea
                    class="w-full p-3 bg-gray-50 rounded-lg text-sm leading-relaxed"
                    v-model="form.content"
                    placeholder="请输入期次内容..."
                    style="height: 200px"
                ></textarea>
            </view>

            <view class="border-b border-gray-100 py-3">
                <view class="flex items-center justify-between">
                    <text class="text-gray-700">是否开奖</text>
                    <switch
                        :checked="form.is_opened == 1"
                        @change="onIsOpenedChange"
                        :color="themeStore.primaryColor"
                    />
                </view>
            </view>

            <view class="border-b border-gray-100 py-3" v-if="form.is_opened == 1">
                <view class="text-gray-700 mb-2">开奖号码</view>
                <input
                    class="w-full p-3 bg-gray-50 rounded-lg text-sm"
                    v-model="form.open_code"
                    placeholder="请输入开奖号码"
                />
            </view>

            <view class="border-b border-gray-100 py-3">
                <view class="flex items-center justify-between">
                    <text class="text-gray-700">发布状态</text>
                    <view class="flex items-center">
                        <text
                            class="text-sm mr-2"
                            :class="form.issue_status > 0 ? 'text-green-500' : 'text-gray-400'"
                            >{{ form.issue_status > 0 ? '已发布' : '草稿' }}</text
                        >
                        <switch
                            :checked="form.issue_status > 0"
                            @change="onStatusChange"
                            :color="themeStore.primaryColor"
                        />
                    </view>
                </view>
            </view>

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
                    {{ submitting ? '保存中...' : '保存' }}
                </u-button>
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue'
import request from '@/utils/request'
import { onLoad, onShow } from '@dcloudio/uni-app'
import { useThemeStore } from '@/stores/theme'
import { safeNavigateBack } from '@/utils/util'

const themeStore = useThemeStore()
const themeStyle = computed(() => `--color-primary: ${themeStore.primaryColor};`)

const isEdit = ref(false)
const submitting = ref(false)
const seriesId = ref('')

const form = reactive({
    id: '',
    series_id: '',
    issue_no: '',
    title: '',
    content: '',
    open_code: '',
    is_opened: 0,
    issue_status: 0
})

const onIsOpenedChange = (e: any) => {
    form.is_opened = e.detail.value ? 1 : 0
}

const onStatusChange = (e: any) => {
    form.issue_status = e.detail.value ? 1 : 0
}

const goBack = () => {
    safeNavigateBack({ defaultUrl: '/pages/index/index' })
}

const handleSubmit = async () => {
    if (!form.issue_no.trim()) {
        return uni.$u.toast('请输入期号')
    }
    if (!form.title.trim()) {
        return uni.$u.toast('请输入标题')
    }
    if (!form.content.trim()) {
        return uni.$u.toast('请输入内容')
    }

    submitting.value = true

    try {
        const submitData: any = {
            id: form.id || undefined,
            series_id: form.series_id,
            issue_no: form.issue_no,
            title: form.title,
            content: form.content,
            open_code: form.open_code,
            is_opened: form.is_opened,
            issue_status: form.issue_status
        }

        await request.post({
            url: '/merchant.issue/save',
            data: submitData
        })

        uni.showToast({ title: isEdit.value ? '修改成功' : '发布成功', icon: 'success' })
        setTimeout(() => safeNavigateBack({ defaultUrl: '/pages/index/index' }), 1500)
    } catch (e: any) {
        uni.$u.toast(e?.msg || '操作失败，请重试')
    } finally {
        submitting.value = false
    }
}

const getDetail = async (id: string) => {
    try {
        const res = await request.get({ url: '/merchant.issue/detail', data: { id } })
        if (res) {
            form.id = res.id
            form.series_id = res.series_id || ''
            form.issue_no = res.issue_no || ''
            form.title = res.title || ''
            form.content = res.content || ''
            form.open_code = res.open_code || ''
            form.is_opened = res.is_opened || 0
            form.issue_status = res.issue_status ?? 0
        }
    } catch (e) {}
}

onShow(() => {
    uni.setNavigationBarTitle({ title: isEdit.value ? '编辑期次' : '发布期次' })
    themeStore.getTheme()
})

onLoad((options: any) => {
    if (options.id) {
        isEdit.value = true
        getDetail(options.id)
    }
    if (options.series_id) {
        seriesId.value = options.series_id
        form.series_id = options.series_id
    }
})
</script>

<style lang="scss" scoped>
.issue-edit {
    background-color: #f5f5f5;
}
.bg-f5 {
    background-color: #f5f5f5;
}
</style>
