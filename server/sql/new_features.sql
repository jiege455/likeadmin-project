-- ----------------------------
-- 商家入驻申请表
-- ----------------------------
CREATE TABLE IF NOT EXISTS `la_merchant_apply` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '申请用户ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '商户名称',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '联系电话',
  `desc` varchar(255) DEFAULT '' COMMENT '简介',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态:0-待审核,1-通过,2-拒绝',
  `audit_remark` varchar(255) DEFAULT '' COMMENT '审核备注',
  `create_time` int(10) DEFAULT NULL,
  `update_time` int(10) DEFAULT NULL,
  `delete_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商家入驻申请表';

-- ----------------------------
-- 商户表
-- ----------------------------
CREATE TABLE IF NOT EXISTS `la_merchant` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '关联用户ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '商户名称',
  `desc` varchar(255) DEFAULT '' COMMENT '商户简介',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '联系电话',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '当前余额',
  `total_income` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '累计收入',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态:1-正常,0-禁用',
  `create_time` int(10) DEFAULT NULL,
  `update_time` int(10) DEFAULT NULL,
  `delete_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户表';

-- ----------------------------
-- 商户资金明细表
-- ----------------------------
CREATE TABLE IF NOT EXISTS `la_merchant_income_log` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `merchant_id` int(11) UNSIGNED NOT NULL COMMENT '商户ID',
  `source_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '来源类型:1-文章,2-课程',
  `source_id` int(11) NOT NULL DEFAULT 0 COMMENT '来源ID(文章ID或课程ID)',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变动金额',
  `platform_ratio` decimal(5,2) DEFAULT '0.00' COMMENT '平台抽成比例%',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `create_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `merchant_id` (`merchant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户资金明细表';

-- ----------------------------
-- 分销记录表
-- ----------------------------
CREATE TABLE IF NOT EXISTS `la_distribution_log` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '获得佣金的用户ID(推广员)',
  `source_user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '贡献佣金的用户ID(下单人)',
  `order_id` int(11) DEFAULT 0 COMMENT '关联订单ID',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '佣金金额',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态:0-待结算,1-已结算',
  `create_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='分销记录表';

-- ----------------------------
-- 修改文章表，添加商户ID
-- ----------------------------
ALTER TABLE `la_article` ADD COLUMN `merchant_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属商户ID' AFTER `cid`;
ALTER TABLE `la_article` ADD COLUMN `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '价格' AFTER `merchant_id`;
ALTER TABLE `la_article` ADD COLUMN `audit_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '审核状态:0-待审核,1-通过,2-拒绝' AFTER `is_show`;

-- ----------------------------
-- 修改用户表，添加邀请人ID和分销金额
-- ----------------------------
ALTER TABLE `la_user` ADD COLUMN `inviter_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '邀请人ID' AFTER `id`;
ALTER TABLE `la_user` ADD COLUMN `commission` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '当前可提现佣金' AFTER `inviter_id`;
ALTER TABLE `la_user` ADD COLUMN `total_commission` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '累计获得佣金' AFTER `commission`;

-- ----------------------------
-- 课程表 (简单设计)
-- ----------------------------
CREATE TABLE IF NOT EXISTS `la_course` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `merchant_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商户ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '课程标题',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '价格',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态:1-上架,0-下架',
  `create_time` int(10) DEFAULT NULL,
  `update_time` int(10) DEFAULT NULL,
  `delete_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='课程表';

-- ----------------------------
-- 菜单管理数据 (示例，需根据实际情况调整 pid)
-- ----------------------------
-- 假设顶级菜单 ID 从 1000 开始
-- 请在 phpMyAdmin 中执行完上述结构后，手动添加菜单或使用以下 SQL (需确认 pid)
