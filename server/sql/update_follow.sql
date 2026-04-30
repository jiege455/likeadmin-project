-- 商家关注表
CREATE TABLE IF NOT EXISTS `la_merchant_follow` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `merchant_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商家ID',
  `create_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `merchant_id` (`merchant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商家关注表';
