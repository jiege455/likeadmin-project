<!--
  开发者公众号：杰哥网络科技。
  qq2711793818 杰哥
-->
<template>
    <div>
        <el-form label-width="70px">
            <el-card shadow="never" class="!border-none flex mt-2">
                <div class="flex items-end mb-4">
                    <div class="text-base text-[#101010] font-medium">面板设置</div>
                </div>
                <el-form-item label="按钮颜色">
                    <el-color-picker :model-value="content.btn_color" @change="updateContent('btn_color', $event)" />
                </el-form-item>
                <el-form-item label="按钮文字">
                    <el-input :model-value="content.btn_text" @change="updateContent('btn_text', $event)" />
                </el-form-item>
                <el-form-item label="自定义金额">
                    <el-switch :model-value="content.show_custom_amount" @change="updateContent('show_custom_amount', $event)" :active-value="1" :inactive-value="0" />
                </el-form-item>
                <el-form-item label="充值说明">
                    <el-input :model-value="content.notice" @change="updateContent('notice', $event)" type="textarea" :rows="4" />
                </el-form-item>
            </el-card>

            <el-card shadow="never" class="!border-none flex mt-2">
                <div class="flex items-end mb-4">
                    <div class="text-base text-[#101010] font-medium">充值选项</div>
                </div>
                <div v-for="(item, index) in contentData.amounts" :key="index" class="flex mb-2">
                    <el-input v-model="item.value" type="number" placeholder="金额" class="mr-2" style="width: 100px" />
                    <el-input v-model="item.text" placeholder="显示文本" class="flex-1" />
                </div>
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
