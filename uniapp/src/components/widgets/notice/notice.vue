<!--
  开发者公众号：杰哥网络科技。
  qq2711793818 杰哥
  公告滚动组件
-->
<template>
    <view
        class="w-notice mx-[20rpx] mt-[20rpx]"
        :style="{ backgroundColor: styles.bg_color }"
        v-if="showList.length"
    >
        <view class="notice-icon">
            <text class="iconfont icon-gonggao" :style="{ color: styles.text_color }">公告</text>
        </view>
        <swiper class="notice-swiper" vertical autoplay circular :interval="3000" :duration="500">
            <swiper-item v-for="(item, index) in showList" :key="item.id">
                <view
                    class="notice-item"
                    :style="{ color: styles.text_color }"
                    @click="navTo(item.id)"
                >
                    <view class="flex items-center flex-1 min-w-0">
                        <view
                            v-if="item.type == 2"
                            class="text-xs px-[8rpx] py-[2rpx] rounded mr-[8rpx] flex-shrink-0"
                            :style="{ backgroundColor: styles.text_color, color: styles.bg_color }"
                        >
                            重要
                        </view>
                        <view
                            v-else-if="item.type == 3"
                            class="text-xs px-[8rpx] py-[2rpx] rounded mr-[8rpx] flex-shrink-0 bg-orange-500 text-white"
                        >
                            活动
                        </view>
                        <text class="truncate">{{ item.title }}</text>
                    </view>
                    <view class="flex-shrink-0 ml-[10rpx]">
                        <view
                            class="w-[12rpx] h-[12rpx] rounded-full bg-red-500"
                            v-if="!item.is_read"
                        ></view>
                    </view>
                </view>
            </swiper-item>
        </swiper>
        <view class="notice-more" @click="goMessage" :style="{ color: styles.text_color }">
            更多
        </view>
    </view>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps({
    content: {
        type: Object,
        default: () => ({})
    },
    styles: {
        type: Object,
        default: () => ({})
    },
    list: {
        type: Array,
        default: () => []
    }
})

const showList = computed(() => {
    if (!Array.isArray(props.list)) return []
    const limit = props.content.limit || 5
    return props.list.slice(0, limit)
})

const navTo = (id: number) => {
    uni.navigateTo({
        url: `/pages/notice_detail/notice_detail?id=${id}`
    })
}

const goMessage = () => {
    uni.navigateTo({
        url: '/pages/message/index'
    })
}
</script>

<style lang="scss" scoped>
.w-notice {
    display: flex;
    align-items: center;
    padding: 0 20rpx;
    height: 80rpx;
    border-radius: 14rpx;

    .notice-icon {
        margin-right: 20rpx;
        display: flex;
        align-items: center;
        flex-shrink: 0;
        .iconfont {
            font-size: 28rpx;
            font-weight: bold;
        }
    }

    .notice-swiper {
        flex: 1;
        height: 100%;
        min-width: 0;

        .notice-item {
            height: 100%;
            display: flex;
            align-items: center;
            font-size: 26rpx;
            overflow: hidden;
        }
    }

    .notice-more {
        flex-shrink: 0;
        font-size: 24rpx;
        margin-left: 16rpx;
        padding: 4rpx 12rpx;
        border-radius: 20rpx;
        background-color: rgba(255, 255, 255, 0.3);
    }
}
</style>
