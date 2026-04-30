-- 系列文章连期发布功能数据库升级
-- 开发者：杰哥网络科技
-- QQ：2711793818 杰哥

-- 1. 为文章表添加上一期内容字段
ALTER TABLE `la_article` ADD COLUMN `prev_issue_content` TEXT NULL COMMENT '上一期内容(免费预览)' AFTER `hidden_content`;
ALTER TABLE `la_article` ADD COLUMN `prev_issue_no` VARCHAR(20) NULL DEFAULT '' COMMENT '上一期期次号' AFTER `prev_issue_content`;
ALTER TABLE `la_article` ADD COLUMN `issue_title` VARCHAR(200) NULL DEFAULT '' COMMENT '期次标题' AFTER `prev_issue_no`;

-- 2. 创建期次购买记录表
CREATE TABLE IF NOT EXISTS `la_series_issue_order` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
  `series_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '系列ID',
  `article_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '文章ID',
  `issue_no` varchar(20) NOT NULL DEFAULT '' COMMENT '购买的期次号',
  `order_sn` varchar(32) NOT NULL DEFAULT '' COMMENT '订单编号',
  `pay_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '支付金额',
  `pay_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支付状态:0-未支付,1-已支付',
  `pay_time` int(10) UNSIGNED DEFAULT NULL COMMENT '支付时间',
  `pay_way` tinyint(1) DEFAULT NULL COMMENT '支付方式:1-余额,2-微信,3-支付宝',
  `transaction_id` varchar(64) DEFAULT '' COMMENT '第三方交易号',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_order_sn` (`order_sn`),
  KEY `idx_user_series` (`user_id`, `series_id`),
  KEY `idx_article` (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系列期次购买记录表';
