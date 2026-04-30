-- =====================================================
-- 商家推广绑定功能 - 数据库升级脚本
-- 开发者：杰哥网络科技 qq2711793818 杰哥
-- =====================================================

-- 1. 为用户表添加当前显示商家字段
ALTER TABLE `la_user` 
ADD COLUMN `current_merchant_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '当前显示的商家ID' AFTER `inviter_id`;

-- 2. 添加索引优化查询
ALTER TABLE `la_user` ADD INDEX `idx_current_merchant` (`current_merchant_id`);

-- 执行完成后请刷新缓存
