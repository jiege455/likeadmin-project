<template>
    <el-form label-width="70px">
        <el-card shadow="never" class="!border-none flex mt-2">
            <el-form-item label="线条颜色">
                <color-picker v-model="styles.line_color" />
            </el-form-item>
            <el-form-item label="线条样式">
                <el-radio-group v-model="content.style">
                    <el-radio label="solid">实线</el-radio>
                    <el-radio label="dashed">虚线</el-radio>
                    <el-radio label="dotted">点线</el-radio>
                </el-radio-group>
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
