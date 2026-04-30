ALTER TABLE `la_article` ADD COLUMN `is_paid` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否付费:0-免费,1-付费';
ALTER TABLE `la_article` ADD COLUMN `price` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '付费价格';
ALTER TABLE `la_article` ADD COLUMN `hidden_content` text COMMENT '隐藏内容(付费可见)';
