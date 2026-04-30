<template>
    <el-form label-width="70px">
        <el-card shadow="never" class="!border-none flex mt-2">
            <el-form-item label="封面图">
                <material-picker v-model="content.poster" upload-class="bg-body" exclude-domain />
            </el-form-item>
            <el-form-item label="视频链接">
                <div class="flex-1">
                     <el-input v-model="content.url" placeholder="请输入视频链接" />
                     <div class="form-tips">请填写视频的完整链接，支持mp4格式</div>
                </div>
            </el-form-item>
             <el-form-item label="自动播放">
                <el-switch v-model="content.autoplay" :active-value="1" :inactive-value="0" />
            </el-form-item>
        </el-card>

        <el-card shadow="never" class="!border-none flex mt-2">
            <el-form-item label="背景颜色">
                <color-picker v-model="styles.bg_color" />
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
