-- ============================================
-- 公共聊天室功能数据库升级脚本
-- 开发者公众号：杰哥网络科技
-- QQ: 2711793818 杰哥
-- ============================================

-- 聊天消息表
DROP TABLE IF EXISTS `la_chat_message`;
CREATE TABLE `la_chat_message` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `room_id` varchar(50) NOT NULL DEFAULT 'public' COMMENT '聊天室ID，public为公共频道',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '发送用户ID',
  `nickname` varchar(100) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '用户头像',
  `content` text NOT NULL COMMENT '消息内容',
  `msg_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '消息类型：1-文字 2-图片 3-系统消息',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否删除：0-否 1-是',
  `create_time` int(10) UNSIGNED DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_room_id` (`room_id`),
  KEY `idx_create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='聊天消息表';

-- 聊天室配置表
DROP TABLE IF EXISTS `la_chat_room`;
CREATE TABLE `la_chat_room` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '聊天室名称',
  `room_id` varchar(50) NOT NULL DEFAULT '' COMMENT '聊天室ID',
  `description` varchar(255) DEFAULT '' COMMENT '聊天室描述',
  `max_users` int(11) NOT NULL DEFAULT 1000 COMMENT '最大用户数',
  `is_public` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否公开：0-否 1-是',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：0-禁用 1-启用',
  `create_time` int(10) UNSIGNED DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_room_id` (`room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='聊天室配置表';

-- 初始化公共聊天室
INSERT INTO `la_chat_room` (`name`, `room_id`, `description`, `max_users`, `is_public`, `status`, `create_time`) 
VALUES ('公共频道', 'public', '所有人都可以参与的公共聊天室', 10000, 1, 1, UNIX_TIMESTAMP());
