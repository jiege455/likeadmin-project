-- 文章订单表添加支付相关字段
-- 执行此SQL前请备份数据库

ALTER TABLE `la_article_order` ADD COLUMN `pay_way` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '支付方式:1-微信支付,2-支付宝,3-余额支付' AFTER `merchant_profit`;
ALTER TABLE `la_article_order` ADD COLUMN `pay_sn` varchar(64) NOT NULL DEFAULT '' COMMENT '支付编号' AFTER `order_sn`;
