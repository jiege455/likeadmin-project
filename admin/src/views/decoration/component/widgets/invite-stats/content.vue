<template>
    <div class="invite-stats px-3" :style="{ paddingTop: `${styles.padding_top}px`, paddingBottom: `${styles.padding_bottom}px` }">
        <div class="stats-card rounded-lg p-5 flex justify-between items-center" 
             :style="{ backgroundColor: content.bg_color, color: content.text_color }">
            <div class="flex-1 text-center border-r border-white/20">
                <div class="text-sm opacity-80 mb-1">今日邀请 (人)</div>
                <div class="text-2xl font-bold">12</div>
            </div>
            <div class="flex-1 text-center">
                <div class="text-sm opacity-80 mb-1">累计邀请 (人)</div>
                <div class="text-2xl font-bold">345</div>
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
