<template>
    <uni-nav title="系列列表" text-color="#ffffff"></uni-nav>

    <view class="series-list min-h-screen bg-f5">
        <view class="list p-3">
            <view
                class="item bg-white rounded-xl mb-3 overflow-hidden"
                v-for="(item, index) in list"
                :key="index"
                @click="toDetail(item)"
            >
                <image
                    :src="item.image || '/static/images/default.png'"
                    mode="aspectFill"
                    class="w-full h-40"
                />
                <view class="p-3">
                    <view class="font-bold text-base">{{ item.name }}</view>
                    <view class="flex items-center mt-2">
                        <view
                            class="text-xs px-2 py-1 rounded"
                            :style="{
                                backgroundColor: themeStore.primaryColor + '20',
                                color: themeStore.primaryColor
                            }"
                        >
                            {{ getLotteryTypeText(item.lottery_type) }}
                        </view>
                        <view class="text-red-500 font-bold ml-2">¥{{ item.series_price }}</view>
                    </view>
                    <view class="text-xs text-gray-400 mt-1">{{ item.total_issues }}期</view>
                </view>
            </view>
            <u-empty v-if="list.length === 0" mode="list" text="暂无系列"></u-empty>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import request from '@/utils/request'
import { onShow } from '@dcloudio/uni-app'
import { useThemeStore } from '@/stores/theme'
import { safeNavigateBack } from '@/utils/util'

const themeStore = useThemeStore()
const themeStyle = computed(() => `--color-primary: ${themeStore.primaryColor};`)

const list = ref<any[]>([])

const getLotteryTypeText = (type: string) => {
    const texts: Record<string, string> = {
        fc3d: '福彩3D',
        pl3: '排列三',
        ssq: '双色球',
        dlt: '大乐透'
    }
    return texts[type] || type
}

const getList = async () => {
    try {
        const res = await request.get({ url: '/series/lists' })
        list.value = res.lists || []
    } catch (e) {
        list.value = []
    }
}

const toDetail = (item: any) => {
    uni.navigateTo({ url: `/packages/pages/series/detail?id=${item.id}` })
}

const goBack = () => {
    safeNavigateBack({ defaultUrl: '/pages/index/index' })
}

onShow(() => {
    uni.setNavigationBarTitle({ title: '系列列表' })
    getList()
    themeStore.getTheme()
})
</script>

<style lang="scss" scoped>
.series-list {
    background-color: #f5f5f5;
}
.bg-f5 {
    background-color: #f5f5f5;
}
</style>
