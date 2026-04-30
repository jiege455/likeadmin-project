<!--
  开发者公众号：杰哥网络科技。
  qq2711793818 杰哥
-->
<template>
    <div>
        <el-form label-width="80px">
            <el-form-item label="背景设置">
                <el-radio-group :model-value="content.background_type" @change="updateContent('background_type', $event)">
                    <el-radio :label="1">背景图片</el-radio>
                    <el-radio :label="2">纯色背景</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="背景图片" v-if="content.background_type == 1">
                <material-picker :model-value="content.background_image" @update:model-value="updateContent('background_image', $event)" exclude-domain />
                <div class="form-tips">建议尺寸：750*350px</div>
            </el-form-item>
            <el-form-item label="背景颜色" v-if="content.background_type == 2">
                <color-picker :model-value="content.background_color" @change="updateContent('background_color', $event)" default-color="#FF2D3A" />
                <div class="form-tips">默认使用系统主题色</div>
            </el-form-item>
            <el-form-item label="文字颜色">
                <color-picker :model-value="content.text_color" @change="updateContent('text_color', $event)" default-color="#ffffff" />
            </el-form-item>
        </el-form>
    </div>
</template>
<script lang="ts" setup>
import type { PropType } from 'vue'

import type options from './options'

type OptionsType = ReturnType<typeof options>
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

const emit = defineEmits(['update:content'])

const updateContent = (key: string, value: any) => {
    emit('update:content', {
        ...props.content,
        [key]: value
    })
}
</script>

<style lang="scss" scoped></style>
