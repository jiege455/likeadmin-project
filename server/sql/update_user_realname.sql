CREATE TABLE IF NOT EXISTS `la_user_realname` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `real_name` varchar(32) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `id_card` varchar(32) NOT NULL DEFAULT '' COMMENT '身份证号',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态: 0-待审核, 1-认证通过, 2-认证失败',
  `fail_reason` varchar(255) NOT NULL DEFAULT '' COMMENT '失败原因',
  `create_time` int(11) unsigned DEFAULT NULL,
  `update_time` int(11) unsigned DEFAULT NULL,
  `delete_time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_id` (`user_id`),
  KEY `idx_create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户实名认证表';

INSERT INTO `la_system_menu`(`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`) VALUES (20, 'C', '实名认证', 'el-icon-Postcard', 0, 'user.realname/lists', 'realname', 'user/realname/index', '', '', 0, 1, 0, 1677143000, 1677143000);
