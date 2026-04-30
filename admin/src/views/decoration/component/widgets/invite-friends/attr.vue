<template>
    <div>
        <el-form label-width="70px">
            <el-form-item label="标题">
                <el-input v-model="content.title" />
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
