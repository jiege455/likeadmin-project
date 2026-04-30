<template>
    <el-form label-width="70px">
        <el-card shadow="never" class="!border-none flex mt-2">
            <el-form-item label="主标题">
                <el-input v-model="content.title" placeholder="请输入主标题" />
            </el-form-item>
            <el-form-item label="副标题">
                <el-input v-model="content.desc" placeholder="请输入副标题" />
            </el-form-item>
            <el-form-item label="对齐方式">
                <el-radio-group v-model="content.align">
                    <el-radio label="left">左对齐</el-radio>
                    <el-radio label="center">居中</el-radio>
                    <el-radio label="right">右对齐</el-radio>
                </el-radio-group>
            </el-form-item>
        </el-card>

        <el-card shadow="never" class="!border-none flex mt-2">
            <el-form-item label="背景颜色">
                <color-picker v-model="styles.bg_color" />
            </el-form-item>
            <el-form-item label="主标题颜色">
                <color-picker v-model="styles.title_color" />
            </el-form-item>
            <el-form-item label="副标题颜色">
                <color-picker v-model="styles.desc_color" />
            </el-form-item>
        </el-card>
    </el-form>
</template>

<script setup lang="ts">
import ColorPicker from '@/components/color-picker/index.vue'

defineProps({
    content: {
        type: Object,
        default: () => ({})
    },
    styles: {
        type: Object,
        default: () => ({})
    }
})
</script>
