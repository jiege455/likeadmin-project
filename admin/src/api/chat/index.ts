/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * 聊天管理API
 */
import request from '@/utils/request'

// 聊天室列表
export function chatRoomLists(params: any) {
    return request.get({ url: '/chat.chat_room/lists', params })
}

// 添加聊天室
export function addChatRoom(params: any) {
    return request.post({ url: '/chat.chat_room/add', params })
}

// 编辑聊天室
export function editChatRoom(params: any) {
    return request.post({ url: '/chat.chat_room/edit', params })
}

// 聊天室详情
export function chatRoomDetail(params: any) {
    return request.get({ url: '/chat.chat_room/detail', params })
}

// 删除聊天室
export function deleteChatRoom(params: any) {
    return request.post({ url: '/chat.chat_room/delete', params })
}

// 修改聊天室状态
export function changeChatRoomStatus(params: any) {
    return request.post({ url: '/chat.chat_room/status', params })
}

// 聊天消息列表
export function chatMessageLists(params: any) {
    return request.get({ url: '/chat.chat_message/lists', params })
}

// 聊天消息详情
export function chatMessageDetail(params: any) {
    return request.get({ url: '/chat.chat_message/detail', params })
}

// 删除聊天消息
export function deleteChatMessage(params: any) {
    return request.post({ url: '/chat.chat_message/delete', params })
}

// 清空聊天消息
export function clearChatMessage(params: any) {
    return request.post({ url: '/chat.chat_message/clear', params })
}

// 违禁词列表
export function bannedWordLists(params: any) {
    return request.get({ url: '/chat.chat_banned_word/lists', params })
}

// 添加违禁词
export function addBannedWord(params: any) {
    return request.post({ url: '/chat.chat_banned_word/add', params })
}

// 编辑违禁词
export function editBannedWord(params: any) {
    return request.post({ url: '/chat.chat_banned_word/edit', params })
}

// 删除违禁词
export function deleteBannedWord(params: any) {
    return request.post({ url: '/chat.chat_banned_word/delete', params })
}

// 修改违禁词状态
export function changeBannedWordStatus(params: any) {
    return request.post({ url: '/chat.chat_banned_word/status', params })
}

// 获取聊天设置
export function getChatSetting() {
    return request.get({ url: '/chat.chat_setting/getConfig' })
}

// 保存聊天设置
export function setChatSetting(params: any) {
    return request.post({ url: '/chat.chat_setting/setConfig', params })
}

// ========== 聊天记录管理 ==========

// 聊天记录列表
export function chatRecordLists(params: any) {
    return request.get({ url: '/chat.chat_record/lists', params })
}

// 聊天记录详情
export function chatRecordDetail(params: any) {
    return request.get({ url: '/chat.chat_record/detail', params })
}

// 删除聊天记录
export function deleteChatRecord(params: any) {
    return request.post({ url: '/chat.chat_record/delete', params })
}

// 获取私聊会话列表
export function chatConversations(params: any) {
    return request.get({ url: '/chat.chat_record/conversations', params })
}

// ========== 公共聊天室消息 ==========

// 公共聊天室消息列表
export function chatPublicLists(params: any) {
    return request.get({ url: '/chat.chat_public/lists', params })
}

// ========== 私聊消息 ==========

// 私聊消息列表
export function chatPrivateLists(params: any) {
    return request.get({ url: '/chat.chat_private/lists', params })
}

// ========== 禁言管理 ==========

// 禁言记录列表
export function chatBanLists(params: any) {
    return request.get({ url: '/chat.chat_ban/lists', params })
}

// 添加禁言
export function addChatBan(params: any) {
    return request.post({ url: '/chat.chat_ban/add', params })
}

// 解除禁言
export function cancelChatBan(params: any) {
    return request.post({ url: '/chat.chat_ban/cancel', params })
}

// 禁言详情
export function chatBanDetail(params: any) {
    return request.get({ url: '/chat.chat_ban/detail', params })
}

// 检查用户禁言状态
export function checkChatBan(params: any) {
    return request.get({ url: '/chat.chat_ban/check', params })
}
