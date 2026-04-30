-- 文章标签功能数据库升级脚本
-- 开发者：杰哥网络科技
-- QQ：2711793818 杰哥

-- 1. 创建文章标签表
CREATE TABLE IF NOT EXISTS `la_article_tag` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '标签ID',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '标签名称',
  `click_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '点击次数',
  `article_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '文章数量',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序（越大越靠前）',
  `is_hot` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否热门：0-否 1-是',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示：0-隐藏 1-显示',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `idx_name` (`name`),
  KEY `idx_article_count` (`article_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文章标签表';

-- 2. 创建文章标签关联表
CREATE TABLE IF NOT EXISTS `la_article_tag_relation` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `article_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '文章ID',
  `tag_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '标签ID',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_article_tag` (`article_id`, `tag_id`),
  KEY `idx_tag_id` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文章标签关联表';

-- 3. 为文章表添加标签字段
ALTER TABLE `la_article` ADD COLUMN `tags` varchar(255) DEFAULT '' COMMENT '标签（逗号分隔）' AFTER `issue_status`;

-- 4. 插入一些默认标签
INSERT INTO `la_article_tag` (`name`, `sort`, `is_hot`, `is_show`, `create_time`, `update_time`) VALUES
('技术教程', 100, 1, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('热门推荐', 90, 1, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('新手入门', 80, 1, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('高级技巧', 70, 0, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('经验分享', 60, 0, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
