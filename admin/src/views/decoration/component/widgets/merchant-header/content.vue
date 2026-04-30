<!--
  开发者公众号：杰哥网络科技。
  qq2711793818 杰哥
-->
<template>
    <div class="merchant-header" :style="bgStyle">
        <div class="h-[44px]"></div>
        
        <div class="flex justify-between items-center px-4 py-2 text-white">
            <div class="text-lg font-bold">{{ shopName }}</div>
            <div class="text-sm bg-white/20 px-3 py-1 rounded-full">切换商家</div>
        </div>

        <div class="mx-3 mt-2 bg-white rounded-xl p-4 relative z-10 shadow-lg">
            <div class="flex">
                <div class="w-[50px] h-[50px] rounded-full bg-gray-200 overflow-hidden shrink-0 border-2 border-red-100">
                    <el-icon :size="28" color="#ccc"><User /></el-icon>
                </div>
                <div class="ml-3 flex-1">
                    <div class="font-bold text-base mb-1">商家名称</div>
                    <div v-if="content.show_stats" class="text-xs text-blue-500 bg-blue-50 inline-block px-2 py-0.5 rounded mb-1">
                        推广员65%分成
                    </div>
                    <div class="text-xs text-gray-500 line-clamp-1">这是商家的简介描述信息</div>
                </div>
            </div>
            
            <div v-if="content.show_desc" class="mt-3 text-xs text-gray-600 bg-gray-50 p-3 rounded leading-relaxed">
                购买建议：在购买哪个作者数据前，请在搜索框输入该作者名字，查看在平台上的历史记录和评价，再决定是否购买。
            </div>
        </div>

        <div v-if="content.show_buttons" class="flex justify-around items-center px-4 py-4 text-white text-xs">
            <div v-if="content.show_wechat" class="flex flex-col items-center gap-1">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                    <el-icon :size="18"><ChatDotRound /></el-icon>
                </div>
                <span>商家微信</span>
            </div>
            
            <div v-if="content.show_share" class="flex flex-col items-center gap-1">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                    <el-icon :size="18"><Star /></el-icon>
                </div>
                <span>推广TA</span>
            </div>
            
            <div v-if="content.show_chat" class="flex flex-col items-center gap-1">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                    <el-icon :size="18"><ChatLineRound /></el-icon>
                </div>
                <span>私聊</span>
            </div>
            
            <div v-if="content.show_complain" class="flex flex-col items-center gap-1">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                    <el-icon :size="18"><Service /></el-icon>
                </div>
                <span>投诉反馈</span>
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
import type { PropType } from 'vue'
import { computed } from 'vue'
import { User, ChatDotRound, Star, ChatLineRound, Service } from '@element-plus/icons-vue'
import type options from './options'

type OptionsType = ReturnType<typeof options>
const props = defineProps({
    content: {
        type: Object as PropType<OptionsType['content']>,
        default: () => ({
            bg_type: 1,
            bg_color: '#e1251b',
            bg_image: '',
            show_stats: 1,
            show_desc: 1,
            show_buttons: 1,
            show_wechat: 1,
            show_share: 1,
            show_chat: 1,
            show_complain: 1
        })
    },
    styles: {
        type: Object as PropType<OptionsType['styles']>,
        default: () => ({})
    }
})

const bgStyle = computed(() => {
    const { bg_type, bg_color, bg_image } = props.content
    if (bg_type == 2 && bg_image) {
        return { backgroundImage: `url(${bg_image})`, backgroundSize: '100% auto', backgroundRepeat: 'no-repeat' }
    }
    return { backgroundColor: bg_color || '#e1251b' }
})
</script>

<style lang="scss" scoped>
.merchant-header {
    width: 375px;
    overflow: hidden;
}

.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
