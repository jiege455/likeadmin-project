<template>
    <!--
    开发者公众号：杰哥网络科技
    QQ：2711793818 杰哥
    -->
    <view
        class="w-wallet-header"
        :style="{
            paddingTop: `${styles?.padding_top || 0}px`,
            paddingBottom: `${styles?.padding_bottom || 0}px`
        }"
    >
        <view
            class="header-card p-5 pb-8 relative"
            :style="{
                backgroundColor: content?.bg_color || 'var(--color-primary)',
                color: content?.text_color || '#ffffff'
            }"
        >
            <view class="text-center">
                <!-- <view class="text-lg mb-2">个人钱包</view> -->
                <view v-if="content?.show_balance" class="text-4xl font-bold"
                    >¥ {{ wallet.user_money || '0.00' }}</view
                >
                <navigator
                    v-if="content?.show_detail_btn"
                    url="/packages/pages/account_log/account_log"
                    hover-class="none"
                    class="inline-block mt-2 px-3 py-1 border border-white rounded text-xs"
                >
                    资金明细
                </navigator>
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { rechargeConfig } from '@/api/recharge'
import { onShow } from '@dcloudio/uni-app'

const wallet = ref<any>({})

const getWallet = async () => {
    try {
        const data = await rechargeConfig()
        wallet.value = data
    } catch (e) {
        console.error(e)
    }
}

// 监听刷新事件
uni.$on('refreshWallet', getWallet)

defineProps({
    content: {
        type: Object,
        default: () => ({})
    },
    styles: {
        type: Object,
        default: () => ({})
    }
})

onMounted(() => {
    getWallet()
})

onUnmounted(() => {
    uni.$off('refreshWallet', getWallet)
})
</script>

<style lang="scss" scoped></style>
