<template>
<!--
  开发者公众号：杰哥网络科技
  QQ: 2711793818 杰哥
  私聊消息管理
-->
<div class="chat-private-lists">
    <el-card class="!border-none" shadow="never">
        <el-form :inline="true" :model="queryParams" class="mb-4">
            <el-form-item label="用户ID">
                <el-input v-model="queryParams.user_id" placeholder="请输入用户ID" clearable @keyup.enter="resetPage" />
            </el-form-item>
            <el-form-item label="对方ID">
                <el-input v-model="queryParams.target_id" placeholder="请输入对方ID" clearable @keyup.enter="resetPage" />
            </el-form-item>
            <el-form-item label="消息内容">
                <el-input v-model="queryParams.content" placeholder="请输入消息内容" clearable @keyup.enter="resetPage" />
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="resetPage">查询</el-button>
                <el-button @click="resetQuery">重置</el-button>
            </el-form-item>
        </el-form>
        <el-table size="large" v-loading="pager.loading" :data="pager.lists">
            <el-table-column label="ID" prop="id" width="80" />
            <el-table-column label="会话ID" prop="room_id" width="160" show-overflow-tooltip>
                <template #default="{ row }">
                    <el-tooltip :content="row.room_id" placement="top">
                        <span class="text-blue-500 cursor-pointer">{{ row.room_id }}</span>
                    </el-tooltip>
                </template>
            </el-table-column>
            <el-table-column label="发送者" min-width="140">
                <template #default="{ row }">
                    <div class="flex items-center">
                        <el-avatar :size="32" :src="row.avatar" class="mr-2" />
                        <div>
                            <div>{{ row.nickname || '未知用户' }}</div>
                            <div class="text-gray-400 text-xs">ID:{{ row.user_id }}</div>
                        </div>
                    </div>
                </template>
            </el-table-column>
            <el-table-column label="消息内容" prop="content" min-width="200" show-overflow-tooltip>
                <template #default="{ row }">
                    <span v-if="row.msg_type === 2">[图片]</span>
                    <span v-else>{{ row.content }}</span>
                </template>
            </el-table-column>
            <el-table-column label="消息类型" width="80">
                <template #default="{ row }">
                    <el-tag :type="row.msg_type === 1 ? '' : 'warning'" size="small">
                        {{ row.msg_type_text }}
                    </el-tag>
                </template>
            </el-table-column>
            <el-table-column label="发送时间" prop="create_time" width="160" />
            <el-table-column label="操作" width="80" fixed="right">
                <template #default="{ row }">
                    <el-button type="danger" link @click="handleDelete(row.id)">删除</el-button>
                </template>
            </el-table-column>
        </el-table>
        <div class="flex justify-end mt-4">
            <pagination v-model="pager" @change="getLists" />
        </div>
    </el-card>
</div>
</template>

<script setup lang="ts">
import { chatPrivateLists, deleteChatRecord } from '@/api/chat'
import { usePaging } from '@/hooks/usePaging'
import feedback from '@/utils/feedback'

const queryParams = reactive({
    user_id: '',
    target_id: '',
    content: ''
})

const { pager, getLists, resetPage, resetQuery } = usePaging({
    fetchFun: chatPrivateLists,
    params: queryParams
})

const handleDelete = async (id: number) => {
    await feedback.confirm('确定要删除该消息吗？')
    await deleteChatRecord({ id })
    feedback.msgSuccess('删除成功')
    getLists()
}

getLists()
</script>
