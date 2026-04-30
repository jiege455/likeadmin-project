<template>
    <div class="custom-navbar relative">
        <div 
            class="flex items-center justify-between px-4 h-11 border-b"
            :style="{ backgroundColor: content.bg_color, color: content.text_color }"
        >
            <div class="w-20 flex items-center">
                <i v-if="content.show_back" class="el-icon-ArrowLeft text-xl"></i>
            </div>
            <div class="font-bold text-base truncate flex-1 text-center">{{ content.title }}</div>
            <div class="w-20"></div>
        </div>
        <div v-if="content.fixed" class="absolute top-0 right-0 bg-red-500 text-white text-xs px-1">
            固定
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
