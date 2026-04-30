-- ============================================
-- 聊天功能扩展 - 违禁词和设置
-- 开发者公众号：杰哥网络科技
-- QQ: 2711793818 杰哥
-- ============================================

-- 违禁词表
DROP TABLE IF EXISTS `la_chat_banned_word`;
CREATE TABLE `la_chat_banned_word` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `word` varchar(100) NOT NULL DEFAULT '' COMMENT '违禁词',
  `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '类型：1-违禁词 2-敏感词',
  `replace_word` varchar(100) DEFAULT '' COMMENT '替换词（为空则直接拦截）',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：0-禁用 1-启用',
  `create_time` int(10) UNSIGNED DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_word` (`word`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='聊天违禁词表';

-- 聊天设置表
DROP TABLE IF EXISTS `la_chat_setting`;
CREATE TABLE `la_chat_setting` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '配置名称',
  `value` text COMMENT '配置值',
  `create_time` int(10) UNSIGNED DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='聊天设置表';

-- 初始化聊天设置
INSERT INTO `la_chat_setting` (`name`, `value`, `create_time`) VALUES 
('chat_enabled', '1', UNIX_TIMESTAMP()),
('chat_notice', '欢迎来到聊天室，请文明聊天！', UNIX_TIMESTAMP()),
('max_message_length', '500', UNIX_TIMESTAMP()),
('message_interval', '1', UNIX_TIMESTAMP()),
('enable_banned_word', '1', UNIX_TIMESTAMP()),
('enable_ip_blacklist', '0', UNIX_TIMESTAMP());

-- 初始化常用违禁词
INSERT INTO `la_chat_banned_word` (`word`, `type`, `status`, `create_time`) VALUES 
('傻逼', 1, 1, UNIX_TIMESTAMP()),
('操你', 1, 1, UNIX_TIMESTAMP()),
('妈的', 1, 1, UNIX_TIMESTAMP()),
('草泥马', 1, 1, UNIX_TIMESTAMP()),
(' fuck ', 1, 1, UNIX_TIMESTAMP()),
(' shit ', 1, 1, UNIX_TIMESTAMP());
