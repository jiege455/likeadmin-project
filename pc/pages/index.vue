<!--
    PC端首页
    开发者：杰哥网络科技
    QQ: 2711793818 杰哥
    统一装修系统：读取移动端装修数据，PC端自适应显示
-->
<template>
    <div class="index-page">
        <!-- 装修组件渲染 -->
        <template v-for="(item, index) in pageWidgets" :key="index">
            <!-- 搜索组件 -->
            <WidgetSearch 
                v-if="item.name === 'search'" 
                :content="item.content" 
                :styles="item.styles" 
            />
            
            <!-- 轮播图组件 -->
            <WidgetBanner 
                v-if="item.name === 'banner'" 
                :content="item.content" 
                :styles="item.styles" 
            />
            
            <!-- 导航菜单组件 -->
            <WidgetNav 
                v-if="item.name === 'nav'" 
                :content="item.content" 
                :styles="item.styles" 
            />
            
            <!-- 中间广告图组件 -->
            <WidgetMiddleBanner 
                v-if="item.name === 'middle-banner'" 
                :content="item.content" 
                :styles="item.styles" 
            />
            
            <!-- 资讯列表组件 -->
            <WidgetNews 
                v-if="item.name === 'news'" 
                :content="item.content" 
                :styles="item.styles"
                :articles="pageData.new"
            />
            
            <!-- 空白占位组件 -->
            <WidgetBlank 
                v-if="item.name === 'blank'" 
                :content="item.content" 
                :styles="item.styles" 
            />
            
            <!-- 分割线组件 -->
            <WidgetSeparateLine 
                v-if="item.name === 'separate-line'" 
                :content="item.content" 
                :styles="item.styles" 
            />
            
            <!-- 标题文字组件 -->
            <WidgetTitleText 
                v-if="item.name === 'title-text'" 
                :content="item.content" 
                :styles="item.styles" 
            />
        </template>
        
        <!-- 默认资讯区域（当没有装修数据时显示） -->
        <div v-if="!pageWidgets.length" class="default-content">
            <div class="flex">
                <div class="w-[750px] h-[340px] flex-none mr-5">
                    <ElCarousel
                        v-if="swiperData.enabled"
                        class="w-full"
                        trigger="click"
                        height="340px"
                    >
                        <ElCarouselItem v-for="item in showList" :key="item">
                            <NuxtLink :to="item.link?.path" target="_blank">
                                <ElImage
                                    class="w-full h-full rounded-[8px] bg-white overflow-hidden"
                                    :src="appStore.getImageUrl(item.image)"
                                    fit="contain"
                                />
                            </NuxtLink>
                        </ElCarouselItem>
                    </ElCarousel>
                </div>
                <InformationCard
                    link="/information/new"
                    class="flex-1 min-w-0"
                    header="最新资讯"
                    :data="pageData.new"
                    :show-time="false"
                />
            </div>
            <div class="mt-5 flex">
                <InformationCard
                    link="/information"
                    class="w-[750px] flex-none mr-5"
                    header="全部资讯"
                    :data="pageData.all"
                    :only-title="false"
                />
                <InformationCard
                    link="/information/hot"
                    class="flex-1"
                    header="热门资讯"
                    :data="pageData.hot"
                    :only-title="false"
                    image-size="mini"
                    :show-author="false"
                    :show-desc="false"
                    :show-click="false"
                    :border="false"
                    :title-line="2"
                />
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { ElCarousel, ElCarouselItem, ElImage } from 'element-plus'
import { getIndex } from '@/api/shop'
import { useAppStore } from '~~/stores/app'

// 导入PC端装修组件
import WidgetBanner from '~/components/widgets/banner.vue'
import WidgetNav from '~/components/widgets/nav.vue'
import WidgetSearch from '~/components/widgets/search.vue'
import WidgetNews from '~/components/widgets/news.vue'
import WidgetMiddleBanner from '~/components/widgets/middle-banner.vue'
import WidgetBlank from '~/components/widgets/blank.vue'
import WidgetSeparateLine from '~/components/widgets/separate-line.vue'
import WidgetTitleText from '~/components/widgets/title-text.vue'

const appStore = useAppStore()

// 获取首页数据（现在读取的是移动端装修数据 id=1）
const { data: pageData } = await useAsyncData(() => getIndex(), {
    default: () => ({
        all: [],
        hot: [],
        new: [],
        page: {}
    })
})

// 解析装修组件数据
const pageWidgets = computed(() => {
    try {
        const data = pageData.value.page?.data
        if (data) {
            const parsed = JSON.parse(data)
            // 过滤掉 pc-banner 组件（PC端使用移动端组件渲染）
            return parsed.filter((item: any) => item.name !== 'pc-banner')
        }
        return []
    } catch (error) {
        console.error('解析装修数据失败:', error)
        return []
    }
})

// 兼容旧的PC轮播图数据（当没有装修组件时使用）
const swiperData = computed(() => {
    try {
        const data = JSON.parse(pageData.value.page?.data || '[]')
        return data.find((item: any) => item.name === 'pc-banner')?.content || { enabled: false }
    } catch (error) {
        return { enabled: false }
    }
})

const showList = computed(() => {
    return swiperData.value?.data || []
})
</script>

<style lang="scss" scoped>
.index-page {
    min-height: calc(100vh - 200px);
    padding: 20px 0;
    background: #f5f7fa;
}

.default-content {
    max-width: 1200px;
    margin: 0 auto;
}
</style>
