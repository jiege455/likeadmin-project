<!--
    PC端中间广告图组件
    开发者：杰哥网络科技
    QQ: 2711793818 杰哥
-->
<template>
    <div class="widget-middle-banner" v-if="content?.enabled && content?.data?.length">
        <div class="banner-list">
            <template v-for="(item, index) in content.data" :key="index">
                <NuxtLink 
                    v-if="getLinkPath(item.link)" 
                    :to="getLinkPath(item.link)"
                    class="banner-item"
                >
                    <ElImage
                        class="banner-image"
                        :src="getImageUrl(item.image)"
                        fit="cover"
                    />
                </NuxtLink>
                <ElImage
                    v-else
                    class="banner-image"
                    :src="getImageUrl(item.image)"
                    fit="cover"
                />
            </template>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ElImage } from 'element-plus'
import { useAppStore } from '~~/stores/app'

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
</script>

<style lang="scss" scoped>
.widget-middle-banner {
    width: 100%;
    max-width: 1200px;
    margin: 20px auto;
    
    .banner-list {
        display: flex;
        gap: 15px;
    }
    
    .banner-item {
        flex: 1;
        display: block;
    }
    
    .banner-image {
        width: 100%;
        height: 120px;
        border-radius: 8px;
        cursor: pointer;
        transition: transform 0.3s;
        
        &:hover {
            transform: scale(1.02);
        }
    }
}
</style>
