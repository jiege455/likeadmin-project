<template>
    <view class="discovery-page min-h-screen bg-gray-50 flex flex-col">
        <!-- 顶部搜索栏 -->
        <view class="search-box bg-white px-3 py-2 sticky top-0 z-50">
            <u-search
                placeholder="搜索感兴趣的内容"
                :show-action="false"
                @click="goSearch"
                disabled
            ></u-search>
        </view>

        <!-- 来源筛选 Tabs（全部/平台/商户） -->
        <view class="source-tabs-box bg-white border-b border-gray-100 px-3 py-2">
            <view class="source-tabs flex justify-around">
                <view
                    v-for="(source, index) in sourceList"
                    :key="index"
                    class="source-tab text-sm py-2 px-4 rounded-full"
                    :class="currentSource === index ? 'source-tab-active' : ''"
                    :style="
                        currentSource === index
                            ? { backgroundColor: themeStore.primaryColor, color: '#fff' }
                            : {}
                    "
                    @click="changeSource(index)"
                >
                    {{ source.name }}
                </view>
            </view>
        </view>

        <!-- 分类 Tabs -->
        <view class="tabs-box bg-white border-b border-gray-100">
            <u-tabs
                :list="categoryList"
                :current="currentTab"
                @change="changeTab"
                :is-scroll="true"
                active-color="#2979ff"
            ></u-tabs>
        </view>

        <!-- 文章列表 -->
        <view class="flex-1 min-h-0">
            <z-paging ref="paging" v-model="dataList" @query="queryList" :fixed="false">
                <view class="p-2">
                    <view
                        v-for="(item, index) in dataList"
                        :key="index"
                        class="mb-2 bg-white rounded-lg p-3 flex"
                        @click="goDetail(item)"
                    >
                        <!-- 左侧图片 -->
                        <u-image
                            v-if="item.image"
                            :src="item.image"
                            width="220rpx"
                            height="160rpx"
                            border-radius="8"
                            class="mr-3 flex-shrink-0"
                        ></u-image>

                        <!-- 右侧内容 -->
                        <view class="flex-1 flex flex-col justify-between">
                            <view class="title text-base font-bold u-line-2">{{ item.title }}</view>

                            <view class="flex justify-between items-center mt-2">
                                <view class="flex items-center">
                                    <!-- 商户文章显示商户头像 -->
                                    <u-avatar
                                        v-if="item.merchant_id && item.merchant_image"
                                        :src="item.merchant_image"
                                        size="32"
                                        class="mr-1"
                                    ></u-avatar>
                                    <u-avatar
                                        v-else
                                        src="/static/default_avatar.png"
                                        size="32"
                                        class="mr-1"
                                    ></u-avatar>
                                    <text
                                        class="text-xs"
                                        :class="
                                            item.merchant_id ? 'text-gray-700' : 'text-gray-500'
                                        "
                                    >
                                        {{ item.merchant_name || '平台' }}
                                    </text>
                                </view>
                                <text class="text-xs text-gray-400">{{ item.click }}阅读</text>
                            </view>

                            <view class="flex justify-between items-center mt-1">
                                <text
                                    class="text-red-500 font-bold text-sm"
                                    v-if="Number(item.price) > 0"
                                    >¥{{ item.price }}</text
                                >
                                <text class="text-green-500 text-sm" v-else>免费</text>
                                <view class="flex items-center">
                                    <u-icon
                                        :name="item.collect ? 'star-fill' : 'star'"
                                        size="24"
                                        :color="item.collect ? '#FFC107' : '#999'"
                                    ></u-icon>
                                    <text class="text-xs text-gray-400 ml-1">{{
                                        item.collect ? '已收藏' : '收藏'
                                    }}</text>
                                </view>
                            </view>
                        </view>
                    </view>
                </view>
            </z-paging>
        </view>

        <tabbar />
    </view>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { getArticleList, getArticleCate } from '@/api/news'
import { useThemeStore } from '@/stores/theme'

const themeStore = useThemeStore()
const paging = ref(null)
const categoryList = ref([{ name: '全部', id: 0 }])
const currentTab = ref(0)
const dataList = ref([])
const currentSource = ref(0)
const sourceList = ref([
    { name: '全部', value: '' },
    { name: '平台文章', value: 0 },
    { name: '商户文章', value: 'merchant' }
])

// 获取分类
const getCategories = async () => {
    try {
        const res = await getArticleCate()
        if (res.lists) {
            categoryList.value = [
                { name: '全部', id: 0 },
                ...res.lists.map((item: any) => ({
                    name: item.name,
                    id: item.id
                }))
            ]
        }
    } catch (e) {
        console.error(e)
    }
}

// 获取列表数据
const queryList = async (pageNo: number, pageSize: number) => {
    try {
        const categoryId = categoryList.value[currentTab.value]?.id || ''
        const params: any = {
            page_no: pageNo,
            page_size: pageSize,
            cid: categoryId
        }

        // 根据来源筛选
        const sourceValue = sourceList.value[currentSource.value]?.value
        if (sourceValue === 0) {
            // 平台文章 merchant_id=0
            params.merchant_id = 0
        } else if (sourceValue === 'merchant') {
            // 商户文章 merchant_id>0，传 1 表示获取所有商户文章
            params.merchant_id = 1
            params.is_merchant = 1
        }

        const res = await getArticleList(params)
        paging.value.complete(res.lists)
    } catch (e) {
        paging.value.complete(false)
    }
}

const changeTab = (index: number) => {
    currentTab.value = index
    paging.value.reload()
}

const changeSource = (index: number) => {
    currentSource.value = index
    paging.value.reload()
}

const goSearch = () => {
    uni.navigateTo({ url: '/pages/search/search' })
}

const goDetail = (item: any) => {
    const merchantId = item.merchant_id || ''
    uni.navigateTo({
        url: `/pages/news_detail/news_detail?id=${item.id}&merchant_id=${merchantId}`
    })
}

onMounted(() => {
    getCategories()
})
</script>

<style scoped>
.discovery-page {
    height: 100vh;
    display: flex;
    flex-direction: column;
}

.source-tabs-box {
    display: flex;
    align-items: center;
}

.source-tabs {
    display: flex;
    gap: 12rpx;
}

.source-tab {
    transition: all 0.3s;
}

.source-tab-active {
    color: #fff;
}
</style>
