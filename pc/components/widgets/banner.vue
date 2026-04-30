<!--
    PC端轮播图组件
    开发者：杰哥网络科技
    QQ: 2711793818 杰哥
-->
<template>
    <div class="widget-banner" v-if="content?.enabled && content?.data?.length">
        <ElCarousel
            trigger="click"
            :height="height"
            :interval="7000"
            indicator-position="outside"
        >
            <ElCarouselItem v-for="(item, index) in content.data" :key="index">
                <NuxtLink 
                    v-if="getLinkPath(item.link)" 
                    :to="getLinkPath(item.link)" 
                    target="_blank"
                    class="block w-full h-full"
                >
                    <ElImage
                        class="w-full h-full rounded-lg overflow-hidden"
                        :src="getImageUrl(item.image)"
                        fit="cover"
                    />
                </NuxtLink>
                <ElImage
                    v-else
                    class="w-full h-full rounded-lg overflow-hidden cursor-pointer"
                    :src="getImageUrl(item.image)"
                    fit="cover"
                />
            </ElCarouselItem>
        </ElCarousel>
    </div>
</template>

<script setup lang="ts">
import { ElCarousel, ElCarouselItem, ElImage } from 'element-plus'
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

const height = computed(() => {
    return props.styles?.height || '400px'
})

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
.widget-banner {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    
    :deep(.el-carousel__item) {
        border-radius: 8px;
        overflow: hidden;
    }
}
</style>
