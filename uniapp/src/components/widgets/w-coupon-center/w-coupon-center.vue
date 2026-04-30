<template>
    <view
        class="w-coupon-center"
        :style="{
            paddingTop: `${styles.padding_top}px`,
            paddingBottom: `${styles.padding_bottom}px`,
            backgroundColor: styles.bg_color
        }"
    >
        <u-tabs
            v-if="content.show_my_coupon"
            :list="tabs"
            :current="current"
            @change="changeTab"
            :active-color="content.theme_color || '#e1251b'"
            :is-scroll="false"
            bg-color="transparent"
        ></u-tabs>

        <view class="list p-3">
            <view
                class="coupon-item"
                v-for="(item, index) in list"
                :key="index"
                :style="{ backgroundColor: styles.item_bg_color || '#fff' }"
            >
                <!-- 左侧金额 -->
                <view
                    class="left"
                    :style="{
                        backgroundColor: styles.coupon_left_bg_color || '#fff5f5',
                        borderRightColor: styles.coupon_border_color || '#ffcccc'
                    }"
                >
                    <view class="price" :style="{ color: content.theme_color || '#e1251b' }">
                        <text class="symbol">¥</text>{{ item.money }}
                    </view>
                    <view class="condition" :style="{ color: content.theme_color || '#e1251b' }">
                        满{{ item.condition_money }}可用
                    </view>
                </view>

                <!-- 右侧信息 -->
                <view class="right">
                    <view class="info">
                        <view class="name" :style="{ color: styles.text_color || '#333' }">{{
                            item.name
                        }}</view>
                        <view class="time" :style="{ color: styles.desc_color || '#999' }">{{
                            item.use_time_desc
                        }}</view>
                    </view>

                    <view class="action">
                        <view
                            class="tag"
                            :style="{
                                color: content.theme_color || '#e1251b',
                                borderColor: content.theme_color || '#e1251b'
                            }"
                        >
                            {{ item.merchant_name || '平台' }}
                        </view>

                        <u-button
                            v-if="current === 0"
                            size="mini"
                            shape="circle"
                            type="primary"
                            :disabled="item.is_get == 1"
                            @click="handleGet(item.id)"
                            :custom-style="{
                                backgroundColor: item.is_get
                                    ? '#ccc'
                                    : content.theme_color || '#e1251b',
                                border: 'none',
                                color: '#fff'
                            }"
                        >
                            {{ item.is_get ? '已领取' : '立即领取' }}
                        </u-button>
                        <u-button
                            v-else
                            size="mini"
                            shape="circle"
                            disabled
                            :custom-style="{
                                backgroundColor: '#ccc',
                                color: '#fff',
                                border: 'none'
                            }"
                        >
                            {{ statusText }}
                        </u-button>
                    </view>
                </view>
            </view>

            <u-empty v-if="list.length === 0" mode="coupon" text="暂无优惠券"></u-empty>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import request from '@/utils/request'

const props = defineProps({
    content: {
        type: Object,
        default: () => ({
            show_my_coupon: 1,
            theme_color: '#e1251b'
        })
    },
    styles: {
        type: Object,
        default: () => ({})
    }
})

const tabs = ref([{ name: '领券中心' }, { name: '未使用' }, { name: '已使用' }, { name: '已过期' }])

const current = ref(0)
const list = ref<any[]>([])

const statusText = computed(() => {
    const map = ['立即领取', '去使用', '已使用', '已过期']
    return map[current.value]
})

const getList = async () => {
    let url = '/coupon/lists'
    let params = {}

    if (current.value > 0) {
        url = '/coupon/myList'
        params = { status: current.value - 1 }
    }

    try {
        const res = await request.get({ url, params })
        list.value = res.lists || res || []
    } catch (e) {
        console.error('获取优惠券列表失败:', e)
        list.value = []
    }
}

const changeTab = (index: number) => {
    current.value = index
    list.value = []
    getList()
}

const handleGet = async (id: number) => {
    try {
        await request.post({ url: '/coupon/receive', data: { coupon_id: id } })
        uni.showToast({ title: '领取成功' })
        getList()
    } catch (e: any) {
        uni.showToast({ title: e.msg || '领取失败', icon: 'none' })
    }
}

onMounted(() => {
    getList()
})
</script>

<style lang="scss" scoped>
.w-coupon-center {
    /* min-height: 100vh; */
    /* background-color: #f5f5f5; */
    /* padding: 20rpx; */
}

.coupon-item {
    display: flex;
    /* background-color: #fff; */
    border-radius: 16rpx;
    margin-bottom: 20rpx;
    overflow: hidden;
    box-shadow: 0 2rpx 10rpx rgba(0, 0, 0, 0.05);
    height: 180rpx;

    .left {
        width: 200rpx;
        /* background-color: #fff5f5; // 浅红背景 */
        /* color: #e1251b; */
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-right: 2rpx dashed; /* color set by style */

        .price {
            font-size: 48rpx;
            font-weight: bold;
            .symbol {
                font-size: 24rpx;
            }
        }
        .condition {
            font-size: 22rpx;
            margin-top: 10rpx;
        }
    }

    .right {
        flex: 1;
        padding: 20rpx;
        display: flex;
        flex-direction: column;
        justify-content: space-between;

        .name {
            font-size: 30rpx;
            font-weight: bold;
            /* color: #333; */
        }
        .time {
            font-size: 22rpx;
            /* color: #999; */
        }
        .action {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            .tag {
                font-size: 20rpx;
                padding: 2rpx 10rpx;
                border: 1rpx solid;
                border-radius: 4rpx;
            }
        }
    }
}
</style>
