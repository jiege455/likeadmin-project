<template>
    <div>
        <el-form label-width="70px">
            <el-form-item label="主标题">
                <el-input v-model="content.title" />
            </el-form-item>
            <el-form-item label="副标题">
                <el-input v-model="content.desc" />
            </el-form-item>
            <el-form-item label="按钮文字">
                <el-input v-model="content.btnText" />
            </el-form-item>
            <el-form-item label="跳转链接">
                <link-picker v-model="content.link" />
            </el-form-item>
        </el-form>
    </div>
</template>

<script lang="ts" setup>
import type { PropType } from 'vue'
import type { optionsType } from './options'
import LinkPicker from '@/components/link/picker.vue'

defineProps({
    content: {
        type: Object as PropType<optionsType>,
        default: () => ({})
    },
    styles: {
        type: Object as PropType<any>,
        default: () => ({})
    }
})
</script>

<style lang="scss" scoped></style>
