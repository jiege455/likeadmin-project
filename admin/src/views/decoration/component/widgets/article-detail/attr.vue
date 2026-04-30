<template>
    <div>
        <el-form label-width="80px">
            <el-form-item label="样式风格">
                <el-radio-group v-model="content.style">
                    <el-radio label="simple">简洁模式</el-radio>
                    <el-radio label="detail">详细模式</el-radio>
                </el-radio-group>
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
