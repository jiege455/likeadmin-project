<!--
  开发者公众号：杰哥网络科技。
  qq2711793818 杰哥
-->
<template>
    <div>
        <el-form label-width="70px">
            <el-card shadow="never" class="!border-none flex mt-2">
                <div class="flex items-end mb-4">
                    <div class="text-base text-[#101010] font-medium">列表设置</div>
                </div>
                <el-form-item label="列表样式">
                    <el-radio-group :model-value="content.style" @change="updateContent('style', $event)">
                        <el-radio label="single">单列大图</el-radio>
                        <el-radio label="double">双列瀑布流</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="显示标签">
                    <el-switch :model-value="content.show_tags" @change="updateContent('show_tags', $event)" :active-value="1" :inactive-value="0" />
                </el-form-item>
                <el-form-item label="显示价格">
                    <el-switch :model-value="content.show_price" @change="updateContent('show_price', $event)" :active-value="1" :inactive-value="0" />
                </el-form-item>
            </el-card>

            <el-card shadow="never" class="!border-none flex mt-2">
                <div class="flex items-end mb-4">
                    <div class="text-base text-[#101010] font-medium">间距设置</div>
                </div>
                <el-form-item label="顶部间距">
                    <el-slider :model-value="styles.padding_top" @change="updateStyles('padding_top', $event)" :max="50" show-input />
                </el-form-item>
                <el-form-item label="底部间距">
                    <el-slider :model-value="styles.padding_bottom" @change="updateStyles('padding_bottom', $event)" :max="50" show-input />
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
