<template>
    <div class="page-meta">
        <div class="nav-bar flex items-center justify-center relative h-[44px]" 
             :style="{ color: content.text_color === '1' ? '#ffffff' : '#000000' }">
            <div class="relative z-10 text-base font-medium truncate max-w-[200px]">
                <img v-if="content.title_type == 2 && content.title_img" :src="content.title_img" class="h-[30px] object-contain" />
                <span v-else>{{ content.title_text || content.title || '页面标题' }}</span>
            </div>
            <div class="absolute left-3 z-10 text-xl">
                <span class="iconfont icon-arrow-left"></span>
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

<style lang="scss" scoped>
.page-meta {
    width: 100%;
    // 模拟状态栏高度
    padding-top: 20px; 
}
.nav-bar {
    // 确保标题栏在最上层
    z-index: 10;
}
</style>