<template>
    <view
        class="wallet-card-widget"
        :style="{
            backgroundColor: styles.root_bg_color,
            marginTop: styles.margin_top + 'px',
            marginBottom: styles.margin_bottom + 'px',
            padding: '0 20rpx'
        }"
    >
        <view
            class="flex items-center justify-between py-4 px-3 bg-white"
            :style="{ borderRadius: styles.border_radius + 'px' }"
        >
            <view
                class="flex-1 text-center border-r border-gray-100"
                @click="handleLink(content.wallet_link)"
            >
                <view class="text-xl font-bold mb-1" :style="{ color: balanceColor }">{{
                    wallet.balance || '0.00'
                }}</view>
                <view class="text-xs" :style="{ color: styles.label_color }">我的钱包</view>
            </view>
            <view class="flex-1 text-center" @click="handleLink(content.commission_link)">
                <view class="text-xl font-bold mb-1" :style="{ color: balanceColor }">{{
                    wallet.commission || '0.00'
                }}</view>
                <view class="text-xs" :style="{ color: styles.label_color }">我的佣金</view>
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useUserStore } from '@/stores/user'
import { navigateTo } from '@/utils/util'
import { formatMoney } from '@/utils/money'

const props = defineProps({
    content: {
        type: Object,
        default: () => ({})
    },
    styles: {
        type: Object,
        default: () => ({})
    }
})

const userStore = useUserStore()
const loading = ref(false)

const wallet = computed(() => {
    return {
        balance: formatMoney(userStore.userInfo.user_money),
        commission: formatMoney(userStore.userInfo.earnings)
    }
})

const balanceColor = computed(() => {
    if (!props.styles.balance_color || props.styles.balance_color === '#333333') {
        return 'var(--color-primary)'
    }
    return props.styles.balance_color
})

const refreshWallet = async () => {
    if (loading.value) return
    loading.value = true
    try {
        await userStore.getUser()
    } catch (e) {
        console.error('刷新钱包数据失败:', e)
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    refreshWallet()
})

const handleLink = (link: any) => {
    if (link && link.path) {
        navigateTo(link)
    }
}
</script>

<style scoped>
.wallet-card-widget {
    overflow: hidden;
}
</style>
