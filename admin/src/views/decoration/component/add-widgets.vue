<template>
    <div class="add-widgets">
        <div class="title text-base font-bold mb-4 flex items-center justify-between">
            <div class="flex items-center">
                <span class="mr-2 text-black">组件库</span>
                <el-tag size="small" type="info" effect="dark">拖拽或点击添加</el-tag>
            </div>
        </div>
        
        <el-scrollbar max-height="400px">
            <div class="widget-list">
                <div
                    v-for="(item, key) in widgetList"
                    :key="key"
                    class="widget-item"
                    @click="handleAdd(key)"
                >
                    <div class="widget-icon">
                        <i v-if="typeof item.icon === 'string' && item.icon.startsWith('icon-')" class="iconfont" :class="item.icon" style="font-size: 24px;"></i>
                        <icon v-else-if="typeof item.icon === 'string' && item.icon.startsWith('el-icon-')" :name="item.icon" size="24" />
                        <el-icon v-else :size="24">
                            <component :is="item.icon === 'element-plus' ? 'ElIcon' : item.icon" />
                        </el-icon>
                    </div>
                    <div class="widget-info">
                        <span class="widget-title">{{ item.title }}</span>
                        <!-- <span class="widget-desc">组件描述...</span> -->
                    </div>
                    <div class="widget-action">
                        <el-icon><Plus /></el-icon>
                    </div>
                </div>
            </div>
        </el-scrollbar>
    </div>
</template>

<script lang="ts" setup>
import widgets from './widgets'
import { computed } from 'vue'
import { Plus } from '@element-plus/icons-vue'

const props = defineProps({
    pageType: {
        type: String,
        default: '1'
    }
})

const emit = defineEmits(['add'])

const widgetList = computed(() => {
    const list: Record<string, any> = {}
    
    Object.keys(widgets).forEach(key => {
        const options = widgets[key] || {}
        // 排除 page-meta (页面设置)，其他所有组件均显示
        if (
            options.name && 
            options.title && 
            options.name !== 'page-meta'
        ) {
            list[key] = {
                title: options.title,
                icon: options.icon || 'el-icon-ElementPlus' // 使用有效的默认图标
            }
        }
    })
    return list
})

const handleAdd = (key: string) => {
    emit('add', key)
}
</script>

<style lang="scss" scoped>
.add-widgets {
    @apply bg-white p-4 rounded-lg shadow-sm border border-gray-100; // 改回白色背景
    
    .widget-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .widget-item {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        background-color: #f8f9fb; // 浅灰背景
        border: 1px solid transparent;
        border-radius: 8px;
        cursor: pointer;
        transition: border-color 0.2s, background-color 0.2s, transform 0.2s;

        &:hover {
            background-color: #fff;
            border-color: var(--el-color-primary);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);

            .widget-icon {
                color: var(--el-color-primary);
                background-color: var(--el-color-primary-light-9);
            }
            
            .widget-title {
                color: var(--el-color-primary);
            }

            .widget-action {
                opacity: 1;
                color: var(--el-color-primary);
            }
        }
    }

    .widget-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #fff;
        border-radius: 8px;
        color: #606266; // 深灰图标
        margin-right: 12px;
        transition: color 0.2s, background-color 0.2s;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
    }

    .widget-info {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .widget-title {
        font-size: 15px;
        font-weight: 600;
        color: #303133; // 深色文字
        transition: color 0.3s;
    }
    
    .widget-action {
        opacity: 0;
        transition: opacity 0.3s;
    }
}
</style>