DROP TABLE IF EXISTS `la_system_notice`;
CREATE TABLE `la_system_notice` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '公告标题',
  `content` text COMMENT '公告内容',
  `recipient` tinyint(1) NOT NULL DEFAULT 1 COMMENT '接收对象:1-全员,2-仅商户',
  `is_show` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态:1-显示,0-隐藏',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `create_time` int(10) DEFAULT NULL,
  `update_time` int(10) DEFAULT NULL,
  `delete_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统公告表';
