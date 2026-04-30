<template>
    <div class="pages-menu flex flex-col h-full">
        <div class="p-2" v-if="showAdd">
            <el-button type="primary" class="w-full" @click="$emit('add')">
                <icon name="el-icon-Plus" class="mr-1" /> 新建页面
            </el-button>
        </div>
        <el-scrollbar class="flex-1">
            <el-menu
                :default-active="modelValue"
                class="w-[160px] min-h-full"
                @select="handleSelect"
            >
                <el-menu-item v-for="(item, key) in menus" :index="key" :key="item.id">
                    <div class="flex items-center justify-between w-full">
                        <span class="truncate">{{ item.name }}</span>
                        <el-icon 
                            v-if="item.id > 4 && item.type == 10" 
                            class="ml-2 text-gray-400 hover:text-red-500" 
                            @click.stop="$emit('delete', item.id)"
                        >
                            <Delete />
                        </el-icon>
                    </div>
                </el-menu-item>
            </el-menu>
        </el-scrollbar>
    </div>
</template>
<script lang="ts" setup>
import type { PropType } from 'vue'
import { Delete } from '@element-plus/icons-vue'

defineProps({
    menus: {
        type: Object as PropType<Record<string, any>>,
        default: () => ({})
    },
    modelValue: {
        type: String,
        default: '1'
    },
    showAdd: {
        type: Boolean,
        default: true
    }
})
const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void
    (event: 'add'): void
    (event: 'delete', id: number): void
}>()
const handleSelect = (index: string) => {
    emit('update:modelValue', index)
}
</script>

<style lang="scss" scoped>
.pages-menu {
    :deep(.el-menu) {
        border-right: none;
    }
    :deep(.el-menu-item) {
        border-color: transparent;
        &.is-active {
            border-right-width: 2px;
            border-color: var(--el-color-primary);
            background-color: var(--el-color-primary-light-9);
        }
    }
}
</style>
