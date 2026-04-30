/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * 聊天相关 API
 */
import request from '@/utils/request'

// 消息类型枚举
export enum MessageTypeEnum {
    TEXT = 1,
    IMAGE = 2,
    VOICE = 3,
    VIDEO = 4
}

// 消息数据结构
export interface ChatMessage {
    id: number
    conversation_id: string
    sender_id: number
    sender_name: string
    sender_avatar: string
    message_type: MessageTypeEnum
    content: string
    create_time: string
    is_read: number
}

// 聊天室数据结构
export interface ChatRoom {
    id: string
    name: string
    avatar: string
    member_count: number
    last_message?: ChatMessage
}

// WebSocket 配置
export interface ChatWebSocketConfig {
    ws_url: string
    token: string
    heartbeat_interval: number
}

// 聊天设置
export interface ChatSetting {
    announcement: string
    notice: string
}

// 获取聊天消息列表参数
export interface GetChatMessageListParams {
    conversation_id?: string
    page_no?: number
    page_size?: number
}

// 获取聊天消息列表响应
export interface GetChatMessageListResponse {
    list: ChatMessage[]
    total: number
}

// 获取聊天室列表响应 - 后端直接返回数组
export interface ChatRoomItem {
    id: number
    name: string
    room_id: string
    description: string
    max_users: number
}
export type GetChatRoomListResponse = ChatRoomItem[]

// 发送消息参数
export interface SendChatMessageParams {
    conversation_id?: string
    message_type: MessageTypeEnum
    content: string
}

// 发送消息响应
export interface SendChatMessageResponse {
    message_id: number
    create_time: string
}

// 获取聊天消息列表
export function getChatMessageList(
    data: GetChatMessageListParams
): Promise<GetChatMessageListResponse> {
    return request.get({ url: '/chat/lists', data })
}

// 获取聊天室列表
export function getChatRoomList(): Promise<GetChatRoomListResponse> {
    return request.get({ url: '/chat/rooms' })
}

// 获取 WebSocket 配置
export function getChatConfig(): Promise<ChatWebSocketConfig> {
    return request.get({ url: '/chat/config' })
}

// 获取聊天设置（公告等）
export function getChatSetting(): Promise<ChatSetting> {
    return request.get({ url: '/chat/setting' })
}

// 发送消息（HTTP 备用方式）
export function sendChatMessage(data: SendChatMessageParams): Promise<SendChatMessageResponse> {
    return request.post({ url: '/chat/send', data })
}

// ========== 私聊相关 API ==========

// 会话数据结构
export interface Conversation {
    conversation_id: string
    target_id: number
    target_type: number
    target_name: string
    target_avatar: string
    last_message?: ChatMessage
    unread_count: number
}

// 获取会话列表参数
export interface GetConversationListParams {
    page_no?: number
    page_size?: number
}

// 获取会话列表响应
export interface GetConversationListResponse {
    lists: Conversation[]
    total: number
}

// 创建/获取私聊会话参数
export interface CreateConversationParams {
    target_id: number
    target_type?: number
}

// 创建/获取私聊会话响应
export interface CreateConversationResponse {
    conversation_id: string
    target_info?: {
        id: number
        name: string
        avatar: string
    }
}

// 获取会话详情参数
export interface GetConversationDetailParams {
    conversation_id: string
}

// 获取会话详情响应
export type GetConversationDetailResponse = Conversation

// 标记会话已读参数
export interface MarkConversationReadParams {
    conversation_id: string
}

// 删除会话参数
export interface DeleteConversationParams {
    conversation_id: string
}

// 获取私聊消息列表参数
export interface GetPrivateMessageListParams {
    conversation_id: string
    page_no?: number
    page_size?: number
}

// 获取私聊消息列表响应
export interface GetPrivateMessageListResponse {
    list: ChatMessage[]
    total: number
}

// 获取会话列表
export function getConversationList(
    data?: GetConversationListParams
): Promise<GetConversationListResponse> {
    return request.get({ url: '/conversation/list', data })
}

// 创建/获取私聊会话
export function createConversation(
    data: CreateConversationParams
): Promise<CreateConversationResponse> {
    return request.post({ url: '/conversation/create', data })
}

// 获取会话详情
export function getConversationDetail(
    data: GetConversationDetailParams
): Promise<GetConversationDetailResponse> {
    return request.get({ url: '/conversation/detail', data })
}

// 标记会话已读
export function markConversationRead(data: MarkConversationReadParams): Promise<void> {
    return request.post({ url: '/conversation/read', data })
}

// 删除会话
export function deleteConversation(data: DeleteConversationParams): Promise<void> {
    return request.post({ url: '/conversation/delete', data })
}

// 获取私聊消息列表
export function getPrivateMessageList(
    data: GetPrivateMessageListParams
): Promise<GetPrivateMessageListResponse> {
    return request.get({ url: '/conversation/messages', data })
}

// 获取未读消息总数
export function getUnreadTotal(): Promise<{ total: number }> {
    return request.get({ url: '/conversation/unreadTotal' })
}
