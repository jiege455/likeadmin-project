<template>
<!--
  开发者公众号：杰哥网络科技
  QQ: 2711793818 杰哥
  公共聊天室消息管理
-->
<div class="chat-public-lists">
    <el-card class="!border-none" shadow="never">
        <el-form :inline="true" :model="queryParams" class="mb-4">
            <el-form-item label="聊天室">
                <el-select v-model="queryParams.room_id" placeholder="请选择聊天室" clearable>
                    <el-option v-for="room in roomList" :key="room.room_id" :label="room.name" :value="room.room_id" />
                </el-select>
            </el-form-item>
            <el-form-item label="用户昵称">
                <el-input v-model="queryParams.nickname" placeholder="请输入用户昵称" clearable @keyup.enter="resetPage" />
            </el-form-item>
            <el-form-item label="消息内容">
                <el-input v-model="queryParams.content" placeholder="请输入消息内容" clearable @keyup.enter="resetPage" />
            </el-form-item>
            <el-form-item label="消息类型">
                <el-select v-model="queryParams.msg_type" placeholder="请选择" clearable>
                    <el-option label="文字" :value="1" />
                    <el-option label="图片" :value="2" />
                    <el-option label="系统消息" :value="3" />
                </el-select>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="resetPage">查询</el-button>
                <el-button @click="resetQuery">重置</el-button>
            </el-form-item>
        </el-form>
        <div class="mb-4 flex gap-2">
            <el-button type="danger" @click="handleClear" :disabled="!queryParams.room_id">清空消息</el-button>
            <span class="text-gray-400 text-sm leading-8" v-if="queryParams.room_id">
                将清空「{{ getRoomName(queryParams.room_id) }}」的所有消息
            </span>
        </div>
        <el-table size="large" v-loading="pager.loading" :data="pager.lists">
            <el-table-column label="ID" prop="id" width="80" />
            <el-table-column label="聊天室" width="120">
                <template #default="{ row }">
                    <el-tag type="primary" size="small">{{ getRoomName(row.room_id) }}</el-tag>
                </template>
            </el-table-column>
            <el-table-column label="发送者" min-width="150">
                <template #default="{ row }">
                    <div class="flex items-center">
                        <el-avatar :size="32" :src="row.avatar" class="mr-2" />
                        <span>{{ row.nickname || '未知用户' }}</span>
                        <span class="text-gray-400 text-xs ml-2">(ID:{{ row.user_id }})</span>
                    </div>
                </template>
            </el-table-column>
            <el-table-column label="消息内容" prop="content" min-width="250" show-overflow-tooltip>
                <template #default="{ row }">
                    <span v-if="row.msg_type === 2">[图片]</span>
                    <span v-else-if="row.msg_type === 3" class="text-blue-500">[系统消息]</span>
                    <span v-else>{{ row.content }}</span>
                </template>
            </el-table-column>
            <el-table-column label="消息类型" width="90">
                <template #default="{ row }">
                    <el-tag :type="row.msg_type === 1 ? '' : row.msg_type === 2 ? 'warning' : 'info'" size="small">
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
import { chatPublicLists, deleteChatRecord, clearChatMessage, chatRoomLists } from '@/api/chat'
import { usePaging } from '@/hooks/usePaging'
import feedback from '@/utils/feedback'

const queryParams = reactive({
    room_id: '',
    nickname: '',
    content: '',
    msg_type: ''
})

const roomList = ref<any[]>([])

const { pager, getLists, resetPage, resetQuery } = usePaging({
    fetchFun: chatPublicLists,
    params: queryParams
})

const getRoomList = async () => {
    try {
        const res = await chatRoomLists({ page_no: 1, page_size: 100, status: 1 })
        roomList.value = res.lists || []
        if (roomList.value.length > 0 && !queryParams.room_id) {
            queryParams.room_id = 'public'
        }
    } catch (e) {
        console.error(e)
    }
}

const getRoomName = (roomId: string) => {
    const room = roomList.value.find(r => r.room_id === roomId)
    return room ? room.name : roomId
}

const handleDelete = async (id: number) => {
    await feedback.confirm('确定要删除该消息吗？')
    await deleteChatRecord({ id })
    feedback.msgSuccess('删除成功')
    getLists()
}

const handleClear = async () => {
    if (!queryParams.room_id) {
        feedback.msgError('请先选择要清空的聊天室')
        return
    }
    await feedback.confirm('确定要清空该聊天室的所有消息吗？此操作不可恢复！')
    await clearChatMessage({ room_id: queryParams.room_id })
    feedback.msgSuccess('清空成功')
    getLists()
}

getRoomList()
getLists()
</script>
