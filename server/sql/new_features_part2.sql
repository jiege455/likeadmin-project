-- ----------------------------
-- 系统公告表
-- ----------------------------
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

-- ----------------------------
-- 公共消息表 (留言/聊天)
-- ----------------------------
CREATE TABLE IF NOT EXISTS `la_public_message` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `content` varchar(500) NOT NULL DEFAULT '' COMMENT '消息内容',
  `is_illegal` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否违规',
  `create_time` int(10) DEFAULT NULL,
  `delete_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='公共消息表';

-- ----------------------------
-- 提现申请表 (商户/推广员)
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
