<!--
  开发者公众号：杰哥网络科技。
  qq2711793818 杰哥
-->
<template>
    <div class="merchant-content-list bg-gray-50" :style="{ paddingTop: `${styles.padding_top || 10}px`, paddingBottom: `${styles.padding_bottom || 10}px` }">
        <div class="px-3">
            <div class="font-bold text-base mb-3 border-l-4 border-red-500 pl-2">全部内容</div>
            
            <div v-if="content.style === 'single'" class="space-y-3">
                <div v-for="i in 2" :key="i" class="flex py-3 border-b border-gray-100 bg-white">
                    <div class="w-[120px] h-[90px] rounded-lg bg-gray-200 flex-shrink-0 overflow-hidden">
                        <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200"></div>
                    </div>
                    <div class="ml-3 flex-1 flex flex-col justify-between py-1">
                        <div>
                            <div class="text-sm font-bold line-clamp-2 leading-snug">资料标题示例，这是一条商家发布的资料内容</div>
                            <div class="text-xs text-gray-400 mt-1 line-clamp-1">资料简介描述信息</div>
                        </div>
                        <div class="flex justify-between items-end">
                            <div class="flex items-center text-xs text-gray-400">
                                <el-icon class="mr-1"><View /></el-icon>
                                123
                            </div>
                            <div>
                                <span v-if="content.show_price" class="text-red-500 font-bold">
                                    <span class="text-xs">¥</span>38
                                </span>
                                <span v-else class="text-green-500 font-bold text-sm">免费</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div v-else class="grid grid-cols-2 gap-3">
                <div v-for="i in 4" :key="i" class="bg-white rounded-lg overflow-hidden">
                    <div class="w-full h-[100px] bg-gradient-to-br from-gray-100 to-gray-200"></div>
                    <div class="p-2">
                        <div class="text-sm font-bold line-clamp-2">资料标题示例</div>
                        <div class="flex justify-between items-center mt-2">
                            <div class="flex items-center text-xs text-gray-400">
                                <el-icon class="mr-1"><View /></el-icon>
                                123
                            </div>
                            <span v-if="content.show_price" class="text-red-500 font-bold text-sm">
                                <span class="text-xs">¥</span>38
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
import type { PropType } from 'vue'
import { View } from '@element-plus/icons-vue'
import type options from './options'

type OptionsType = ReturnType<typeof options>
defineProps({
    content: {
        type: Object as PropType<OptionsType['content']>,
        default: () => ({
            style: 'single',
            show_tags: 1,
            show_price: 1
        })
    },
    styles: {
        type: Object as PropType<OptionsType['styles']>,
        default: () => ({
            padding_top: 10,
            padding_bottom: 10
        })
    }
})
</script>

<style lang="scss" scoped>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
