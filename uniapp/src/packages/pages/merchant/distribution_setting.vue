<template>
    <uni-nav title="分销设置" text-color="#ffffff"></uni-nav>

    <view class="distribution-setting min-h-screen bg-f5">
        <view class="bg-white m-3 rounded-xl p-4 shadow-sm">
            <view class="flex items-center justify-between py-2">
                <view>
                    <view class="font-medium">开启分销</view>
                    <view class="text-xs text-gray-400 mt-1"
                        >开启后推广员可推广您的文章赚取佣金</view
                    >
                </view>
                <u-switch
                    v-model="formData.distribution_switch"
                    :active-value="1"
                    :inactive-value="0"
                ></u-switch>
            </view>
        </view>

        <view
            class="bg-white mx-3 mt-3 rounded-xl p-4 shadow-sm"
            v-if="formData.distribution_switch"
        >
            <view class="mb-4">
                <view class="font-medium mb-2">默认分销比例</view>
                <view class="text-xs text-gray-400 mb-3">推广员推广成功后获得的佣金比例</view>
                <view class="flex items-center">
                    <u-input
                        v-model="formData.distribution_ratio"
                        type="number"
                        placeholder="请输入比例"
                        border="surround"
                        class="flex-1"
                    />
                    <text class="ml-2 text-lg">%</text>
                </view>
                <view class="text-xs text-gray-400 mt-2"
                    >比例范围：{{ minRatio }}% - {{ maxRatio }}%</view
                >
            </view>
        </view>

        <view class="bg-white mx-3 mt-3 rounded-xl p-4 shadow-sm">
            <view class="font-medium mb-2">分销说明</view>
            <view class="text-sm text-gray-500 leading-relaxed">
                <view
                    >1.
                    开启分销后，推广员分享您的文章链接，用户通过链接购买文章，推广员可获得佣金。</view
                >
                <view class="mt-2"
                    >2. 佣金比例越高，推广员推广积极性越高，但您的收益会相应减少。</view
                >
                <view class="mt-2"
                    >3. 建议设置10%-30%的比例，既能吸引推广员，又能保证您的收益。</view
                >
                <view class="mt-2">4. 您可以在发布文章时单独设置每篇文章的分销比例。</view>
            </view>
        </view>

        <view class="fixed bottom-0 left-0 right-0 p-4 bg-white">
            <u-button
                type="primary"
                shape="circle"
                @click="submit"
                :custom-style="{
                    backgroundColor: themeStore.primaryColor,
                    color: '#ffffff',
                    border: 'none'
                }"
                >保存设置</u-button
            >
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue'
import request from '@/utils/request'
import { onShow } from '@dcloudio/uni-app'
import { useThemeStore } from '@/stores/theme'
import { safeNavigateBack } from '@/utils/util'

const themeStore = useThemeStore()
const themeStyle = computed(() => `--color-primary: ${themeStore.primaryColor};`)

const formData = reactive({
    distribution_switch: 1,
    distribution_ratio: 10
})

const minRatio = ref(0)
const maxRatio = ref(50)

const getSetting = async () => {
    try {
        const res = await request.get({ url: '/merchant.distribution/getSetting' })
        formData.distribution_switch = res.distribution_switch
        formData.distribution_ratio = res.distribution_ratio
        minRatio.value = res.min_ratio ?? 0
        maxRatio.value = res.max_ratio ?? 50
    } catch (e) {}
}

const submit = async () => {
    if (
        formData.distribution_ratio < minRatio.value ||
        formData.distribution_ratio > maxRatio.value
    ) {
        return uni.$u.toast(`比例需在${minRatio.value}%-${maxRatio.value}%之间`)
    }

    try {
        await request.post({ url: '/merchant.distribution/setSetting', data: formData })
        uni.$u.toast('保存成功')
    } catch (e: any) {
        uni.$u.toast(e?.msg || '保存失败')
    }
}

const goBack = () => {
    safeNavigateBack({ defaultUrl: '/pages/index/index' })
}

onShow(() => {
    uni.setNavigationBarTitle({ title: '分销设置' })
    getSetting()
    themeStore.getTheme()
})
</script>

<style lang="scss" scoped>
.distribution-setting {
    background-color: #f5f5f5;
    padding-bottom: 80px;
}
.bg-f5 {
    background-color: #f5f5f5;
}
</style>
