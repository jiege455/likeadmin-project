-- 修复文章相关表缺少的字段
-- 开发者：杰哥网络科技
-- QQ：2711793818 杰哥
-- 执行前请备份数据库！

-- =============================================
-- 1. la_article 表 - 添加缺失字段
-- =============================================

-- 添加 prev_issue_no 字段（上一期期号）
ALTER TABLE `la_article` ADD `prev_issue_no` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '上一期期号' AFTER `issue_no`;

-- 添加 prev_issue_content 字段（上一期内容）
ALTER TABLE `la_article` ADD `prev_issue_content` TEXT COMMENT '上一期内容' AFTER `hidden_content`;

-- 添加 is_series 字段（是否系列文章）
ALTER TABLE `la_article` ADD `is_series` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否系列文章:0-否,1-是' AFTER `desc`;


-- =============================================
-- 2. la_article_cate 表 - 添加缺失字段
-- =============================================

-- 添加 published_issues 字段（已发布期数）
ALTER TABLE `la_article_cate` ADD `published_issues` INT(11) NOT NULL DEFAULT 0 COMMENT '已发布期数' AFTER `total_issues`;


-- =============================================
-- 3. la_merchant_follow 表 - 添加缺失字段
-- =============================================

-- 添加 push_enable 字段（推送开关）
ALTER TABLE `la_merchant_follow` ADD `push_enable` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '推送开关:0-关闭,1-开启' AFTER `merchant_id`;


-- =============================================
-- 验证修改
-- =============================================
-- SHOW COLUMNS FROM la_article;
-- SHOW COLUMNS FROM la_article_cate;
-- SHOW COLUMNS FROM la_merchant_follow;
