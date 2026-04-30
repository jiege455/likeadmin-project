<template>
    <div>
        <el-form label-width="80px">
            <el-form-item label="列表标题">
                <el-input v-model="content.title" placeholder="请输入标题" />
            </el-form-item>
            <el-form-item label="显示数量">
                <el-input-number v-model="content.limit" :min="1" :max="20" />
            </el-form-item>
            <el-form-item label="显示价格">
                <el-switch v-model="content.showPrice" />
            </el-form-item>
            <el-form-item label="显示图片">
                <el-switch v-model="content.showImage" />
            </el-form-item>
        </el-form>
    </div>
</template>

<script lang="ts" setup>
import type { PropType } from 'vue'
import type { optionsType } from './options'

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
