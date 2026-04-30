<!--
    PC端搜索组件
    开发者：杰哥网络科技
    QQ: 2711793818 杰哥
-->
<template>
    <div class="widget-search">
        <div class="search-container">
            <ElInput
                v-model="searchKeyword"
                placeholder="请输入关键词搜索"
                size="large"
                clearable
                @keyup.enter="handleSearch"
            >
                <template #append>
                    <ElButton type="primary" @click="handleSearch">
                        <ElIcon><Search /></ElIcon>
                        搜索
                    </ElButton>
                </template>
            </ElInput>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ElInput, ElButton, ElIcon } from 'element-plus'
import { Search } from '@element-plus/icons-vue'
import { navigateTo } from '#imports'

const props = defineProps({
    content: {
        type: Object,
        default: () => ({})
    },
    styles: {
        type: Object,
        default: () => ({})
    }
})

const searchKeyword = ref('')

const handleSearch = () => {
    if (searchKeyword.value.trim()) {
        navigateTo(`/information?keyword=${encodeURIComponent(searchKeyword.value.trim())}`)
    }
}
</script>

<style lang="scss" scoped>
.widget-search {
    width: 100%;
    max-width: 1200px;
    margin: 20px auto;
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
    
    .search-container {
        max-width: 600px;
        margin: 0 auto;
    }
    
    :deep(.el-input-group__append) {
        background: var(--el-color-primary);
        border-color: var(--el-color-primary);
        
        .el-button {
            color: #fff;
        }
    }
}
</style>
