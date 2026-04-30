<!--
  开发者公众号：杰哥网络科技。
  qq2711793818 杰哥
-->
<template>
    <div>
        <el-form label-width="70px">
            <el-card shadow="never" class="!border-none flex mt-2">
                <div class="flex items-end mb-4">
                    <div class="text-base text-[#101010] font-medium">展示样式</div>
                </div>
                <el-radio-group :model-value="content.style" @change="updateContent('style', $event)">
                    <el-radio :value="1">固定显示</el-radio>
                    <el-radio :value="2">分页滑动</el-radio>
                </el-radio-group>
                <el-form-item label="每行数量" class="mt-4">
                    <el-select :model-value="content.per_line" @change="updateContent('per_line', $event)" style="width: 300px">
                        <el-option
                            v-for="item in 5"
                            :key="item"
                            :label="item + '个'"
                            :value="item"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item label="显示行数">
                    <el-select :model-value="content.show_line" @change="updateContent('show_line', $event)" style="width: 300px">
                        <el-option
                            v-for="item in 2"
                            :key="item"
                            :label="item + '行'"
                            :value="item"
                        />
                    </el-select>
                </el-form-item>
            </el-card>
            <el-card shadow="never" class="!border-none flex mt-2">
                <div class="flex items-end">
                    <div class="text-base text-[#101010] font-medium">菜单设置</div>
                    <div class="text-xs text-tx-secondary ml-2">建议图片尺寸：100px*100px</div>
                </div>
                <div class="flex-1 mt-4">
                    <AddNav :model-value="content.data" @change="updateContent('data', $event)" />
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
