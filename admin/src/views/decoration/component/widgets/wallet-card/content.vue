<template>
    <div class="wallet-card flex justify-between items-center py-4 px-3 bg-white rounded-lg" :style="cardStyle">
        <div class="flex-1 text-center border-r border-gray-100" @click="handleLink(content.wallet_link)">
            <div class="text-lg font-bold mb-1" :style="{ color: balanceColor }">0.00</div>
            <div class="text-xs" :style="{ color: styles.label_color }">我的钱包</div>
        </div>
        <div class="flex-1 text-center" @click="handleLink(content.commission_link)">
            <div class="text-lg font-bold mb-1" :style="{ color: balanceColor }">0.00</div>
            <div class="text-xs" :style="{ color: styles.label_color }">我的佣金</div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue'

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

const balanceColor = computed(() => {
    // 如果没有设置颜色，或者颜色是默认的灰色(#333333)，则使用系统主题色
    // 注意：#000000 (纯黑) 被视为用户自定义颜色，不跟随主题
    if (!props.styles.balance_color || props.styles.balance_color === '#333333') {
        return 'var(--color-primary)'
    }
    return props.styles.balance_color
})

const cardStyle = computed(() => {
    return {
        background: props.styles.root_bg_color || '#ffffff',
        marginTop: `${props.styles.margin_top}px` || '0px',
        marginBottom: `${props.styles.margin_bottom}px` || '0px',
        borderRadius: `${props.styles.border_radius}px` || '8px'
    }
})

const handleLink = (link: any) => {
    // 预览组件不需要真实跳转
}
</script>

<style lang="scss" scoped>
.wallet-card {
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
}
</style>
