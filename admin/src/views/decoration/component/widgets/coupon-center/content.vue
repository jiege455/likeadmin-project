<template>
    <div 
        class="coupon-center p-3"
        :style="{ 
            paddingTop: `${styles.padding_top}px`, 
            paddingBottom: `${styles.padding_bottom}px`,
            backgroundColor: styles.bg_color
        }"
    >
        <!-- 模拟 Tab 栏 -->
        <div class="flex mb-3 bg-white p-2 rounded-lg" v-if="content.show_my_coupon">
            <div class="flex-1 text-center font-bold text-red-500 border-b-2 border-red-500 pb-1">领券中心</div>
            <div class="flex-1 text-center text-gray-500 pb-1">我的优惠券</div>
        </div>

        <!-- 模拟优惠券列表 -->
        <div class="space-y-3">
            <div v-for="i in 2" :key="i" class="flex bg-white rounded-lg overflow-hidden h-24 shadow-sm">
                <!-- 左侧 -->
                <div class="w-24 bg-red-50 flex flex-col items-center justify-center text-red-500 border-r border-red-100 border-dashed relative">
                    <div class="text-xl font-bold">¥100</div>
                    <div class="text-xs mt-1">满300可用</div>
                </div>
                <!-- 右侧 -->
                <div class="flex-1 p-3 flex flex-col justify-between">
                    <div>
                        <div class="font-bold text-sm">全场通用券</div>
                        <div class="text-xs text-gray-400 mt-1">2023-12-31 前有效</div>
                    </div>
                    <div class="flex justify-between items-end">
                        <div class="text-xs text-red-500 border border-red-500 px-1 rounded">通用</div>
                        <div 
                            class="px-3 py-1 rounded-full text-white text-xs"
                            :style="{ backgroundColor: content.theme_color }"
                        >
                            立即领取
                        </div>
                    </div>
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
