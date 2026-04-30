<template>
    <view class="w-merchant-content-list bg-white" :style="styles">
        <z-paging
            ref="paging"
            v-model="dataList"
            @query="queryList"
            :fixed="false"
            :use-page-scroll="true"
            :auto="false"
        >
            <view class="p-3">
                <view
                    class="font-bold text-lg mb-3 border-l-4 pl-2"
                    :style="{ borderColor: primaryColor }"
                    >全部内容</view
                >

                <view
                    v-for="(item, index) in dataList"
                    :key="index"
                    class="content-item bg-white rounded-xl p-3 mb-2 shadow-sm"
                    @click="goDetail(item.id)"
                >
                    <view class="flex justify-between items-start">
                        <view class="flex-1 pr-2 overflow-hidden">
                            <view class="font-bold text-base line-clamp-2">{{
                                item.title
                            }}</view>
                            <view class="flex items-center gap-1 mt-2 flex-wrap">
                                <view class="text-xs text-gray-400">{{ formatTime(item.create_time) }}</view>
                                <view
                                    v-for="tag in item.tag_list"
                                    :key="tag.id"
                                    class="tag-item"
                                    :style="{
                                        color: primaryColor,
                                        backgroundColor: primaryColor + '15'
                                    }"
                                >
                                    {{ tag.name }}
                                </view>
                            </view>
                        </view>
                        <view class="shrink-0 ml-2">
                            <view v-if="Number(item.price) > 0" class="price-tag">
                                <text class="text-xs">¥</text>
                                <text class="text-base font-bold">{{ item.price }}</text>
                            </view>
                            <view v-else class="free-tag">免费</view>
                        </view>
                    </view>
                </view>
            </view>
        </z-paging>
    </view>
</template>

<script setup lang="ts">
import { ref, watch, nextTick, computed } from 'vue'
import { getArticleList } from '@/api/news'
import { useThemeStore } from '@/stores/theme'

const props = defineProps<{
    content: any
    styles: any
    merchantId: any
    keyword: string
    filterParams: any
}>()

const themeStore = useThemeStore()
const primaryColor = computed(() => themeStore.primaryColor || '#FF2D3A')

const formatTime = (time: string) => {
    if (!time) return ''
    const parts = time.split(' ')
    if (parts.length > 1) {
        const date = parts[0]
        const timePart = parts[1]
        const timeWithoutSeconds = timePart.substring(0, 5)
        return date + ' ' + timeWithoutSeconds
    }
    return time
}

const paging = ref(null)
const dataList = ref([])
const currentKeyword = ref('')
const currentFilter = ref({
    type: '',
    price: ''
})

const queryList = async (pageNo: number, pageSize: number) => {
    if (!props.merchantId) {
        paging.value?.complete([])
        return
    }

    try {
        const params: any = {
            page_no: pageNo,
            page_size: pageSize,
            merchant_id: props.merchantId
        }

        if (currentKeyword.value) {
            params.keyword = currentKeyword.value
        }

        if (currentFilter.value.type) {
            params.price_type = currentFilter.value.type
        }

        if (currentFilter.value.price) {
            params.price_range = currentFilter.value.price
        }

        const res = await getArticleList(params)
        paging.value?.complete(res.lists || [])
    } catch (e) {
        paging.value?.complete(false)
    }
}

const goDetail = (id: number) => {
    uni.navigateTo({
        url: `/pages/news_detail/news_detail?id=${id}`
    })
}

const setKeyword = (keyword: string) => {
    currentKeyword.value = keyword
    nextTick(() => {
        paging.value?.reload()
    })
}

const setFilter = (filter: any) => {
    currentFilter.value = { ...filter }
    nextTick(() => {
        paging.value?.reload()
    })
}

watch(
    () => props.merchantId,
    (val) => {
        if (val) {
            nextTick(() => {
                paging.value?.reload()
            })
        }
    },
    { immediate: true }
)

watch(
    () => props.filterParams,
    (val) => {
        if (val) {
            currentFilter.value = { ...val }
            nextTick(() => {
                paging.value?.reload()
            })
        }
    },
    { deep: true }
)

defineExpose({
    setKeyword,
    setFilter
})
</script>

<style scoped>
.w-merchant-content-list {
    min-height: 200rpx;
}

.content-item {
    border: 1px solid #f0f0f0;
    transition: all 0.2s;
}

.content-item:active {
    transform: scale(0.98);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.tag-item {
    font-size: 10px;
    padding: 2rpx 8rpx;
    border-radius: 16rpx;
}

.price-tag {
    color: #ff2d3a;
}

.free-tag {
    color: #52c41a;
    font-size: 12px;
    font-weight: bold;
}
</style>
