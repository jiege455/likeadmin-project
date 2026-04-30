SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

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
-- 用户关注商户表
-- ----------------------------
CREATE TABLE IF NOT EXISTS `la_user_follow_merchant` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `merchant_id` int(11) UNSIGNED NOT NULL COMMENT '商户ID',
  `is_push` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否接收推送:1-是,0-否',
  `create_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_merchant` (`user_id`, `merchant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户关注商户表';

-- ----------------------------
-- 用户商家绑定关系表
-- ----------------------------
CREATE TABLE IF NOT EXISTS `la_user_merchant` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `merchant_id` int(11) NOT NULL DEFAULT '0' COMMENT '商家ID',
  `inviter_id` int(11) NOT NULL DEFAULT '0' COMMENT '邀请人ID',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_merchant` (`user_id`,`merchant_id`),
  KEY `idx_inviter` (`inviter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户商家绑定关系表';

SET FOREIGN_KEY_CHECKS = 1;
