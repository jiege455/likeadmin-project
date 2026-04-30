<!--
  开发者公众号：杰哥网络科技。
  qq2711793818 杰哥
  功能说明：悬浮按钮组件预览
-->
<template>
    <div class="float-btn-preview" :style="containerStyle">
        <div 
            class="btn-card flex items-center justify-between px-4 py-3 rounded-xl"
            :style="{ backgroundColor: content.bg_color || '#EF4444', borderRadius: `${styles.border_radius || 12}px` }"
        >
            <div class="flex items-center">
                <div 
                    class="icon-box w-10 h-10 rounded-full flex items-center justify-center"
                    style="background-color: rgba(255,255,255,0.2);"
                >
                    <el-icon size="20" :color="content.icon_color || '#ffffff'"><Star /></el-icon>
                </div>
                <div class="ml-3">
                    <div class="text-base font-bold" :style="{ color: content.title_color || '#ffffff' }">
                        {{ content.title || '我的关注' }}
                    </div>
                    <div v-if="content.subtitle" class="text-xs mt-1" :style="{ color: content.subtitle_color || 'rgba(255,255,255,0.8)' }">
                        {{ content.subtitle }}
                    </div>
                </div>
            </div>
            <div 
                class="action-btn px-4 py-2 rounded-full text-sm font-medium"
                :style="{ 
                    backgroundColor: content.btn_bg_color || '#ffffff',
                    color: content.btn_color || '#EF4444'
                }"
            >
                {{ content.btn_text || '去查看' }}
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import { Star } from '@element-plus/icons-vue'

const props = defineProps({
    content: {
        type: Object,
        default: () => ({})
    },
    styles: {
        type: Object,
        default: () => ({})
    }
})

const containerStyle = computed(() => ({
    marginTop: `${props.styles.margin_top || 0}px`,
    marginBottom: `${props.styles.margin_bottom || 10}px`,
    marginLeft: `${props.styles.padding_horizontal || 12}px`,
    marginRight: `${props.styles.padding_horizontal || 12}px`
}))
</script>

<style lang="scss" scoped>
.float-btn-preview {
    .btn-card {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
}
</style>
