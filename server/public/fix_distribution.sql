SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for la_distribution_log
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
-- Table structure for la_withdraw_apply
-- ----------------------------
CREATE TABLE IF NOT EXISTS `la_withdraw_apply` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID(推广员)',
  `merchant_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商户ID',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '提现金额',
  `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '类型:1-商户,2-推广员',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态:0-待审核,1-通过,2-拒绝',
  `audit_remark` varchar(255) DEFAULT '' COMMENT '审核备注',
  `create_time` int(10) DEFAULT NULL,
  `update_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='提现申请表';

SET FOREIGN_KEY_CHECKS = 1;
