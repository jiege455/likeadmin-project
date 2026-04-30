-- 创建系列表
DROP TABLE IF EXISTS `la_series`;
CREATE TABLE `la_series` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '系列名称',
  `description` text COMMENT '系列描述',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT '封面图片',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '系列价格',
  `lottery_type` varchar(50) NOT NULL DEFAULT '' COMMENT '彩票类型: fc3d-福彩3D, pl3-排列三, ssq-双色球, dlt-大乐透',
  `total_issues` int(11) NOT NULL DEFAULT '0' COMMENT '总期数',
  `current_issue` int(11) NOT NULL DEFAULT '0' COMMENT '当前期数',
  `auto_publish` tinyint(1) NOT NULL DEFAULT '0' COMMENT '自动发布: 0-否, 1-是',
  `publish_interval` int(11) NOT NULL DEFAULT '0' COMMENT '发布间隔(秒)',
  `series_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '系列状态: 0-已结束, 1-进行中',
  `merchant_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '所属商户ID',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `create_time` int(11) UNSIGNED DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(11) UNSIGNED DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `idx_merchant_id` (`merchant_id`),
  KEY `idx_lottery_type` (`lottery_type`),
  KEY `idx_series_status` (`series_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系列表';

-- 创建期次表
DROP TABLE IF EXISTS `la_issue`;
CREATE TABLE `la_issue` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `series_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '系列ID',
  `issue_no` varchar(20) NOT NULL DEFAULT '' COMMENT '期号',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `content` text COMMENT '内容',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT '封面图片',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '单期价格',
  `issue_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '期次状态: 0-草稿, 1-已发布, 2-已开奖',
  `is_opened` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否已开奖: 0-否, 1-是',
  `open_code` varchar(50) NOT NULL DEFAULT '' COMMENT '开奖号码',
  `open_time` int(11) UNSIGNED DEFAULT NULL COMMENT '开奖时间',
  `publish_time` int(11) UNSIGNED DEFAULT NULL COMMENT '发布时间',
  `merchant_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '所属商户ID',
  `click_virtual` int(11) NOT NULL DEFAULT '0' COMMENT '虚拟浏览量',
  `click_actual` int(11) NOT NULL DEFAULT '0' COMMENT '实际浏览量',
  `create_time` int(11) UNSIGNED DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(11) UNSIGNED DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `idx_series_id` (`series_id`),
  KEY `idx_issue_no` (`issue_no`),
  KEY `idx_merchant_id` (`merchant_id`),
  KEY `idx_issue_status` (`issue_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='期次表';

-- 创建用户实名认证表
DROP TABLE IF EXISTS `la_user_realname`;
CREATE TABLE `la_user_realname` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
  `real_name` varchar(32) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `id_card` varchar(32) NOT NULL DEFAULT '' COMMENT '身份证号',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '状态: 0-待审核, 1-认证通过, 2-认证失败',
  `fail_reason` varchar(255) NOT NULL DEFAULT '' COMMENT '失败原因',
  `create_time` int(11) UNSIGNED DEFAULT NULL,
  `update_time` int(11) UNSIGNED DEFAULT NULL,
  `delete_time` int(11) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_id` (`user_id`),
  KEY `idx_create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户实名认证表';
