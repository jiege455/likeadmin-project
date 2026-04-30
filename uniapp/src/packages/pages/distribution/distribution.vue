<template>
    <uni-nav title="佣金明细" text-color="#ffffff"></uni-nav>

    <view class="commission-page min-h-screen bg-f5">
        <view
            class="header pt-4 pb-12 px-4"
            :style="{ backgroundColor: themeStore.primaryColor, color: '#ffffff' }"
        >
            <view class="text-sm opacity-90 mb-2">累计收益</view>
            <view class="text-4xl font-bold mb-6">{{ info.total_commission || '0.00' }}</view>

            <view class="flex items-center relative">
                <!-- 中间分割线 -->
                <view
                    class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-[1px] h-8 bg-white opacity-30"
                ></view>

                <view class="flex-1 text-center">
                    <view class="text-sm opacity-90 mb-1">今日收益</view>
                    <view class="text-xl font-bold">{{ info.today_commission || '0.00' }}</view>
                </view>
                <view class="flex-1 text-center">
                    <view class="text-sm opacity-90 mb-1">昨日收益</view>
                    <view class="text-xl font-bold">{{ info.yesterday_commission || '0.00' }}</view>
                </view>
            </view>
        </view>

        <!-- 内容区域：白色卡片，向上偏移覆盖头部 -->
        <view
            class="content-area bg-white -mt-6 rounded-t-xl min-h-[60vh] relative z-10 overflow-hidden"
        >
            <!-- Tabs -->
            <view class="flex border-b border-gray-100">
                <view
                    class="flex-1 h-12 flex items-center justify-center text-sm font-medium relative transition-colors"
                    :class="currentTab === 0 ? 'font-bold' : 'text-gray-500'"
                    :style="{ color: currentTab === 0 ? themeStore.primaryColor : '' }"
                    @click="currentTab = 0"
                >
                    收益记录
                    <view
                        v-if="currentTab === 0"
                        class="absolute bottom-0 w-8 h-[3px] rounded-full transition-all"
                        :style="{ backgroundColor: themeStore.primaryColor }"
                    ></view>
                </view>
                <view
                    class="flex-1 h-12 flex items-center justify-center text-sm font-medium relative transition-colors"
                    :class="currentTab === 1 ? 'font-bold' : 'text-gray-500'"
                    :style="{ color: currentTab === 1 ? themeStore.primaryColor : '' }"
                    @click="currentTab = 1"
                >
                    收益统计
                    <view
                        v-if="currentTab === 1"
                        class="absolute bottom-0 w-8 h-[3px] rounded-full transition-all"
                        :style="{ backgroundColor: themeStore.primaryColor }"
                    ></view>
                </view>
            </view>

            <!-- 列表内容 (Tab 0) -->
            <view class="h-full" v-if="currentTab === 0">
                <z-paging
                    ref="paging"
                    v-model="dataList"
                    @query="queryList"
                    :fixed="false"
                    :use-page-scroll="true"
                    :auto="true"
                    :default-page-size="15"
                    :empty-view-center="false"
                >
                    <template #top>
                        <view></view>
                    </template>

                    <view
                        v-for="(item, index) in dataList"
                        :key="index"
                        class="flex justify-between items-center p-4 border-b border-gray-50 active:bg-gray-50"
                    >
                        <view>
                            <view class="text-sm text-gray-800 mb-1 font-medium">{{
                                item.type_desc || '分销佣金'
                            }}</view>
                            <view class="text-xs text-gray-400">{{ item.create_time }}</view>
                        </view>
                        <view class="text-right">
                            <view
                                class="text-base font-bold"
                                :style="{ color: themeStore.primaryColor }"
                            >
                                +{{ item.money }}
                            </view>
                        </view>
                    </view>

                    <template #empty>
                        <view class="flex flex-col items-center justify-center pt-20">
                            <u-empty
                                mode="data"
                                icon="http://cdn.uviewui.com/uview/empty/data.png"
                                text="暂无收益记录"
                            ></u-empty>
                        </view>
                    </template>
                </z-paging>
            </view>

            <!-- 统计内容 (Tab 1) -->
            <view
                class="flex flex-col items-center justify-center pt-20 text-gray-400"
                v-if="currentTab === 1"
            >
                <u-empty
                    mode="data"
                    text="暂无统计数据"
                    icon="http://cdn.uviewui.com/uview/empty/data.png"
                ></u-empty>
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { getDistributionIndex, getDistributionCommissionLog } from '@/api/distribution'
import { onShow } from '@dcloudio/uni-app'
import { useThemeStore } from '@/stores/theme'
import { safeNavigateBack } from '@/utils/util'

const themeStore = useThemeStore()
const themeStyle = computed(() => {
    return `
        --color-primary: ${themeStore.primaryColor};
    `
})

const info = ref<any>({})
const currentTab = ref(0)
const dataList = ref<any[]>([])
const paging = ref(null)

const goBack = () => {
    safeNavigateBack({ defaultUrl: '/pages/index/index' })
}

const getData = async () => {
    try {
        const res = await getDistributionIndex()
        info.value = res || {}
    } catch (e) {
        console.error(e)
    }
}

const queryList = async (pageNo: number, pageSize: number) => {
    try {
        const res = await getDistributionCommissionLog({
            page_no: pageNo,
            page_size: pageSize
        })
        const list = Array.isArray(res) ? res : res.lists || []
        paging.value.complete(list)
    } catch (e) {
        paging.value.complete(false)
    }
}

onShow(() => {
    uni.setNavigationBarTitle({ title: '佣金明细' })
    getData()
})
</script>

<style lang="scss" scoped>
.commission-page {
    background-color: #f5f5f5;
}
.bg-f5 {
    background-color: #f5f5f5;
}
</style>
