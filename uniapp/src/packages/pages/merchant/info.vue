<template>
    <uni-nav title="店铺设置"></uni-nav>

    <view class="merchant-info min-h-screen bg-f5">
        <!-- 表单区域 -->
        <view class="content-area bg-white mx-3 mt-3 rounded-xl p-4 relative z-10 shadow-sm">
            <view class="border-b border-gray-100 py-3 flex items-center">
                <text class="w-24 text-gray-700">店铺名称</text>
                <input class="flex-1" v-model="formData.name" placeholder="请输入店铺名称" />
            </view>
            <view class="border-b border-gray-100 py-3 flex items-center">
                <text class="w-24 text-gray-700">联系电话</text>
                <input class="flex-1" v-model="formData.mobile" placeholder="请输入联系电话" />
            </view>
            <view class="border-b border-gray-100 py-3 flex items-center">
                <text class="w-24 text-gray-700">微信号</text>
                <input class="flex-1" v-model="formData.wechat" placeholder="请输入微信号" />
            </view>
            <view class="py-3">
                <text class="w-24 text-gray-700 block mb-2">店铺简介</text>
                <textarea
                    class="w-full h-24 p-2 bg-gray-50 rounded-lg text-sm"
                    v-model="formData.intro"
                    placeholder="请输入店铺简介"
                ></textarea>
            </view>

            <view class="mt-6">
                <u-button
                    type="primary"
                    shape="circle"
                    @click="submit"
                    :loading="loading"
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
import { onShow } from '@dcloudio/uni-app'
import { uploadFile } from '@/utils/upload'
import { useThemeStore } from '@/stores/theme'
import { safeNavigateBack } from '@/utils/util'

const themeStore = useThemeStore()
const themeStyle = computed(() => {
    return `
        --color-primary: ${themeStore.primaryColor};
    `
})

const loading = ref(false)

const formData = reactive({
    name: '',
    mobile: '',
    wechat: '',
    intro: '',
    logo: ''
})

const chooseLogo = async () => {
    uni.chooseImage({
        count: 1,
        success: async (res) => {
            const tempFilePaths = res.tempFilePaths
            try {
                uni.showLoading({ title: '上传中...' })
                const uploadRes = await uploadFile(tempFilePaths[0])
                uni.hideLoading()
                formData.logo = uploadRes.uri || uploadRes.url || uploadRes
            } catch (e) {
                uni.hideLoading()
                uni.$u.toast('图片上传失败')
            }
        }
    })
}

const getInfo = async () => {
    try {
        const res = await request.get({ url: '/merchant.info/get' })
        if (res) {
            formData.name = res.name || ''
            formData.mobile = res.mobile || ''
            formData.wechat = res.wechat || ''
            formData.intro = res.intro || ''
            formData.logo = res.logo || ''
        }
    } catch (e) {
        uni.$u.toast('获取店铺信息失败')
    }
}

const goBack = () => {
    safeNavigateBack({ defaultUrl: '/pages/index/index' })
}

const submit = async () => {
    loading.value = true
    try {
        await request.post({ url: '/merchant.info/set', data: formData })
        uni.showToast({ title: '保存成功' })
    } catch (e) {
    } finally {
        loading.value = false
    }
}

onShow(() => {
    uni.setNavigationBarTitle({ title: '店铺设置' })
    getInfo()
    themeStore.getTheme()
})
</script>

<style lang="scss" scoped>
.merchant-info {
    background-color: #f5f5f5;
}
.bg-f5 {
    background-color: #f5f5f5;
}
</style>
