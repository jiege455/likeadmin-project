CREATE TABLE IF NOT EXISTS `la_withdraw_apply` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商家ID',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '提现金额',
  `fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '手续费',
  `left_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '提现后余额',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '提现方式: 1-微信零钱, 2-支付宝, 3-银行卡',
  `account_info` text COMMENT '账户信息(JSON)',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态: 1-待审核, 2-提现中, 3-提现成功, 4-提现失败',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '管理员备注',
  `transfer_result` text COMMENT '转账结果',
  `create_time` int(11) unsigned DEFAULT NULL,
  `update_time` int(11) unsigned DEFAULT NULL,
  `audit_time` int(11) unsigned DEFAULT NULL,
  `delete_time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_merchant_id` (`merchant_id`),
  KEY `idx_create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='提现申请表';
