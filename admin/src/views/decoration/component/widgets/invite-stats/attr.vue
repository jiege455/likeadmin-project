<!--
  开发者公众号：杰哥网络科技。
  qq2711793818 杰哥
-->
<template>
    <div>
        <el-form label-width="70px">
            <el-card shadow="never" class="!border-none flex mt-2">
                <div class="flex items-end mb-4">
                    <div class="text-base text-[#101010] font-medium">统计设置</div>
                </div>
                <el-form-item label="背景颜色">
                    <el-color-picker :model-value="content.bg_color" @change="updateContent('bg_color', $event)" />
                </el-form-item>
                <el-form-item label="文字颜色">
                    <el-color-picker :model-value="content.text_color" @change="updateContent('text_color', $event)" />
                </el-form-item>
            </el-card>

            <el-card shadow="never" class="!border-none flex mt-2">
                <div class="flex items-end mb-4">
                    <div class="text-base text-[#101010] font-medium">间距设置</div>
                </div>
                <el-form-item label="顶部间距">
                    <el-slider v-model="contentData.styles.padding_top" :max="50" show-input />
                </el-form-item>
                <el-form-item label="底部间距">
                    <el-slider v-model="contentData.styles.padding_bottom" :max="50" show-input />
                </el-form-item>
            </el-card>
        </el-form>
    </div>
</template>
<script lang="ts" setup>
import type { PropType } from 'vue'
import { computed } from 'vue'
import type options from './options'

type OptionsType = ReturnType<typeof options>
const emits = defineEmits<{
    (event: 'update:content', data: OptionsType['content']): void
    (event: 'update:styles', data: OptionsType['styles']): void
}>()

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



// 更新 content 的某个字段
const updateContent = (key, value) => {
    emit('update:content', { ...props.content, [key]: value })
}

// 更新 styles 的某个字段
const updateStyles = (key, value) => {
    emit('update:styles', { ...props.styles, [key]: value })
}
</script>

<style lang="scss" scoped></style>
