<!--
  开发者公众号：杰哥网络科技。
  qq2711793818 杰哥
-->
<template>
    <div>
        <el-form label-width="90px" size="large" label-position="top">
            <el-card shadow="never" class="!border-none flex mt-2">
                <el-form-item label="平台名称">
                    <el-input
                        class="w-[400px]"
                        show-word-limit
                        maxlength="20"
                        :model-value="content.title" @change="updateContent('title', $event)"
                    />
                </el-form-item>

                <el-form-item label="客服二维码">
                    <div>
                        <material-picker :model-value="content.qrcode" @change="updateContent('qrcode', $event)" exclude-domain />
                    </div>
                </el-form-item>

                <el-form-item label="备注">
                    <el-input
                        class="w-[400px]"
                        show-word-limit
                        maxlength="20"
                        :model-value="content.remark" @change="updateContent('remark', $event)"
                    />
                </el-form-item>
                <el-form-item label="联系电话">
                    <el-input class="w-[400px]" :model-value="content.mobile" @change="updateContent('mobile', $event)" />
                </el-form-item>
                <el-form-item label="服务时间">
                    <el-input
                        class="w-[400px]"
                        show-word-limit
                        maxlength="20"
                        :model-value="content.time" @change="updateContent('time', $event)"
                    />
                </el-form-item>
            </el-card>
        </el-form>
    </div>
</template>
<script lang="ts" setup>
import type { PropType } from 'vue'

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
