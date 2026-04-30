<!--
  开发者公众号：杰哥网络科技。
  qq2711793818 杰哥
-->
<template>
    <el-form ref="form" label-width="80px" size="large">
        <el-card shadow="never" class="!border-none flex mt-2">
            <el-form-item label="页面名称">
                <el-input
                    :model-value="content.title" @change="updateContent('title', $event)"
                    maxlength="12"
                    show-word-limit
                    class="w-[300px]"
                    placeholder="请输入页面名称(菜单显示)"
                ></el-input>
                <div class="form-tips text-xs text-gray-400 mt-1">该名称用于后台菜单显示</div>
            </el-form-item>
            <el-form-item label="顶部标题">
                <el-radio-group :model-value="content.title_type" @change="updateContent('title_type', $event)">
                    <el-radio :label="1">文字</el-radio>
                    <el-radio :label="2">图片</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item v-if="content.title_type == 1" label="标题文字">
                <el-input
                    :model-value="content.title_text" @change="updateContent('title_text', $event)"
                    maxlength="8"
                    show-word-limit
                    class="w-[300px]"
                    placeholder="请输入顶部标题文字"
                ></el-input>
            </el-form-item>
            <el-form-item v-if="content.title_type == 2">
                <material-picker :model-value="content.title_img" @change="updateContent('title_img', $event)" :limit="1" size="100px" />
                <div class="form-tips">建议图片尺寸：300px*40px</div>
            </el-form-item>
            <el-form-item label="文字颜色" v-if="content.title_type == 1">
                <el-radio-group :model-value="content.text_color" @change="updateContent('text_color', $event)">
                    <el-radio label="1">白色</el-radio>
                    <el-radio label="2">黑色</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="页面背景">
                <el-radio-group :model-value="content.bg_type" @change="updateContent('bg_type', $event)">
                    <el-radio :label="1">背景颜色</el-radio>
                    <el-radio :label="2">背景图片</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item v-if="content.bg_type == 1">
                <color-picker :model-value="content.bg_color" @change="updateContent('bg_color', $event)" reset-color="#F5F5F5" />
            </el-form-item>
            <el-form-item v-if="content.bg_type == 2">
                <material-picker :model-value="content.bg_image" @change="updateContent('bg_image', $event)" :limit="1" size="100px" />
                <div class="form-tips">建议图片尺寸：750px*高度不限</div>
            </el-form-item>
        </el-card>
    </el-form>
</template>
<script lang="ts" setup>
import type { PropType } from 'vue'
import { computed } from 'vue'
import MaterialPicker from '@/components/material/picker.vue'
import ColorPicker from '@/components/color-picker/index.vue'

import type options from './options'

type OptionsType = ReturnType<typeof options>
const emits = defineEmits<(event: 'update:content', data: OptionsType['content']) => void>()
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
