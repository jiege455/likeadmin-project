-- 文章标签表升级 - 添加创建者字段
-- 开发者：杰哥网络科技 QQ2711793818 杰哥

-- 添加 user_id 字段
ALTER TABLE `la_article_tag` ADD COLUMN `user_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建者ID（0=系统预留）' AFTER `name`;

-- 添加索引
ALTER TABLE `la_article_tag` ADD INDEX `idx_user_id` (`user_id`);

-- 清空预留标签（可选，如果需要清空的话）
-- DELETE FROM `la_article_tag` WHERE `user_id` = 0;
