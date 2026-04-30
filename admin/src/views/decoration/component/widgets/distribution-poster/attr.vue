<!--
  开发者公众号：杰哥网络科技。
  qq2711793818 杰哥
-->
<template>
    <div>
        <el-form label-width="70px">
            <el-card shadow="never" class="!border-none flex mt-2">
                <div class="flex items-end mb-4">
                    <div class="text-base text-[#101010] font-medium">海报设置</div>
                </div>
                <el-form-item label="背景图片">
                    <material-picker :model-value="content.bg_image" @change="updateContent('bg_image', $event)" upload-class="bg-body" />
                    <div class="form-tips">建议尺寸：750px*1334px</div>
                </el-form-item>
                <el-form-item label="显示昵称">
                    <el-switch :model-value="content.show_nickname" @change="updateContent('show_nickname', $event)" :active-value="1" :inactive-value="0" />
                </el-form-item>
                <el-form-item label="显示二维码">
                    <el-switch :model-value="content.show_qrcode" @change="updateContent('show_qrcode', $event)" :active-value="1" :inactive-value="0" />
                </el-form-item>
            </el-card>
        </el-form>
    </div>
</template>
<script lang="ts" setup>
import type { PropType } from 'vue'
import { computed } from 'vue'
import MaterialPicker from '@/components/material/picker.vue'
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
