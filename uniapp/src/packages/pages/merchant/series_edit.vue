<template>
    <uni-nav :title="isEdit ? '编辑系列' : '创建系列'"></uni-nav>

    <view class="series-edit min-h-screen bg-f5">
        <view class="content-area bg-white mx-3 mt-3 rounded-xl p-4 relative z-10 shadow-sm">
            <view class="border-b border-gray-100 py-3">
                <view class="text-gray-700 mb-2">系列名称 <text class="text-red-500">*</text></view>
                <input
                    class="w-full p-3 bg-gray-50 rounded-lg text-sm"
                    v-model="form.name"
                    placeholder="请输入系列名称"
                />
            </view>

            <view class="border-b border-gray-100 py-3">
                <view class="flex items-center justify-between">
                    <text class="text-gray-700">彩票类型 <text class="text-red-500">*</text></text>
                    <picker
                        mode="selector"
                        :range="lotteryTypeList"
                        range-key="label"
                        @change="onLotteryTypeChange"
                        class="flex-1 text-right"
                    >
                        <view class="flex justify-end items-center">
                            <text :class="form.lottery_type ? 'text-gray-800' : 'text-gray-400'">{{
                                lotteryTypeName || '请选择彩票类型'
                            }}</text>
                            <u-icon name="arrow-right" size="14" color="#999" class="ml-1"></u-icon>
                        </view>
                    </picker>
                </view>
            </view>

            <view class="border-b border-gray-100 py-3">
                <view class="flex items-center justify-between">
                    <text class="text-gray-700">系列价格 <text class="text-red-500">*</text></text>
                    <view class="flex items-center">
                        <input
                            class="w-24 text-right p-2 bg-gray-50 rounded-lg text-sm"
                            v-model="form.series_price"
                            type="digit"
                            placeholder="0"
                        />
                        <text class="text-gray-500 ml-2">元</text>
                    </view>
                </view>
                <view class="text-xs text-gray-400 mt-1">用户购买系列后可查看所有期次内容</view>
            </view>

            <view class="border-b border-gray-100 py-3">
                <view class="flex items-center justify-between">
                    <text class="text-gray-700">总期数</text>
                    <view class="flex items-center">
                        <input
                            class="w-24 text-right p-2 bg-gray-50 rounded-lg text-sm"
                            v-model="form.total_issues"
                            type="number"
                            placeholder="0"
                        />
                        <text class="text-gray-500 ml-2">期</text>
                    </view>
                </view>
                <view class="text-xs text-gray-400 mt-1">该系列计划发布的总期数</view>
            </view>

            <view class="border-b border-gray-100 py-3">
                <view class="flex items-center justify-between mb-2">
                    <text class="text-gray-700">系列介绍</text>
                </view>
                <textarea
                    class="w-full p-3 bg-gray-50 rounded-lg text-sm leading-relaxed"
                    v-model="form.series_desc"
                    placeholder="请输入系列介绍..."
                    style="height: 100px"
                ></textarea>
            </view>

            <view class="border-b border-gray-100 py-3">
                <view class="flex items-center justify-between">
                    <view class="text-gray-700">自动发布</view>
                    <switch
                        :checked="form.auto_publish == 1"
                        @change="onAutoPublishChange"
                        :color="themeStore.primaryColor"
                    />
                </view>
                <view class="text-xs text-gray-400 mt-1">开启后系统将按设定间隔自动发布期次</view>
            </view>

            <view class="border-b border-gray-100 py-3" v-if="form.auto_publish == 1">
                <view class="flex items-center justify-between">
                    <text class="text-gray-700">发布间隔</text>
                    <view class="flex items-center">
                        <input
                            class="w-24 text-right p-2 bg-gray-50 rounded-lg text-sm"
                            v-model="form.publish_interval"
                            type="number"
                            placeholder="0"
                        />
                        <text class="text-gray-500 ml-2">秒</text>
                    </view>
                </view>
                <view class="text-xs text-gray-400 mt-1">每期间隔多少秒自动发布</view>
            </view>

            <view class="border-b border-gray-100 py-3">
                <view class="flex items-center justify-between">
                    <text class="text-gray-700">系列状态</text>
                    <view class="flex items-center">
                        <text
                            class="text-sm mr-2"
                            :class="form.series_status == 1 ? 'text-green-500' : 'text-gray-400'"
                            >{{ form.series_status == 1 ? '进行中' : '已结束' }}</text
                        >
                        <switch
                            :checked="form.series_status == 1"
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

const lotteryTypeList = [
    { value: 'fc3d', label: '福彩3D' },
    { value: 'pl3', label: '排列三' },
    { value: 'ssq', label: '双色球' },
    { value: 'dlt', label: '大乐透' }
]

const form = reactive({
    id: '',
    name: '',
    lottery_type: '',
    series_price: '',
    total_issues: '',
    series_desc: '',
    auto_publish: 0,
    publish_interval: '',
    series_status: 1
})

const lotteryTypeName = computed(() => {
    const item = lotteryTypeList.find((t) => t.value === form.lottery_type)
    return item ? item.label : ''
})

const onLotteryTypeChange = (e: any) => {
    const index = e.detail.value
    form.lottery_type = lotteryTypeList[index]?.value || ''
}

const onAutoPublishChange = (e: any) => {
    form.auto_publish = e.detail.value ? 1 : 0
}

const onStatusChange = (e: any) => {
    form.series_status = e.detail.value ? 1 : 0
}

const goBack = () => {
    safeNavigateBack({ defaultUrl: '/pages/index/index' })
}

const handleSubmit = async () => {
    if (!form.name.trim()) {
        return uni.$u.toast('请输入系列名称')
    }
    if (!form.lottery_type) {
        return uni.$u.toast('请选择彩票类型')
    }
    if (!form.series_price || parseFloat(form.series_price) <= 0) {
        return uni.$u.toast('请输入系列价格')
    }

    submitting.value = true

    try {
        const submitData: any = {
            id: form.id || undefined,
            name: form.name,
            lottery_type: form.lottery_type,
            series_price: form.series_price,
            total_issues: form.total_issues || 0,
            series_desc: form.series_desc,
            auto_publish: form.auto_publish,
            publish_interval: form.publish_interval || 0,
            series_status: form.series_status
        }

        await request.post({
            url: '/merchant.series/save',
            data: submitData
        })

        uni.showToast({ title: isEdit.value ? '修改成功' : '创建成功', icon: 'success' })
        setTimeout(() => safeNavigateBack({ defaultUrl: '/pages/index/index' }), 1500)
    } catch (e: any) {
        uni.$u.toast(e?.msg || '操作失败，请重试')
    } finally {
        submitting.value = false
    }
}

const getDetail = async (id: string) => {
    try {
        const res = await request.get({ url: '/merchant.series/detail', data: { id } })
        if (res) {
            form.id = res.id
            form.name = res.name || ''
            form.lottery_type = res.lottery_type || ''
            form.series_price = res.series_price || ''
            form.total_issues = res.total_issues || ''
            form.series_desc = res.series_desc || ''
            form.auto_publish = res.auto_publish || 0
            form.publish_interval = res.publish_interval || ''
            form.series_status = res.series_status ?? 1
        }
    } catch (e) {}
}

onShow(() => {
    uni.setNavigationBarTitle({ title: isEdit.value ? '编辑系列' : '创建系列' })
    themeStore.getTheme()
})

onLoad((options: any) => {
    if (options.id) {
        isEdit.value = true
        getDetail(options.id)
    }
})
</script>

<style lang="scss" scoped>
.series-edit {
    background-color: #f5f5f5;
}
.bg-f5 {
    background-color: #f5f5f5;
}
</style>
