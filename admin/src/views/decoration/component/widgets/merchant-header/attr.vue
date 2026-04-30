<!--
  开发者公众号：杰哥网络科技。
  qq2711793818 杰哥
-->
<template>
    <div>
        <el-form label-width="70px">
            <el-card shadow="never" class="!border-none flex mt-2">
                <div class="flex items-end mb-4">
                    <div class="text-base text-[#101010] font-medium">背景设置</div>
                </div>
                <el-form-item label="背景类型">
                    <el-radio-group :model-value="content.bg_type" @change="updateContent('bg_type', $event)">
                        <el-radio :label="1">纯色</el-radio>
                        <el-radio :label="2">图片</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="背景颜色" v-if="contentData.bg_type == 1">
                    <el-color-picker :model-value="content.bg_color" @change="updateContent('bg_color', $event)" />
                </el-form-item>
                <el-form-item label="背景图片" v-if="contentData.bg_type == 2">
                     <material-picker :model-value="content.bg_image" @change="updateContent('bg_image', $event)" :limit="1" />
                </el-form-item>
            </el-card>

            <el-card shadow="never" class="!border-none flex mt-2">
                <div class="flex items-end mb-4">
                    <div class="text-base text-[#101010] font-medium">显示设置</div>
                </div>
                <el-form-item label="显示统计">
                    <el-switch :model-value="content.show_stats" @change="updateContent('show_stats', $event)" :active-value="1" :inactive-value="0" />
                </el-form-item>
                <el-form-item label="显示简介">
                    <el-switch :model-value="content.show_desc" @change="updateContent('show_desc', $event)" :active-value="1" :inactive-value="0" />
                </el-form-item>
                <el-form-item label="底部按钮">
                    <el-switch :model-value="content.show_buttons" @change="updateContent('show_buttons', $event)" :active-value="1" :inactive-value="0" />
                </el-form-item>
                
                <template v-if="contentData.show_buttons">
                    <el-form-item label="商家微信">
                        <el-switch :model-value="content.show_wechat" @change="updateContent('show_wechat', $event)" :active-value="1" :inactive-value="0" />
                    </el-form-item>
                    <el-form-item label="推广TA">
                        <el-switch :model-value="content.show_share" @change="updateContent('show_share', $event)" :active-value="1" :inactive-value="0" />
                    </el-form-item>
                    <el-form-item label="私聊">
                        <el-switch :model-value="content.show_chat" @change="updateContent('show_chat', $event)" :active-value="1" :inactive-value="0" />
                    </el-form-item>
                    <el-form-item label="投诉反馈">
                        <el-switch :model-value="content.show_complain" @change="updateContent('show_complain', $event)" :active-value="1" :inactive-value="0" />
                    </el-form-item>
                </template>
            </el-card>

            <el-card shadow="never" class="!border-none flex mt-2">
                <div class="flex items-end mb-4">
                    <div class="text-base text-[#101010] font-medium">链接设置</div>
                </div>
                <el-form-item label="切换商家">
                    <link-picker :model-value="content.switch_link" @change="updateContent('switch_link', $event)" />
                </el-form-item>
            </el-card>
        </el-form>
    </div>
</template>
<script lang="ts" setup>
import type { PropType } from 'vue'
import { computed } from 'vue'
import MaterialPicker from '@/components/material/picker.vue'
import LinkPicker from '@/components/link/picker.vue'
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
