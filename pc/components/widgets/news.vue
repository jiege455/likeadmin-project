<!--
    PC端资讯列表组件
    开发者：杰哥网络科技
    QQ: 2711793818 杰哥
-->
<template>
    <div class="widget-news" v-if="articles?.length">
        <div class="news-header">
            <h3 class="news-title">{{ content?.title || '资讯推荐' }}</h3>
            <NuxtLink to="/information" class="view-more">
                查看更多
                <ElIcon><ArrowRight /></ElIcon>
            </NuxtLink>
        </div>
        <div class="news-grid">
            <NuxtLink 
                v-for="(item, index) in articles" 
                :key="index"
                :to="`/information/detail/${item.id}`"
                class="news-item"
            >
                <ElImage
                    v-if="item.image"
                    class="news-image"
                    :src="getImageUrl(item.image)"
                    fit="cover"
                />
                <div class="news-content">
                    <h4 class="news-item-title">{{ item.title }}</h4>
                    <p class="news-desc" v-if="item.desc">{{ item.desc }}</p>
                    <div class="news-meta">
                        <span v-if="item.author">{{ item.author }}</span>
                        <span>{{ item.click }} 阅读</span>
                    </div>
                </div>
            </NuxtLink>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ElImage, ElIcon } from 'element-plus'
import { ArrowRight } from '@element-plus/icons-vue'
import { useAppStore } from '~~/stores/app'

const props = defineProps({
    content: {
        type: Object,
        default: () => ({})
    },
    styles: {
        type: Object,
        default: () => ({})
    },
    articles: {
        type: Array,
        default: () => []
    }
})

const appStore = useAppStore()
const getImageUrl = appStore.getImageUrl
</script>

<style lang="scss" scoped>
.widget-news {
    width: 100%;
    max-width: 1200px;
    margin: 20px auto;
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
    
    .news-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .news-title {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin: 0;
        
        &::before {
            content: '';
            display: inline-block;
            width: 4px;
            height: 18px;
            background: var(--el-color-primary);
            margin-right: 10px;
            vertical-align: middle;
            border-radius: 2px;
        }
    }
    
    .view-more {
        display: flex;
        align-items: center;
        color: #666;
        font-size: 14px;
        text-decoration: none;
        transition: color 0.3s;
        
        &:hover {
            color: var(--el-color-primary);
        }
    }
    
    .news-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
    
    .news-item {
        display: block;
        background: #f8f9fa;
        border-radius: 8px;
        overflow: hidden;
        text-decoration: none;
        transition: all 0.3s;
        
        &:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }
    }
    
    .news-image {
        width: 100%;
        height: 160px;
    }
    
    .news-content {
        padding: 15px;
    }
    
    .news-item-title {
        font-size: 15px;
        font-weight: 500;
        color: #333;
        margin: 0 0 8px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .news-desc {
        font-size: 13px;
        color: #666;
        margin: 0 0 10px;
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .news-meta {
        display: flex;
        gap: 15px;
        font-size: 12px;
        color: #999;
    }
}

@media (max-width: 992px) {
    .widget-news .news-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 576px) {
    .widget-news .news-grid {
        grid-template-columns: 1fr;
    }
}
</style>
