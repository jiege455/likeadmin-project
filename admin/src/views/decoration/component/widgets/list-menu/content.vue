<template>
    <div class="list-menu bg-white rounded-lg overflow-hidden mx-3" :style="{ marginBottom: `${styles.margin_bottom}px` }">
        <div 
            v-for="(item, index) in showList" 
            :key="index" 
            class="flex items-center p-4 border-b border-gray-50 last:border-none"
        >
            <decoration-img width="24px" height="24px" :src="item.image" alt="" />
            <div class="flex-1 ml-3 text-sm text-gray-800">{{ item.name }}</div>
            <el-icon class="text-gray-400"><ArrowRight /></el-icon>
        </div>
    </div>
</template>
<script lang="ts" setup>
import type { PropType } from 'vue'
import { ArrowRight } from '@element-plus/icons-vue'
import DecorationImg from '../../decoration-img.vue'
import type options from './options'
import { computed } from 'vue'

type OptionsType = ReturnType<typeof options>
const props = defineProps({
    content: {
        type: Object as PropType<OptionsType['content']>,
        default: () => ({})
    },
    styles: {
        type: Object as PropType<OptionsType['styles']>,
        default: () => ({})
    }
})

const showList = computed(() => {
    return props.content.data?.filter((item: any) => item.is_show == '1') || []
})
</script>

<style lang="scss" scoped>
.list-menu {
    box-shadow: 0 0 10px rgba(0,0,0,0.05);
}
</style>
