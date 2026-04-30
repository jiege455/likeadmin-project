<template>
    <div class="recharge-panel p-4" :style="{ paddingTop: `${styles.padding_top}px`, paddingBottom: `${styles.padding_bottom}px` }">
        <!-- 充值方式 -->
        <div class="mb-6">
            <div class="text-base font-bold mb-3">第一步：选择充值方式</div>
            <div class="flex space-x-3">
                <div class="flex-1 flex flex-col items-center justify-center p-4 rounded-lg border-2 border-red-500 bg-red-50">
                    <div class="text-green-500 text-2xl mb-1">
                        <i class="el-icon-ChatDotRound"></i>
                    </div>
                    <span class="text-sm">微信支付</span>
                </div>
                <div class="flex-1 flex flex-col items-center justify-center p-4 rounded-lg border border-gray-200 bg-gray-50">
                    <div class="text-blue-500 text-2xl mb-1">
                        <i class="el-icon-PlatformEleme"></i>
                    </div>
                    <span class="text-sm">支付宝</span>
                </div>
            </div>
        </div>

        <!-- 充值金额 -->
        <div class="mb-6">
            <div class="text-base font-bold mb-3">第二步：选择充值金额</div>
            <div class="grid grid-cols-3 gap-3">
                <div 
                    v-for="(item, index) in content.amounts" 
                    :key="index"
                    class="flex items-center justify-center h-16 rounded-lg border transition-all text-lg font-medium cursor-pointer"
                    :class="index === 0 ? 'border-red-500 bg-red-50 text-red-500' : 'border-gray-200 bg-gray-50 text-gray-600'"
                >
                    {{ item.text }}
                </div>
            </div>
            <!-- 自定义金额 -->
            <div v-if="content.show_custom_amount" class="mt-3 flex items-center bg-gray-50 rounded-lg px-4 h-12 border border-gray-200">
                <span class="text-gray-600 font-bold mr-2">¥</span>
                <span class="text-gray-400 text-sm">请输入充值金额</span>
                <span class="text-gray-600 ml-auto">元</span>
            </div>
        </div>

        <!-- 确认按钮 -->
        <div class="mt-8">
            <div 
                class="w-full h-11 flex items-center justify-center rounded-full text-white text-base font-medium"
                :style="{ backgroundColor: content.btn_color }"
            >
                {{ content.btn_text }}
            </div>
        </div>

        <!-- 说明 -->
        <div class="mt-6 text-xs text-gray-400 whitespace-pre-wrap leading-relaxed">
            <div class="mb-1">充值说明：</div>
            {{ content.notice }}
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
