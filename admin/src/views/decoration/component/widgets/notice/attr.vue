<template>
    <el-form label-width="70px">
        <el-card shadow="never" class="!border-none flex mt-2">
            <el-form-item label="显示条数">
                <el-input-number v-model="content.limit" :min="1" :max="20" />
                <div class="form-tips">将自动获取后台【消息管理-系统公告】中最新发布的公告</div>
            </el-form-item>
            <el-form-item label="接收对象">
                <el-radio-group v-model="content.recipient">
                    <el-radio :label="1">全员</el-radio>
                    <el-radio :label="2">仅商户</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="背景颜色">
                <color-picker v-model="styles.bg_color" />
            </el-form-item>
            <el-form-item label="文字颜色">
                <color-picker v-model="styles.text_color" />
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
