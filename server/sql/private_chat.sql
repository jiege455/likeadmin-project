-- =============================================
-- 私聊功能数据库表
-- 开发者公众号：杰哥网络科技
-- QQ: 2711793818 杰哥
-- =============================================

-- 1. 私聊会话表
DROP TABLE IF EXISTS `la_chat_conversation`;
CREATE TABLE `la_chat_conversation` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `conversation_id` varchar(64) NOT NULL DEFAULT '' COMMENT '会话ID，格式：private_{小ID}_{大ID}',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `target_id` int(11) UNSIGNED NOT NULL COMMENT '对方ID（商家ID或用户ID）',
  `target_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '对方类型：1-商家 2-用户',
  `last_message` varchar(500) NOT NULL DEFAULT '' COMMENT '最后一条消息内容',
  `last_message_time` int(10) UNSIGNED DEFAULT NULL COMMENT '最后消息时间',
  `unread_count` int(11) NOT NULL DEFAULT 0 COMMENT '未读消息数',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否删除：0-否 1-是',
  `create_time` int(10) UNSIGNED DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_conversation_user` (`conversation_id`, `user_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_target_id` (`target_id`),
  KEY `idx_last_message_time` (`last_message_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='私聊会话表';

-- 2. 聊天禁言记录表
DROP TABLE IF EXISTS `la_chat_ban`;
CREATE TABLE `la_chat_ban` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '被禁言用户ID',
  `user_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '用户类型：1-普通用户 2-商家',
  `ban_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '禁言类型：1-私聊禁言 2-公共聊天禁言 3-全部禁言',
  `reason` varchar(255) NOT NULL DEFAULT '' COMMENT '禁言原因',
  `admin_id` int(11) UNSIGNED NOT NULL COMMENT '操作管理员ID',
  `expire_time` int(10) UNSIGNED DEFAULT NULL COMMENT '禁言到期时间，NULL表示永久',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：0-已解除 1-禁言中',
  `create_time` int(10) UNSIGNED DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_user_type` (`user_type`),
  KEY `idx_status` (`status`),
  KEY `idx_expire_time` (`expire_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='聊天禁言记录表';

-- 3. 为消息表添加索引（如果不存在）
-- ALTER TABLE `la_chat_message` ADD INDEX `idx_room_user` (`room_id`, `user_id`);
