<template>
    <div class="distribution-poster bg-gray-100 p-4 flex justify-center min-h-[500px]">
        <div class="poster-container relative bg-white shadow-lg overflow-hidden" 
             :style="{ width: '300px', height: '500px' }">
            
            <!-- 背景图 -->
            <div class="absolute inset-0 bg-blue-500">
                <img v-if="content.bg_image" :src="content.bg_image" class="w-full h-full object-cover" />
                <div v-else class="w-full h-full flex items-center justify-center text-white bg-gradient-to-br from-blue-400 to-purple-500">
                    默认背景图
                </div>
            </div>

            <!-- 用户信息区域 -->
            <div class="absolute bottom-0 w-full bg-white/90 backdrop-blur-sm p-4 flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-gray-200 overflow-hidden">
                        <img src="https://img.yzcdn.cn/vant/cat.jpeg" class="w-full h-full object-cover" />
                    </div>
                    <div v-if="content.show_nickname" class="ml-2">
                        <div class="text-sm font-bold">微信用户</div>
                        <div class="text-xs text-gray-500">邀请您加入</div>
                    </div>
                </div>
                <!-- 二维码 -->
                <div v-if="content.show_qrcode" class="w-16 h-16 bg-white p-1 rounded">
                    <img src="https://img.yzcdn.cn/vant/cat.jpeg" class="w-full h-full object-cover" />
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
