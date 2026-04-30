CREATE TABLE `la_article_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `order_sn` varchar(32) NOT NULL DEFAULT '' COMMENT '订单编号',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `article_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '文章ID',
  `merchant_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商家ID',
  `order_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '订单金额',
  `pay_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '支付状态:0-未支付,1-已支付',
  `pay_time` int(11) unsigned DEFAULT NULL COMMENT '支付时间',
  `transaction_id` varchar(64) DEFAULT '' COMMENT '第三方支付流水号',
  `create_time` int(11) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_order_sn` (`order_sn`),
  KEY `idx_user_article` (`user_id`,`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文章购买订单表';
