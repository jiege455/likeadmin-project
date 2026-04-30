<template>
    <div class="user-info flex flex-col" :style="bgStyle">
        <div class="flex items-center px-[25px] py-[30px]">
            <img src="@/assets/images/default_image.png" class="w-[60px] h-[60px] rounded-full" alt="" />
            <div class="ml-[15px] flex flex-col" :style="{ color: textColor }">
                <div class="text-[16px] font-medium">账号：13800138000</div>
                <div class="text-[12px] mt-[5px] flex items-center">
                    ID：12345678
                    <el-icon class="ml-1 text-[12px]"><DocumentCopy /></el-icon>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { computed, inject, ref, type Ref } from 'vue'
import { DocumentCopy } from '@element-plus/icons-vue'

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

// 注入系统主题色（默认蓝色，与系统风格主题一致）
// 注意：inject 返回的可能是一个 ref 对象
const injectedThemeColor = inject<Ref<string>>('themeColor', ref('#2F80ED'))

// 获取实际的主题色值
const themeColor = computed(() => {
    // 如果是 ref 对象，取 value；否则直接使用
    return injectedThemeColor?.value || injectedThemeColor || '#2F80ED'
})

const bgStyle = computed(() => {
    const { background_type, background_image, background_color } = props.content;
    const style: any = {};
    
    // 添加过渡效果，使颜色变化平滑
    style.transition = 'background-color 0.3s ease, background-image 0.3s ease';
    
    if (background_type == 1) {
        if (background_image) {
            style.backgroundImage = `url(${background_image})`;
            style.backgroundRepeat = 'no-repeat';
            style.backgroundSize = 'cover';
            style.backgroundPosition = 'center';
        } else {
            // 使用系统主题色（默认蓝色）
            style.backgroundColor = themeColor.value;
        }
    } else if (background_type == 2 && background_color) {
        style.backgroundColor = background_color;
        style.backgroundImage = 'none';
    } else {
        // 默认使用系统主题色（默认蓝色）
        style.backgroundColor = themeColor.value;
    }
    
    return style;
})

// 文字颜色计算
const textColor = computed(() => {
    // 如果设置了文字颜色，使用设置的；否则根据背景判断
    if (props.content.text_color) {
        return props.content.text_color;
    }
    // 默认白色文字
    return '#ffffff';
})
</script>

<style lang="scss" scoped>
.user-info {
    // 基础高度
    min-height: 160px;
    
    // 添加过渡效果，避免颜色突变产生幻影
    transition: background-color 0.3s ease;
    
    // 添加 will-change 优化渲染性能
    will-change: background-color;
    
    // 防止内容闪烁
    * {
        // 子元素也添加过渡
        transition: color 0.2s ease;
    }
}
</style>
