<template>
    <el-form label-width="70px">
        <el-card shadow="never" class="!border-none flex mt-2">
            <el-form-item label="封面图">
                <material-picker v-model="content.image" upload-class="bg-body" exclude-domain />
            </el-form-item>
            <el-form-item label="标题">
                <el-input v-model="content.title" placeholder="请输入直播标题" />
            </el-form-item>
             <el-form-item label="跳转链接">
                <link-picker v-model="content.link" />
            </el-form-item>
        </el-card>

        <el-card shadow="never" class="!border-none flex mt-2">
            <el-form-item label="背景颜色">
                <color-picker v-model="styles.bg_color" />
            </el-form-item>
             <el-form-item label="文字颜色">
                <color-picker v-model="styles.text_color" />
            </el-form-item>
             <el-form-item label="外边距">
                <el-slider v-model="styles.margin" :max="20" />
            </el-form-item>
            <el-form-item label="内边距">
                <el-slider v-model="styles.padding" :max="20" />
            </el-form-item>
             <el-form-item label="圆角">
                <el-slider v-model="styles.radius" :max="20" />
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
