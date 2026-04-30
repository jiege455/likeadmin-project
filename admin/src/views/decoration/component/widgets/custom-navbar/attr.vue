<!--
  开发者公众号：杰哥网络科技。
  qq2711793818 杰哥
-->
<template>
    <div>
        <el-form label-width="70px">
            <el-card shadow="never" class="!border-none flex mt-2">
                <div class="flex items-end mb-4">
                    <div class="text-base text-[#101010] font-medium">导航栏设置</div>
                </div>
                <el-form-item label="标题文字">
                    <el-input :model-value="content.title" @change="updateContent('title', $event)" placeholder="请输入页面标题" clearable />
                </el-form-item>
                <el-form-item label="背景颜色">
                    <el-color-picker :model-value="content.bg_color" @change="updateContent('bg_color', $event)" />
                </el-form-item>
                <el-form-item label="文字颜色">
                    <el-color-picker :model-value="content.text_color" @change="updateContent('text_color', $event)" />
                </el-form-item>
                <el-form-item label="返回按钮">
                    <el-switch :model-value="content.show_back" @change="updateContent('show_back', $event)" :active-value="1" :inactive-value="0" />
                </el-form-item>
                <el-form-item label="固定顶部">
                    <el-switch :model-value="content.fixed" @change="updateContent('fixed', $event)" :active-value="1" :inactive-value="0" />
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
