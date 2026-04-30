ALTER TABLE `la_user` ADD COLUMN `is_distributor` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否分销员: [0=否, 1=是]';

CREATE TABLE `la_distribution_apply` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `mobile` varchar(32) NOT NULL DEFAULT '' COMMENT '手机号',
  `reason` varchar(255) NOT NULL DEFAULT '' COMMENT '申请原因',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态: 0-待审核, 1-审核通过, 2-审核拒绝',
  `audit_remark` varchar(255) DEFAULT '' COMMENT '审核备注',
  `audit_time` int(10) DEFAULT NULL COMMENT '审核时间',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='分销员申请表';
