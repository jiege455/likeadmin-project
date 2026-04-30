<!--
  开发者公众号：杰哥网络科技。
  qq2711793818 杰哥
-->
<template>
    <navigator :url="`/pages/news_detail/news_detail?id=${newsId}`">
        <view class="news-card bg-white mx-3 my-2 rounded-xl p-4 shadow-sm">
            <view class="flex justify-between items-center">
                <view class="flex-1 pr-3">
                    <view class="font-bold text-base line-clamp-1">{{ item.title }}</view>
                    <view class="flex items-center gap-2 mt-2 flex-wrap">
                        <view class="text-xs text-gray-400">{{ formatTime(item.create_time) }}</view>
                        <view
                            v-for="tag in item.tag_list"
                            :key="tag.id"
                            class="tag-item"
                        >
                            {{ tag.name }}
                        </view>
                    </view>
                </view>
                <view class="flex flex-col items-end">
                    <view v-if="item.is_buy" class="paid-tag"> 已购买 </view>
                    <view v-else-if="Number(item.price) > 0" class="price-tag">
                        <text class="text-xs">¥</text>
                        <text class="text-xl font-bold">{{ item.price }}</text>
                    </view>
                    <view v-else class="free-tag"> 免费 </view>
                    <view class="buy-btn mt-2" :class="btnClass">
                        {{ btnText }}
                    </view>
                </view>
            </view>
        </view>
    </navigator>
</template>

<script lang="ts" setup>
import { computed } from 'vue'

const props = withDefaults(
    defineProps<{
        item: any
        newsId: number
    }>(),
    {
        item: {},
        newsId: ''
    }
)

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

const btnText = computed(() => {
    if (props.item.is_buy) return '立即查看'
    if (Number(props.item.price) > 0) return '立即购买'
    return '立即查看'
})

const btnClass = computed(() => {
    if (props.item.is_buy) return 'buy-btn-purchased'
    if (Number(props.item.price) > 0) return 'buy-btn-paid'
    return ''
})
</script>

<style lang="scss" scoped>
.news-card {
    border: 1px solid #f0f0f0;
    transition: all 0.2s;
}

.news-card:active {
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
    color: #4073fa;
    background: #eef4ff;
}

.price-tag {
    color: #ff2d3a;
}

.free-tag {
    color: #52c41a;
    font-size: 14px;
    font-weight: bold;
}

.paid-tag {
    color: #1989fa;
    font-size: 14px;
    font-weight: bold;
}

.buy-btn {
    color: #fff;
    font-size: 12px;
    padding: 8rpx 16rpx;
    border-radius: 20rpx;
    background: #52c41a;
}

.buy-btn-paid {
    background: #ff2d3a;
}

.buy-btn-purchased {
    background: #1989fa;
}
</style>
