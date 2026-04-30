-- 文章订单表添加退款相关字段
-- 开发者：杰哥网络科技 qq2711793818 杰哥
-- 执行此SQL前请备份数据库

ALTER TABLE `la_article_order` ADD COLUMN `refund_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '退款状态:0-未退款,1-已退款' AFTER `transaction_id`;
ALTER TABLE `la_article_order` ADD COLUMN `refund_time` int(11) unsigned DEFAULT NULL COMMENT '退款时间' AFTER `refund_status`;
ALTER TABLE `la_article_order` ADD COLUMN `refund_reason` varchar(255) NOT NULL DEFAULT '' COMMENT '退款原因' AFTER `refund_time`;
