<template>
    <uni-nav :title="isEdit ? '编辑账户' : '添加账户'"></uni-nav>

    <view class="withdraw-account-add min-h-screen bg-f5">
        <view class="bg-white m-3 rounded-xl p-4 shadow-sm">
            <view class="text-base font-bold text-gray-800 mb-3">选择账户类型</view>
            <view class="flex gap-3" v-if="enabledMethods.length > 0">
                <view
                    v-for="method in enabledMethods"
                    :key="method.type"
                    class="flex-1 py-4 text-center rounded-xl border-2 cursor-pointer transition-all"
                    :class="
                        formData.type == method.type
                            ? getMethodStyle(method.type, true)
                            : 'border-gray-200 bg-gray-50'
                    "
                    @click="formData.type = method.type"
                >
                    <view
                        class="w-12 h-12 mx-auto rounded-full flex items-center justify-center mb-2"
                        :class="formData.type == method.type ? '' : 'bg-gray-100'"
                        :style="
                            formData.type == method.type
                                ? { backgroundColor: getMethodColor(method.type) + '20' }
                                : {}
                        "
                    >
                        <u-icon
                            :name="getMethodIcon(method.type)"
                            size="28"
                            :color="
                                formData.type == method.type ? getMethodColor(method.type) : '#999'
                            "
                        ></u-icon>
                    </view>
                    <view
                        class="font-medium text-sm"
                        :class="
                            formData.type == method.type
                                ? getMethodTextClass(method.type)
                                : 'text-gray-500'
                        "
                    >
                        {{ method.name }}
                    </view>
                </view>
            </view>
            <view v-else class="text-center py-4 text-gray-400">
                暂无可用的提现方式，请联系管理员
            </view>
        </view>

        <!-- 表单区域 -->
        <view class="bg-white mx-3 rounded-xl p-4 shadow-sm">
            <!-- 微信零钱 -->
            <template v-if="formData.type == 1">
                <view class="form-item">
                    <view class="form-label">
                        <text class="text-red-500">*</text>
                        微信账号
                    </view>
                    <input
                        v-model="formData.account"
                        placeholder="请输入微信账号"
                        class="form-input"
                        placeholder-class="text-gray-300"
                    />
                </view>
                <view class="form-item">
                    <view class="form-label">
                        <text class="text-red-500">*</text>
                        真实姓名
                    </view>
                    <input
                        v-model="formData.real_name"
                        placeholder="请输入真实姓名"
                        class="form-input"
                        placeholder-class="text-gray-300"
                    />
                </view>
                <view class="form-item">
                    <view class="form-label">微信收款码（选填）</view>
                    <view class="flex items-start gap-3 mt-2">
                        <view class="qrcode-upload" @click="chooseQrcode">
                            <image
                                v-if="formData.qrcode"
                                :src="formData.qrcode"
                                mode="aspectFill"
                                class="w-full h-full rounded-lg"
                            ></image>
                            <view v-else class="flex flex-col items-center justify-center h-full">
                                <u-icon name="plus" size="28" color="#ccc"></u-icon>
                                <text class="text-xs text-gray-400 mt-1">上传收款码</text>
                            </view>
                        </view>
                        <view class="text-xs text-gray-400 flex-1 pt-2">
                            <view>请上传微信收款码图片</view>
                            <view class="mt-1 text-gray-300">管理员可直接扫码打款</view>
                        </view>
                    </view>
                </view>
            </template>

            <!-- 支付宝 -->
            <template v-if="formData.type == 2">
                <view class="form-item">
                    <view class="form-label">
                        <text class="text-red-500">*</text>
                        支付宝账号
                    </view>
                    <input
                        v-model="formData.account"
                        placeholder="请输入支付宝账号"
                        class="form-input"
                        placeholder-class="text-gray-300"
                    />
                </view>
                <view class="form-item">
                    <view class="form-label">
                        <text class="text-red-500">*</text>
                        真实姓名
                    </view>
                    <input
                        v-model="formData.real_name"
                        placeholder="请输入真实姓名"
                        class="form-input"
                        placeholder-class="text-gray-300"
                    />
                </view>
                <view class="form-item">
                    <view class="form-label">支付宝收款码（选填）</view>
                    <view class="flex items-start gap-3 mt-2">
                        <view class="qrcode-upload" @click="chooseQrcode">
                            <image
                                v-if="formData.qrcode"
                                :src="formData.qrcode"
                                mode="aspectFill"
                                class="w-full h-full rounded-lg"
                            ></image>
                            <view v-else class="flex flex-col items-center justify-center h-full">
                                <u-icon name="plus" size="28" color="#ccc"></u-icon>
                                <text class="text-xs text-gray-400 mt-1">上传收款码</text>
                            </view>
                        </view>
                        <view class="text-xs text-gray-400 flex-1 pt-2">
                            <view>请上传支付宝收款码图片</view>
                            <view class="mt-1 text-gray-300">管理员可直接扫码打款</view>
                        </view>
                    </view>
                </view>
            </template>

            <!-- 银行卡 -->
            <template v-if="formData.type == 3">
                <view class="form-item">
                    <view class="form-label">
                        <text class="text-red-500">*</text>
                        银行名称
                    </view>
                    <input
                        v-model="formData.bank_name"
                        placeholder="请输入银行名称"
                        class="form-input"
                        placeholder-class="text-gray-300"
                    />
                </view>
                <view class="form-item">
                    <view class="form-label">
                        <text class="text-red-500">*</text>
                        银行卡号
                    </view>
                    <input
                        v-model="formData.account"
                        placeholder="请输入银行卡号"
                        class="form-input"
                        type="number"
                        placeholder-class="text-gray-300"
                    />
                </view>
                <view class="form-item">
                    <view class="form-label">开户支行（选填）</view>
                    <input
                        v-model="formData.bank_branch"
                        placeholder="请输入开户支行"
                        class="form-input"
                        placeholder-class="text-gray-300"
                    />
                </view>
                <view class="form-item">
                    <view class="form-label">
                        <text class="text-red-500">*</text>
                        持卡人姓名
                    </view>
                    <input
                        v-model="formData.real_name"
                        placeholder="请输入持卡人姓名"
                        class="form-input"
                        placeholder-class="text-gray-300"
                    />
                </view>
            </template>
        </view>

        <!-- 提交按钮 -->
        <view class="mx-3 mt-6" v-if="enabledMethods.length > 0">
            <view
                class="submit-btn py-4 rounded-xl text-center text-white font-bold text-base"
                :style="{ backgroundColor: themeStore.primaryColor }"
                @click="submit"
            >
                {{ isEdit ? '保存修改' : '添加账户' }}
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue'
import request from '@/utils/request'
import { onShow, onLoad } from '@dcloudio/uni-app'
import { useThemeStore } from '@/stores/theme'
import { uploadFile } from '@/utils/upload'
import { safeNavigateBack } from '@/utils/util'

const themeStore = useThemeStore()
const themeStyle = computed(() => `--color-primary: ${themeStore.primaryColor};`)

const isEdit = ref(false)
const merchantId = ref(0)
const enabledMethods = ref<Array<{ type: number; name: string }>>([])

const formData = reactive({
    id: '',
    type: 2,
    account: '',
    real_name: '',
    bank_name: '',
    bank_branch: '',
    qrcode: '',
    merchant_id: 0
})

const getMethodIcon = (type: number) => {
    const icons: Record<number, string> = {
        1: 'weixin-fill',
        2: 'zhifubao',
        3: 'integral-fill'
    }
    return icons[type] || 'account'
}

const getMethodColor = (type: number) => {
    const colors: Record<number, string> = {
        1: '#07C160',
        2: '#1677FF',
        3: '#FF8C00'
    }
    return colors[type] || '#333'
}

const getMethodStyle = (type: number, selected: boolean) => {
    const styles: Record<number, string> = {
        1: 'border-green-500 bg-green-50',
        2: 'border-blue-500 bg-blue-50',
        3: 'border-orange-500 bg-orange-50'
    }
    return selected ? styles[type] || 'border-blue-500 bg-blue-50' : 'border-gray-200 bg-gray-50'
}

const getMethodTextClass = (type: number) => {
    const classes: Record<number, string> = {
        1: 'text-green-600',
        2: 'text-blue-600',
        3: 'text-orange-600'
    }
    return classes[type] || 'text-blue-600'
}

const chooseQrcode = () => {
    uni.chooseImage({
        count: 1,
        sizeType: ['compressed'],
        sourceType: ['album', 'camera'],
        success: async (res) => {
            const tempFilePath = res.tempFilePaths[0]
            uni.showLoading({ title: '上传中...' })
            try {
                const uploadRes = await uploadFile(tempFilePath)
                formData.qrcode = uploadRes.uri || uploadRes.url || uploadRes
                uni.hideLoading()
            } catch (e) {
                uni.hideLoading()
                uni.$u.toast('上传失败')
            }
        }
    })
}

const getEnabledMethods = async () => {
    try {
        const res = await request.get({ url: '/withdraw.account/methods' })
        enabledMethods.value = res.methods || []

        if (enabledMethods.value.length > 0) {
            const hasCurrentType = enabledMethods.value.some((m) => m.type === formData.type)
            if (!hasCurrentType) {
                formData.type = enabledMethods.value[0].type
            }
        }
    } catch (e) {
        enabledMethods.value = [
            { type: 1, name: '微信' },
            { type: 2, name: '支付宝' },
            { type: 3, name: '银行卡' }
        ]
    }
}

const getDetail = async (id: string) => {
    try {
        const res = await request.get({
            url: '/withdraw.account/detail',
            data: { id, merchant_id: merchantId.value }
        })
        if (res) {
            formData.id = res.id
            formData.type = res.type
            formData.account = res.account
            formData.real_name = res.real_name
            formData.bank_name = res.bank_name || ''
            formData.bank_branch = res.bank_branch || ''
            formData.qrcode = res.qrcode || ''
        }
    } catch (e) {}
}

const submit = async () => {
    if (enabledMethods.value.length === 0) {
        return uni.$u.toast('暂无可用的提现方式')
    }
    if (!formData.account) return uni.$u.toast('请输入账号')
    if (!formData.real_name) return uni.$u.toast('请输入真实姓名')
    if (formData.type == 3 && !formData.bank_name) return uni.$u.toast('请输入银行名称')

    formData.merchant_id = merchantId.value

    try {
        const url = isEdit.value ? '/withdraw.account/edit' : '/withdraw.account/add'
        await request.post({ url, data: formData })
        uni.$u.toast(isEdit.value ? '修改成功' : '添加成功')
        setTimeout(() => safeNavigateBack({ defaultUrl: '/pages/index/index' }), 1500)
    } catch (e: any) {
        uni.$u.toast(e?.msg || '操作失败')
    }
}

const goBack = () => {
    safeNavigateBack({ defaultUrl: '/pages/index/index' })
}

onLoad((options: any) => {
    if (options.merchant_id) {
        merchantId.value = parseInt(options.merchant_id)
    }
    if (options.id) {
        isEdit.value = true
        getDetail(options.id)
    }
})

onShow(() => {
    uni.setNavigationBarTitle({ title: isEdit.value ? '编辑账户' : '添加账户' })
    themeStore.getTheme()
    getEnabledMethods()
})
</script>

<style lang="scss" scoped>
.withdraw-account-add {
    background-color: #f5f5f5;
    padding-bottom: 40px;
}
.bg-f5 {
    background-color: #f5f5f5;
}
.form-item {
    padding: 16rpx 0;
    border-bottom: 1rpx solid #f0f0f0;
    &:last-child {
        border-bottom: none;
    }
}
.form-label {
    font-size: 28rpx;
    color: #333;
    margin-bottom: 16rpx;
    font-weight: 500;
}
.form-input {
    width: 100%;
    height: 80rpx;
    background: #f8f8f8;
    border-radius: 12rpx;
    padding: 0 24rpx;
    font-size: 28rpx;
}
.qrcode-upload {
    width: 160rpx;
    height: 160rpx;
    border: 2rpx dashed #ddd;
    border-radius: 16rpx;
    overflow: hidden;
    background-color: #fafafa;
}
.submit-btn {
    box-shadow: 0 4rpx 12rpx rgba(0, 0, 0, 0.1);
}
</style>
