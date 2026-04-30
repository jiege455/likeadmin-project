<template>
    <div class="coupon-lists">
        <el-card class="!border-none" shadow="never">
            <el-form class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item label="优惠券名称">
                    <el-input v-model="queryParams.name" class="w-[280px]" clearable @keyup.enter="resetPage" />
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                </el-form-item>
            </el-form>
        </el-card>

        <el-card class="!border-none mt-4" shadow="never">
            <div class="mb-4">
                <el-button type="primary" @click="handleAdd">新增优惠券</el-button>
            </div>
            
            <el-table :data="pager.lists" v-loading="pager.loading">
                <el-table-column label="ID" prop="id" width="80" />
                <el-table-column label="商户" prop="merchant_name" min-width="100">
                    <template #default="{ row }">
                        <el-tag v-if="row.merchant_name == '平台'" type="primary">平台</el-tag>
                        <span v-else>{{ row.merchant_name }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="名称" prop="name" min-width="120" />
                <el-table-column label="面额" prop="money">
                    <template #default="{ row }">
                        <span class="text-red-500">¥{{ row.money }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="使用门槛" prop="condition_money">
                    <template #default="{ row }">
                        满{{ row.condition_money }}元可用
                    </template>
                </el-table-column>
                <el-table-column label="发放总量" prop="total_count" />
                <el-table-column label="已领取" prop="send_count" />
                <el-table-column label="有效期" prop="use_time_desc" min-width="200" />
                <el-table-column label="状态" width="100">
                    <template #default="{ row }">
                        <el-tag v-if="row.status == 1" type="success">正常</el-tag>
                        <el-tag v-else type="danger">停用</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="创建时间" prop="create_time" width="180" />
                <el-table-column label="操作" width="120" fixed="right">
                    <template #default="{ row }">
                        <el-button type="primary" link @click="handleEdit(row)">编辑</el-button>
                        <el-button type="danger" link @click="handleDel(row.id)">删除</el-button>
                    </template>
                </el-table-column>
            </el-table>
            
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>

        <edit-popup v-if="showEdit" ref="editRef" @success="getLists" @close="showEdit = false" />
    </div>
</template>

<script lang="ts" setup>
import { ref, reactive, nextTick } from 'vue'
import { couponLists, couponDel } from '@/api/marketing/coupon'
import { usePaging } from '@/hooks/usePaging'
import EditPopup from './edit.vue'
import feedback from '@/utils/feedback'

const queryParams = reactive({
    name: ''
})

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: couponLists,
    params: queryParams
})

const showEdit = ref(false)
const editRef = ref()

const handleAdd = () => {
    showEdit.value = true
    nextTick(() => {
        editRef.value?.open('add')
    })
}

const handleEdit = (data: any) => {
    showEdit.value = true
    nextTick(() => {
        editRef.value?.open('edit', data)
    })
}

const handleDel = async (id: number) => {
    await feedback.confirm('确定要删除吗？')
    await couponDel({ id })
    feedback.msgSuccess('删除成功')
    getLists()
}

getLists()
</script>
