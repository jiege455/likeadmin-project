<template>
    <view class="article-list-widget" :style="{ backgroundColor: styles.background_color }">
        <view class="header" v-if="content.title">
            <text class="title" :style="{ color: styles.title_color }">{{ content.title }}</text>
            <text class="more" @click="goList" :style="{ color: styles.more_color || '#999' }"
                >更多 ></text
            >
        </view>
        <view class="list">
            <view
                class="item-card"
                v-for="(item, index) in list"
                :key="index"
                @click="handleClick(item)"
                :style="{ backgroundColor: styles.item_bg_color || '#fff' }"
            >
                <!-- 左上角角标 -->
                <view class="corner-tag">
                    <text class="tag-text">{{ item.cate_name || '公开' }}</text>
                </view>

                <view class="content-wrapper">
                    <!-- 标题 -->
                    <view class="item-title u-line-2" :style="{ color: styles.text_color }">{{
                        item.title
                    }}</view>

                    <!-- 时间 -->
                    <view class="item-time" :style="{ color: styles.desc_color }">{{
                        item.create_time
                    }}</view>
                </view>

                <!-- 右侧价格区域 -->
                <view class="right-section" v-if="content.showPrice">
                    <view class="price-box">
                        <text class="price-val">{{ item.price > 0 ? item.price : '免费' }}</text>
                        <text class="price-unit" v-if="item.price > 0">元</text>
                    </view>
                    <view class="refund-tag" v-if="item.price > 0">
                        <text>不中包退</text>
                    </view>
                </view>
            </view>
        </view>
        <view v-if="loading" class="loading">加载中...</view>
    </view>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import request from '@/utils/request'

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

const list = ref<any[]>([])
const loading = ref(false)

const getList = async () => {
    loading.value = true
    try {
        const res = await request.get({
            url: '/article/lists',
            data: {
                page_no: 1,
                page_size: props.content.limit || 5
            }
        })
        list.value = res.lists || []
    } catch (e) {
        console.error(e)
        list.value = [
            {
                id: 1,
                title: '演示文章：如何使用DIY组件',
                price: '9.99',
                create_time: '2023-05-20',
                image: '',
                cate_name: '公开'
            },
            {
                id: 2,
                title: '演示文章：商家入驻流程详解',
                price: '0.00',
                create_time: '2023-05-21',
                image: '',
                cate_name: 'VIP'
            }
        ]
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    getList()
})

const handleClick = (item: any) => {
    uni.navigateTo({ url: `/pages/news_detail/news_detail?id=${item.id}` })
}

const goList = () => {
    uni.navigateTo({ url: '/pages/news/news' })
}
</script>

<style scoped lang="scss">
.article-list-widget {
    // background-color: #f8f8f8;
    padding: 20rpx;
}
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20rpx;
    padding: 0 10rpx;
}
.header .title {
    font-size: 32rpx;
    font-weight: bold;
    // color: #333;
}
.header .more {
    font-size: 24rpx;
    // color: #999;
}

.item-card {
    position: relative;
    // background-color: #fff;
    border-radius: 16rpx;
    padding: 30rpx 20rpx 30rpx 24rpx;
    margin-bottom: 20rpx;
    box-shadow: 0 2rpx 12rpx rgba(0, 0, 0, 0.05);
    display: flex;
    justify-content: space-between;
    overflow: hidden;
}

.corner-tag {
    position: absolute;
    top: 0;
    left: 0;
    background: linear-gradient(135deg, #f7ce46 0%, #f1b32b 100%);
    color: #fff;
    font-size: 20rpx;
    padding: 4rpx 12rpx;
    border-bottom-right-radius: 12rpx;
    z-index: 1;
}

.content-wrapper {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding-right: 20rpx;
    padding-top: 10rpx;
}

.item-title {
    font-size: 30rpx;
    font-weight: bold;
    color: #333;
    line-height: 1.5;
    margin-bottom: 16rpx;
}

.item-time {
    font-size: 24rpx;
    color: #999;
}

.right-section {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: flex-end;
    min-width: 140rpx;
    border-left: 1rpx dashed #eee;
    padding-left: 20rpx;
}

.price-box {
    color: #ff4444;
    font-weight: bold;
    margin-bottom: 12rpx;
}

.price-val {
    font-size: 36rpx;
}

.price-unit {
    font-size: 24rpx;
    margin-left: 4rpx;
}

.refund-tag {
    background-color: #fff8e1;
    color: #bfa15f;
    font-size: 20rpx;
    padding: 4rpx 10rpx;
    border-radius: 6rpx;
    border: 1rpx solid #f1dcb0;
}

.loading {
    text-align: center;
    color: #999;
    padding: 20rpx;
    font-size: 24rpx;
}
</style>
