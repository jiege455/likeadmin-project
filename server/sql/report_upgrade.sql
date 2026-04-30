-- 举报功能数据库升级脚本
-- 开发者：杰哥网络科技
-- QQ: 2711793818 杰哥
-- 日期: 2026-02-24

-- 添加举报类型字段
ALTER TABLE `la_merchant_complaint` 
ADD COLUMN `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '举报类型:1=商家,2=文章' AFTER `id`,
ADD COLUMN `target_id` int(11) NOT NULL DEFAULT 0 COMMENT '目标ID(商家ID或文章ID)' AFTER `type`,
ADD COLUMN `reason` varchar(100) DEFAULT '' COMMENT '举报原因' AFTER `content`;

-- 添加索引
ALTER TABLE `la_merchant_complaint` 
ADD INDEX `idx_type_target` (`type`, `target_id`);

-- 修改表注释
ALTER TABLE `la_merchant_complaint` COMMENT '用户举报表';

-- 同步更新 like.sql 初始化文件（需要手动同步）
