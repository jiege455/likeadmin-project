<!--
    PC端导航菜单组件
    开发者：杰哥网络科技
    QQ: 2711793818 杰哥
-->
<template>
    <div class="widget-nav" v-if="content?.enabled && content?.data?.length">
        <div class="nav-container">
            <div 
                v-for="(item, index) in content.data" 
                :key="index"
                class="nav-item"
                @click="handleClick(item.link)"
            >
                <ElImage
                    class="nav-icon"
                    :src="getImageUrl(item.image)"
                    fit="contain"
                />
                <span class="nav-name">{{ item.name }}</span>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ElImage } from 'element-plus'
import { useAppStore } from '~~/stores/app'
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

const appStore = useAppStore()
const getImageUrl = appStore.getImageUrl

// 转换移动端链接为PC端链接
const getLinkPath = (link: any) => {
    if (!link?.path) return ''
    
    const path = link.path
    
    // 文章详情页转换
    if (path.includes('/pages/news_detail/news_detail') && link.query?.id) {
        return `/information/detail/${link.query.id}`
    }
    
    // 资讯列表页转换
    if (path.includes('/pages/news/news')) {
        return '/information'
    }
    
    // 其他移动端页面，PC端不支持则返回空
    if (path.startsWith('/pages/')) {
        return ''
    }
    
    return path
}

const handleClick = (link: any) => {
    const path = getLinkPath(link)
    if (path) {
        navigateTo(path)
    }
}
</script>

<style lang="scss" scoped>
.widget-nav {
    width: 100%;
    max-width: 1200px;
    margin: 20px auto;
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
    
    .nav-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: flex-start;
    }
    
    .nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: calc(100% / 8 - 20px);
        min-width: 80px;
        padding: 15px 10px;
        cursor: pointer;
        border-radius: 8px;
        transition: all 0.3s;
        
        &:hover {
            background: #f5f7fa;
            transform: translateY(-2px);
        }
    }
    
    .nav-icon {
        width: 48px;
        height: 48px;
        margin-bottom: 8px;
    }
    
    .nav-name {
        font-size: 14px;
        color: #333;
        text-align: center;
    }
}
</style>
