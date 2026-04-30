<!--
    链接选择器 - 订单选择组件
    开发者：杰哥网络科技 qq2711793818 杰哥
-->
<template>
    <div class="order-list">
        <el-form ref="formRef" :model="queryParams" :inline="true">
            <el-form-item class="w-[280px]" label="订单号">
                <el-input
                    v-model="queryParams.order_sn"
                    placeholder="请输入订单号"
                    clearable
                    @keyup.enter="resetPage"
                >
                </el-input>
                <el-button type="primary" class="ml-4" :icon="Search" @click="resetPage" />
            </el-form-item>
        </el-form>

        <el-table
            size="large"
            v-loading="pager.loading"
            :data="pager.lists"
            height="432px"
            @row-click="handleSelectItem"
        >
            <el-table-column label="选择" min-width="50">
                <template #default="{ row }">
                    <div class="flex row-center">
                        <el-checkbox
                            :model-value="getSelectItem(row.id)"
                            size="large"
                            @change="handleSelectItem(row)"
                        ></el-checkbox>
                    </div>
                </template>
            </el-table-column>
            <el-table-column label="订单号" prop="order_sn" min-width="180" show-overflow-tooltip />
            <el-table-column label="用户" min-width="120">
                <template #default="{ row }">
                    <div class="flex items-center">
                        <el-avatar :size="28" :src="row.avatar">
                            <el-icon><User /></el-icon>
                        </el-avatar>
                        <span class="ml-2">{{ row.nickname || '-' }}</span>
                    </div>
                </template>
            </el-table-column>
            <el-table-column label="金额" width="100" align="right">
                <template #default="{ row }">
                    <span class="text-red-500 font-medium">¥{{ row.order_amount }}</span>
                </template>
            </el-table-column>
            <el-table-column label="状态" width="90" align="center">
                <template #default="{ row }">
                    <el-tag :type="row.pay_status === 1 ? 'success' : 'warning'" size="small">
                        {{ row.pay_status_text }}
                    </el-tag>
                </template>
            </el-table-column>
            <template #empty>
                <div class="flex flex-col items-center py-8">
                    <el-icon :size="48" color="#c0c4cc"><Document /></el-icon>
                    <span class="text-gray-400 mt-2">暂无订单数据</span>
                </div>
            </template>
        </el-table>

        <div class="flex justify-end mt-4">
            <pagination v-model="pager" @change="getLists" />
        </div>
    </div>
</template>

<script lang="ts" setup>
import { Document, Search, User } from '@element-plus/icons-vue'
import type { PropType } from 'vue'

import { orderLists } from '@/api/article/order'
import { usePaging } from '@/hooks/usePaging'

import { LinkTypeEnum } from '.'

const props = defineProps({
    modelValue: {
        type: Object as PropType<any>,
        default: () => ({})
    }
})
const emit = defineEmits<{
    (event: 'update:modelValue', value: any): void
}>()

const selectData = ref<any>({
    path: '/pages/order/order',
    name: '',
    query: {},
    type: LinkTypeEnum.ORDER_LIST
})

const queryParams = reactive<any>({
    order_sn: '',
    pay_status: 1
})

const { pager, getLists, resetPage } = usePaging({
    fetchFun: orderLists,
    params: queryParams
})

const getSelectItem = (id: number) => {
    return id == Number(selectData.value.id)
}

const handleSelectItem = (event: any) => {
    selectData.value = {
        id: event.id,
        name: `订单:${event.order_sn}`,
        path: '/pages/order/order',
        query: {
            id: event.id
        },
        type: LinkTypeEnum.ORDER_LIST
    }

    emit('update:modelValue', selectData.value)
}

watch(
    () => props.modelValue,
    (value) => {
        if (value.type != LinkTypeEnum.ORDER_LIST) {
            return (selectData.value = {
                id: '',
                name: '',
                path: '/pages/order/order',
                type: LinkTypeEnum.SHOP_PAGES
            })
        }
        selectData.value = value
    },
    {
        immediate: true
    }
)

getLists()
</script>

<style lang="scss" scoped>
:deep(.el-input-group__append) {
    .el-button {
        margin: 0 0;
    }
}
</style>
