<template>
    <view class="user-realname">
        <uni-nav title="实名认证"></uni-nav>
        <view class="p-4" v-if="!loading">
            <template v-if="authStatus === 1">
                <view class="bg-white p-6 rounded-lg text-center">
                    <u-icon name="checkmark-circle-fill" color="#52C41A" size="80"></u-icon>
                    <view class="text-lg font-bold mt-4">已通过实名认证</view>
                    <view class="text-gray-500 mt-2">{{ realName }} | {{ idCard }}</view>
                </view>
            </template>
            <template v-else-if="authStatus === 0 && hasSubmitted">
                <view class="bg-white p-6 rounded-lg text-center">
                    <u-icon name="clock-fill" color="#FAAD14" size="80"></u-icon>
                    <view class="text-lg font-bold mt-4">认证审核中</view>
                    <view class="text-gray-500 mt-2">您的实名认证正在审核，请耐心等待</view>
                </view>
            </template>
            <template v-else>
                <view class="bg-white rounded-lg p-4">
                    <view class="mb-2 text-gray-500 text-xs" v-if="authStatus === 2">
                        <text class="text-red-500">审核失败：{{ failReason }}</text>
                    </view>
                    <u-form :model="formData" ref="uForm" label-width="160rpx">
                        <u-form-item label="真实姓名" prop="real_name" required>
                            <u-input v-model="formData.real_name" placeholder="请输入真实姓名" />
                        </u-form-item>
                        <u-form-item label="身份证号" prop="id_card" required>
                            <u-input v-model="formData.id_card" placeholder="请输入身份证号" />
                        </u-form-item>
                        <u-form-item label="手机号码" prop="mobile">
                            <u-input v-model="formData.mobile" placeholder="请输入手机号码(选填)" />
                        </u-form-item>
                    </u-form>
                </view>
                <view class="mt-8">
                    <u-button type="primary" shape="circle" @click="submit" :loading="submitting"
                        >提交认证</u-button
                    >
                </view>
            </template>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { getRealnameInfo, submitRealname } from '@/api/user'

const loading = ref(true)
const submitting = ref(false)
const authStatus = ref<number | null>(null) // null: 未提交, 0: 待审核, 1: 通过, 2: 拒绝
const hasSubmitted = ref(false)
const failReason = ref('')
const realName = ref('')
const idCard = ref('')

const formData = reactive({
    real_name: '',
    id_card: '',
    mobile: ''
})

const getInfo = async () => {
    loading.value = true
    try {
        const res = await getRealnameInfo()
        if (res.user_realname) {
            authStatus.value = res.user_realname.status
            hasSubmitted.value = true
            realName.value = res.user_realname.real_name
            idCard.value = res.user_realname.id_card
            failReason.value = res.user_realname.fail_reason

            // 如果被拒绝，回填信息方便修改
            if (authStatus.value === 2) {
                formData.real_name = res.user_realname.real_name
                // 身份证号带星号，清空让用户重输，或者保留看需求。这里清空比较安全。
                formData.id_card = ''
            }
        } else {
            hasSubmitted.value = false
        }
    } catch (e) {
        console.error(e)
    } finally {
        loading.value = false
    }
}

const submit = async () => {
    if (!formData.real_name) return uni.showToast({ title: '请输入真实姓名', icon: 'none' })
    if (!formData.id_card) return uni.showToast({ title: '请输入身份证号', icon: 'none' })

    submitting.value = true
    try {
        await submitRealname(formData)
        uni.showToast({ title: '提交成功' })
        getInfo()
    } catch (e) {
        // error
    } finally {
        submitting.value = false
    }
}

onMounted(() => {
    getInfo()
})
</script>

<style lang="scss" scoped>
.user-realname {
    min-height: 100vh;
    background-color: #f5f5f5;
}
</style>
