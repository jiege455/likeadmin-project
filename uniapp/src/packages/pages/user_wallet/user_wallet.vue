<template>
    <!--
    开发者公众号：杰哥网络科技
    QQ：2711793818 杰哥
    -->
    <view class="user-wallet min-h-screen bg-f5" :style="themeStyle">
        <uni-nav
            title="个人钱包"
            :bg-color="themeStore.primaryColor"
            text-color="#ffffff"
        ></uni-nav>

        <!-- 固定布局渲染 -->
        <!-- <w-custom-navbar
            :content="{ title: '个人钱包' }"
            :styles="{}"
        /> -->
        <w-wallet-header
            :content="{
                bg_color: themeStore.primaryColor,
                text_color: '#ffffff',
                show_balance: 1,
                show_detail_btn: 1
            }"
            :styles="{}"
        />
        <w-recharge-panel
            :content="{
                amounts: [
                    { value: 30, text: '30元' },
                    { value: 50, text: '50元' },
                    { value: 100, text: '100元' },
                    { value: 200, text: '200元' },
                    { value: 300, text: '300元' },
                    { value: 500, text: '500元' }
                ],
                show_custom_amount: 1,
                btn_color: 'var(--color-primary)',
                btn_text: '确认充值',
                notice: '1. 仅用于查看作者推荐数据\n2. 充值金额可提现到捆绑银行卡，最少充值和提现金额为30元'
            }"
            :styles="{
                padding_top: 10,
                padding_bottom: 10
            }"
        />
    </view>
</template>

<script lang="ts" setup>
import { onUnmounted, computed } from 'vue'
import { onShow } from '@dcloudio/uni-app'
import wWalletHeader from '@/components/widgets/w-wallet-header/w-wallet-header.vue'
import wRechargePanel from '@/components/widgets/w-recharge-panel/w-recharge-panel.vue'
import { safeNavigateBack } from '@/utils/util'
import { useThemeStore } from '@/stores/theme'

const themeStore = useThemeStore()
const themeStyle = computed(() => `--color-primary: ${themeStore.primaryColor};`)

const handleRefreshWallet = () => {}

uni.$on('refreshWallet', handleRefreshWallet)

onUnmounted(() => {
    uni.$off('refreshWallet', handleRefreshWallet)
})

const goBack = () => {
    safeNavigateBack({ defaultUrl: '/pages/index/index' })
}

onShow(() => {
    uni.setNavigationBarTitle({ title: '个人钱包' })
})
</script>

<style lang="scss" scoped>
.user-wallet {
    background-color: #f5f5f5;
}
.bg-f5 {
    background-color: #f5f5f5;
}
</style>
