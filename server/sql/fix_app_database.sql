-- ============================================
-- APP接口修复SQL脚本
-- 修复数据库缺少的字段和表
-- 开发者公众号：杰哥网络科技
-- QQ: 2711793818 杰哥
-- 执行前请备份数据库！
-- ============================================

-- 1. 为 la_article_cate 表添加 is_series 字段（文章分类表）
ALTER TABLE `la_article_cate` ADD COLUMN `is_series` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否系列文章：0-否 1-是' AFTER `is_show`;

-- 2. 为 la_article_cate 表添加系列相关字段
ALTER TABLE `la_article_cate` ADD COLUMN `series_price` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '系列价格' AFTER `is_series`;
ALTER TABLE `la_article_cate` ADD COLUMN `total_issues` int(11) NOT NULL DEFAULT 0 COMMENT '总期数' AFTER `series_price`;
ALTER TABLE `la_article_cate` ADD COLUMN `auto_publish` tinyint(1) NOT NULL DEFAULT 0 COMMENT '自动发布：0-否 1-是' AFTER `total_issues`;
ALTER TABLE `la_article_cate` ADD COLUMN `publish_interval` int(11) NOT NULL DEFAULT 0 COMMENT '发布间隔（秒）' AFTER `auto_publish`;
ALTER TABLE `la_article_cate` ADD COLUMN `series_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '系列状态：0-下架 1-上架' AFTER `publish_interval`;
ALTER TABLE `la_article_cate` ADD COLUMN `lottery_type` varchar(50) NOT NULL DEFAULT '' COMMENT '彩票类型' AFTER `series_status`;
ALTER TABLE `la_article_cate` ADD COLUMN `series_desc` text COMMENT '系列介绍' AFTER `lottery_type`;
ALTER TABLE `la_article_cate` ADD COLUMN `merchant_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属商户ID' AFTER `series_desc`;

-- 3. 为 la_article 表添加文章标签字段
ALTER TABLE `la_article` ADD COLUMN `tags` varchar(255) NOT NULL DEFAULT '' COMMENT '标签（逗号分隔）' AFTER `issue_status`;

-- 4. 为 la_article 表添加系列期次相关字段
ALTER TABLE `la_article` ADD COLUMN `prev_issue_content` text COMMENT '上一期内容（免费预览）' AFTER `hidden_content`;
ALTER TABLE `la_article` ADD COLUMN `prev_issue_no` varchar(20) NOT NULL DEFAULT '' COMMENT '上一期期次号' AFTER `prev_issue_content`;
ALTER TABLE `la_article` ADD COLUMN `issue_title` varchar(200) NOT NULL DEFAULT '' COMMENT '期次标题' AFTER `prev_issue_no`;

-- 5. 创建文章标签表（如果不存在）
CREATE TABLE IF NOT EXISTS `la_article_tag` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '标签ID',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '标签名称',
  `click_count` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '点击次数',
  `article_count` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '文章数量',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序（越大越靠前）',
  `is_hot` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否热门：0-否 1-是',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '是否显示：0-隐藏 1-显示',
  `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '更新时间',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `idx_name` (`name`),
  KEY `idx_article_count` (`article_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文章标签表';

-- 6. 创建文章标签关联表（如果不存在）
CREATE TABLE IF NOT EXISTS `la_article_tag_relation` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `article_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '文章ID',
  `tag_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '标签ID',
  `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_article_tag` (`article_id`, `tag_id`),
  KEY `idx_tag_id` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文章标签关联表';

-- 7. 创建聊天消息表（如果不存在）
CREATE TABLE IF NOT EXISTS `la_chat_message` (
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

-- 8. 创建聊天室配置表（如果不存在）
CREATE TABLE IF NOT EXISTS `la_chat_room` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '聊天室名称',
  `room_id` varchar(50) NOT NULL DEFAULT '' COMMENT '聊天室ID',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '聊天室描述',
  `max_users` int(11) NOT NULL DEFAULT 1000 COMMENT '最大用户数',
  `is_public` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否公开：0-否 1-是',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：0-禁用 1-启用',
  `create_time` int(10) UNSIGNED DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_room_id` (`room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='聊天室配置表';

-- 9. 创建私聊会话表（如果不存在）
CREATE TABLE IF NOT EXISTS `la_chat_conversation` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `conversation_id` varchar(64) NOT NULL DEFAULT '' COMMENT '会话ID',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `target_id` int(11) UNSIGNED NOT NULL COMMENT '对方ID',
  `target_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '对方类型：1-商家 2-用户',
  `last_message` varchar(500) NOT NULL DEFAULT '' COMMENT '最后一条消息内容',
  `last_message_time` int(10) UNSIGNED DEFAULT NULL COMMENT '最后消息时间',
  `unread_count` int(11) NOT NULL DEFAULT 0 COMMENT '未读消息数',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否删除：0-否 1-是',
  `create_time` int(10) UNSIGNED DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_target_id` (`target_id`),
  KEY `idx_last_message_time` (`last_message_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='私聊会话表';

-- 10. 创建聊天禁言记录表（如果不存在）
CREATE TABLE IF NOT EXISTS `la_chat_ban` (
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

-- 11. 创建商家入驻申请表（如果不存在）
CREATE TABLE IF NOT EXISTS `la_merchant_apply` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '申请用户ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '商户名称',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '联系电话',
  `desc` varchar(255) NOT NULL DEFAULT '' COMMENT '简介',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态:0-待审核,1-通过,2-拒绝',
  `audit_remark` varchar(255) NOT NULL DEFAULT '' COMMENT '审核备注',
  `create_time` int(10) DEFAULT NULL,
  `update_time` int(10) DEFAULT NULL,
  `delete_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商家入驻申请表';

-- 12. 创建商户表（如果不存在）
CREATE TABLE IF NOT EXISTS `la_merchant` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '关联用户ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '商户名称',
  `desc` varchar(255) NOT NULL DEFAULT '' COMMENT '商户简介',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '联系电话',
  `money` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '当前余额',
  `total_income` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '累计收入',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态:1-正常,0-禁用',
  `create_time` int(10) DEFAULT NULL,
  `update_time` int(10) DEFAULT NULL,
  `delete_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户表';

-- 13. 创建商户资金明细表（如果不存在）
CREATE TABLE IF NOT EXISTS `la_merchant_income_log` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `merchant_id` int(11) UNSIGNED NOT NULL COMMENT '商户ID',
  `source_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '来源类型:1-文章,2-课程',
  `source_id` int(11) NOT NULL DEFAULT 0 COMMENT '来源ID',
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '变动金额',
  `platform_ratio` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT '平台抽成比例%',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `create_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `merchant_id` (`merchant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户资金明细表';

-- 14. 创建分销记录表（如果不存在）
CREATE TABLE IF NOT EXISTS `la_distribution_log` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '获得佣金的用户ID',
  `source_user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '贡献佣金的用户ID',
  `order_id` int(11) NOT NULL DEFAULT 0 COMMENT '关联订单ID',
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '佣金金额',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态:0-待结算,1-已结算',
  `create_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='分销记录表';

-- 15. 创建系统公告表（如果不存在）
CREATE TABLE IF NOT EXISTS `la_system_notice` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '公告标题',
  `content` text COMMENT '公告内容',
  `recipient` tinyint(1) NOT NULL DEFAULT 1 COMMENT '接收对象:1-全员,2-仅商户',
  `is_show` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态:1-显示,0-隐藏',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `create_time` int(10) DEFAULT NULL,
  `update_time` int(10) DEFAULT NULL,
  `delete_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统公告表';

-- 16. 创建公共消息表（如果不存在）
CREATE TABLE IF NOT EXISTS `la_public_message` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `content` varchar(500) NOT NULL DEFAULT '' COMMENT '消息内容',
  `is_illegal` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否违规',
  `create_time` int(10) DEFAULT NULL,
  `delete_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='公共消息表';

-- 17. 创建提现申请表（如果不存在）
CREATE TABLE IF NOT EXISTS `la_withdraw_apply` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `merchant_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商户ID',
  `money` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '提现金额',
  `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '类型:1-商户,2-推广员',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态:0-待审核,1-通过,2-拒绝',
  `audit_remark` varchar(255) NOT NULL DEFAULT '' COMMENT '审核备注',
  `create_time` int(10) DEFAULT NULL,
  `update_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='提现申请表';

-- 18. 创建系列期次购买记录表（如果不存在）
CREATE TABLE IF NOT EXISTS `la_series_issue_order` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `series_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '系列ID',
  `article_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '文章ID',
  `issue_no` varchar(20) NOT NULL DEFAULT '' COMMENT '购买的期次号',
  `order_sn` varchar(32) NOT NULL DEFAULT '' COMMENT '订单编号',
  `pay_price` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '支付金额',
  `pay_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '支付状态:0-未支付,1-已支付',
  `pay_time` int(10) UNSIGNED DEFAULT NULL COMMENT '支付时间',
  `pay_way` tinyint(1) DEFAULT NULL COMMENT '支付方式:1-余额,2-微信,3-支付宝',
  `transaction_id` varchar(64) NOT NULL DEFAULT '' COMMENT '第三方交易号',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_order_sn` (`order_sn`),
  KEY `idx_user_series` (`user_id`, `series_id`),
  KEY `idx_article` (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系列期次购买记录表';

-- 19. 为用户表添加分销相关字段
ALTER TABLE `la_user` ADD COLUMN `inviter_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '邀请人ID' AFTER `id`;
ALTER TABLE `la_user` ADD COLUMN `commission` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '当前可提现佣金' AFTER `inviter_id`;
ALTER TABLE `la_user` ADD COLUMN `total_commission` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '累计获得佣金' AFTER `commission`;

-- 20. 添加文章分类索引
ALTER TABLE `la_article_cate` ADD INDEX `idx_merchant` (`merchant_id`);
ALTER TABLE `la_article_cate` ADD INDEX `idx_series` (`is_series`, `series_status`);

-- 21. 初始化默认聊天室（如果不存在）
INSERT IGNORE INTO `la_chat_room` (`name`, `room_id`, `description`, `max_users`, `is_public`, `status`, `create_time`)
VALUES ('公共频道', 'public', '所有人都可以参与的公共聊天室', 10000, 1, 1, UNIX_TIMESTAMP());

-- 22. 初始化默认文章标签
INSERT IGNORE INTO `la_article_tag` (`name`, `sort`, `is_hot`, `is_show`, `create_time`, `update_time`) VALUES
('技术教程', 100, 1, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('热门推荐', 90, 1, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('新手入门', 80, 1, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('高级技巧', 70, 0, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('经验分享', 60, 0, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
