<template>
    <uni-nav title="资金明细"></uni-nav>

    <view class="account-log-page min-h-screen bg-f5">
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
                class="flex justify-between items-center p-4 border-b border-gray-50 bg-white active:bg-gray-50"
            >
                <view>
                    <view class="text-sm text-gray-800 mb-1 font-medium">{{
                        item.type_desc || '余额变动'
                    }}</view>
                    <view class="text-xs text-gray-400">{{ item.create_time }}</view>
                </view>
                <view class="text-right">
                    <view
                        class="text-base font-bold"
                        :class="item.action === 1 ? 'text-primary' : 'text-red'"
                    >
                        {{
                            item.change_amount_desc ||
                            (item.action === 1 ? '+' : '-') + item.change_amount
                        }}
                    </view>
                </view>
            </view>

            <template #empty>
                <view class="flex flex-col items-center justify-center pt-20">
                    <u-empty
                        mode="data"
                        icon="http://cdn.uviewui.com/uview/empty/data.png"
                        text="暂无资金明细"
                    ></u-empty>
                </view>
            </template>
        </z-paging>
    </view>
</template>

<script lang="ts" setup>
import { ref, shallowRef } from 'vue'
import { accountLog } from '@/api/user'
import { safeNavigateBack } from '@/utils/util'

const paging = shallowRef()
const dataList = ref<any[]>([])

const queryList = async (pageNo: number, pageSize: number) => {
    try {
        const data = await accountLog({
            page_no: pageNo,
            page_size: pageSize
        })
        const list = Array.isArray(data) ? data : data.lists || []
        paging.value.complete(list)
    } catch (error) {
        paging.value.complete(false)
    }
}

const goBack = () => {
    safeNavigateBack({ defaultUrl: '/pages/index/index' })
}
</script>

<style lang="scss" scoped>
.account-log-page {
    background-color: #f5f5f5;
}
.bg-f5 {
    background-color: #f5f5f5;
}
</style>
