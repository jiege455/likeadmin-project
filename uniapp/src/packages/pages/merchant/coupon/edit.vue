<template>
    <uni-nav :title="isEdit ? '编辑优惠券' : '发布优惠券'"></uni-nav>

    <view class="merchant-coupon-edit min-h-screen bg-f5">
        <!-- 表单区域 -->
        <view class="content-area bg-white mx-3 mt-3 rounded-xl p-4 relative z-10 shadow-sm">
            <view class="border-b border-gray-100 py-3 flex items-center">
                <text class="w-24 text-gray-700">优惠券名称</text>
                <input class="flex-1" v-model="form.name" placeholder="请输入名称" />
            </view>
            <view class="border-b border-gray-100 py-3 flex items-center">
                <text class="w-24 text-gray-700">优惠金额</text>
                <input class="flex-1" v-model="form.money" type="digit" placeholder="请输入" />
                <text class="text-gray-500">元</text>
            </view>
            <view class="border-b border-gray-100 py-3 flex items-center">
                <text class="w-24 text-gray-700">使用门槛</text>
                <input
                    class="flex-1"
                    v-model="form.condition_money"
                    type="digit"
                    placeholder="0表示无门槛"
                />
                <text class="text-gray-500">元</text>
            </view>
            <view class="border-b border-gray-100 py-3 flex items-center">
                <text class="w-24 text-gray-700">发放总数</text>
                <input
                    class="flex-1"
                    v-model="form.total_count"
                    type="number"
                    placeholder="请输入"
                />
                <text class="text-gray-500">张</text>
            </view>
            <view class="border-b border-gray-100 py-3 flex items-center">
                <text class="w-24 text-gray-700">有效期类型</text>
                <picker
                    mode="selector"
                    :range="timeTypeList"
                    range-key="label"
                    @change="onTimeTypeChange"
                    class="flex-1"
                >
                    <view class="flex justify-between items-center">
                        <text>{{ timeTypeList[form.use_time_type - 1]?.label }}</text>
                        <u-icon name="arrow-right" color="#999"></u-icon>
                    </view>
                </picker>
            </view>
            <view
                class="border-b border-gray-100 py-3 flex items-center"
                v-if="form.use_time_type == 1"
            >
                <text class="w-24 text-gray-700">开始时间</text>
                <picker
                    mode="date"
                    :value="form.use_time_start"
                    @change="startChange"
                    class="flex-1"
                >
                    <view class="flex justify-between items-center">
                        <view :class="form.use_time_start ? 'text-gray-800' : 'text-gray-400'">{{
                            form.use_time_start || '请选择'
                        }}</view>
                        <u-icon name="calendar" color="#999"></u-icon>
                    </view>
                </picker>
            </view>
            <view
                class="border-b border-gray-100 py-3 flex items-center"
                v-if="form.use_time_type == 1"
            >
                <text class="w-24 text-gray-700">结束时间</text>
                <picker mode="date" :value="form.use_time_end" @change="endChange" class="flex-1">
                    <view class="flex justify-between items-center">
                        <view :class="form.use_time_end ? 'text-gray-800' : 'text-gray-400'">{{
                            form.use_time_end || '请选择'
                        }}</view>
                        <u-icon name="calendar" color="#999"></u-icon>
                    </view>
                </picker>
            </view>
            <view
                class="border-b border-gray-100 py-3 flex items-center"
                v-if="form.use_time_type == 2"
            >
                <text class="w-24 text-gray-700">有效天数</text>
                <input
                    class="flex-1"
                    v-model="form.use_days"
                    type="number"
                    placeholder="领取后多少天有效"
                />
                <text class="text-gray-500">天</text>
            </view>

            <view class="mt-6">
                <u-button
                    type="primary"
                    shape="circle"
                    @click="handleSubmit"
                    :loading="submitting"
                    :custom-style="{
                        backgroundColor: themeStore.primaryColor,
                        color: '#ffffff',
                        border: 'none'
                    }"
                    >保存</u-button
                >
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

const timeTypeList = [
    { value: 1, label: '固定时间段' },
    { value: 2, label: '领取后生效' }
]

const form = reactive({
    id: '',
    name: '',
    money: '',
    condition_money: '0',
    total_count: '',
    use_time_type: 1,
    use_time_start: '',
    use_time_end: '',
    use_days: '7'
})

const onTimeTypeChange = (e: any) => {
    form.use_time_type = timeTypeList[e.detail.value]?.value || 1
}

const startChange = (e: any) => {
    form.use_time_start = e.detail.value
}

const endChange = (e: any) => {
    form.use_time_end = e.detail.value
}

const goBack = () => {
    safeNavigateBack({ defaultUrl: '/pages/index/index' })
}

const handleSubmit = async () => {
    if (!form.name) return uni.$u.toast('请输入名称')
    if (!form.money || parseFloat(form.money) <= 0) return uni.$u.toast('请输入有效的优惠金额')
    if (!form.total_count || parseInt(form.total_count) <= 0) return uni.$u.toast('请输入发放数量')

    if (form.use_time_type == 1) {
        if (!form.use_time_start || !form.use_time_end) {
            return uni.$u.toast('请选择有效期时间')
        }
    } else {
        if (!form.use_days || parseInt(form.use_days) <= 0) {
            return uni.$u.toast('请输入有效天数')
        }
    }

    submitting.value = true
    try {
        await request.post({ url: '/merchant.coupon/save', data: form })
        uni.showToast({ title: '保存成功' })
        setTimeout(() => uni.navigateBack(), 1500)
    } catch (e: any) {
        uni.$u.toast(e?.msg || '保存失败')
    } finally {
        submitting.value = false
    }
}

onShow(() => {
    uni.setNavigationBarTitle({ title: isEdit.value ? '编辑优惠券' : '发布优惠券' })
    themeStore.getTheme()
})

onLoad((options: any) => {
    if (options.id) {
        isEdit.value = true
        form.id = options.id
    }
})
</script>

<style lang="scss" scoped>
.merchant-coupon-edit {
    background-color: #f5f5f5;
}
.bg-f5 {
    background-color: #f5f5f5;
}
</style>
