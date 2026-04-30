<template>
    <div class="wallet-header" :style="{ paddingTop: `${styles.padding_top}px`, paddingBottom: `${styles.padding_bottom}px` }">
        <div class="header-card p-5 pb-8 relative" :style="{ backgroundColor: content.bg_color, color: content.text_color }">
            <div class="text-center">
                <div class="text-lg mb-2">个人钱包</div>
                <div v-if="content.show_balance" class="text-4xl font-bold">¥ 0.00</div>
                <div v-if="content.show_detail_btn" class="inline-block mt-2 px-3 py-1 border border-white rounded text-xs">
                    资金明细
                </div>
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
import type { PropType } from 'vue'
import type options from './options'

type OptionsType = ReturnType<typeof options>
defineProps({
    content: {
        type: Object as PropType<OptionsType['content']>,
        default: () => ({})
    },
    styles: {
        type: Object as PropType<OptionsType['styles']>,
        default: () => ({})
    }
})
</script>

<style lang="scss" scoped></style>
