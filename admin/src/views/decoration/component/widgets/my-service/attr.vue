<!--
  开发者公众号：杰哥网络科技。
  qq2711793818 杰哥
-->
<template>
    <div>
        <el-form label-width="70px">
            <el-card shadow="never" class="!border-none flex mt-2">
                <el-form-item label="标题">
                    <el-input class="w-[396px]" :model-value="content.title" @input="updateContent('title', $event)" />
                </el-form-item>
            </el-card>
            <el-card shadow="never" class="!border-none flex mt-2">
                <div class="flex items-end mb-4">
                    <div class="text-base text-[#101010] font-medium">展示样式</div>
                </div>
                <el-radio-group :model-value="content.style" @change="updateContent('style', $event)">
                    <el-radio :value="1">横排</el-radio>
                    <el-radio :value="2">竖排</el-radio>
                </el-radio-group>
            </el-card>
            <el-card shadow="never" class="!border-none flex mt-2">
                <div class="flex items-end mb-4">
                    <div class="text-base text-[#101010] font-medium">菜单</div>
                    <div class="text-xs text-tx-secondary ml-2">建议图片尺寸：100px*100px</div>
                </div>
                <div class="flex-1">
                    <AddNav :model-value="content.data" @update:model-value="updateContent('data', $event)" />
                </div>
            </el-card>
        </el-form>
    </div>
</template>
<script lang="ts" setup>
import type { PropType } from 'vue'

import AddNav from '../../add-nav.vue'
import type options from './options'

type OptionsType = ReturnType<typeof options>
const emit = defineEmits<(event: 'update:content', data: OptionsType['content']) => void>()
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
const updateContent = (key: string, value: any) => {
    emit('update:content', {
        ...props.content,
        [key]: value
    })
}
</script>

<style lang="scss" scoped></style>
