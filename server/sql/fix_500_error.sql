CREATE TABLE IF NOT EXISTS `la_distribution_apply` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
  `reason` varchar(255) NOT NULL DEFAULT '' COMMENT '申请原因',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态:0-待审核,1-审核通过,2-审核拒绝',
  `audit_remark` varchar(255) NOT NULL DEFAULT '' COMMENT '审核备注',
  `audit_time` int(11) unsigned DEFAULT NULL COMMENT '审核时间',
  `create_time` int(11) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='分销商申请记录表';

-- 如果 la_user 表没有 is_distributor 字段，请执行下面这句（如果报错字段已存在请忽略）：
-- ALTER TABLE `la_user` ADD COLUMN `is_distributor` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否分销商:0-否,1-是' AFTER `distribution_status`;
