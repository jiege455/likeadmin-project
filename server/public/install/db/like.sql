SET NAMES utf8mb4;

-- Table structure for la_admin
DROP TABLE IF EXISTS `la_admin`;
CREATE TABLE `la_admin` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `root` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否超级管理员 0-否 1-是',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '名称',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '用户头像',
  `account` varchar(32) NOT NULL DEFAULT '' COMMENT '账号',
  `password` varchar(32) NOT NULL COMMENT '密码',
  `login_time` int(10) DEFAULT NULL COMMENT '最后登录时间',
  `login_ip` varchar(39) DEFAULT '' COMMENT '最后登录ip',
  `multipoint_login` tinyint(1) UNSIGNED DEFAULT '1' COMMENT '是否支持多处登录：1-是；0-否；',
  `disable` tinyint(1) UNSIGNED DEFAULT '0' COMMENT '是否禁用：0-否；1-是；',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理员表';

-- Table structure for la_admin_dept
DROP TABLE IF EXISTS `la_admin_dept`;
CREATE TABLE `la_admin_dept` (
`admin_id` int(10) NOT NULL DEFAULT '0' COMMENT '管理员id',
  `dept_id` int(10) NOT NULL DEFAULT '0' COMMENT '部门id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='部门关联表';

-- Table structure for la_admin_jobs
DROP TABLE IF EXISTS `la_admin_jobs`;
CREATE TABLE `la_admin_jobs` (
`admin_id` int(10) NOT NULL COMMENT '管理员id',
  `jobs_id` int(10) NOT NULL COMMENT '岗位id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='岗位关联表';

-- Table structure for la_admin_role
DROP TABLE IF EXISTS `la_admin_role`;
CREATE TABLE `la_admin_role` (
`admin_id` int(10) NOT NULL COMMENT '管理员id',
  `role_id` int(10) NOT NULL COMMENT '角色id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='角色关联表';

-- Table structure for la_admin_session
DROP TABLE IF EXISTS `la_admin_session`;
CREATE TABLE `la_admin_session` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) UNSIGNED NOT NULL COMMENT '用户id',
  `terminal` tinyint(1) NOT NULL DEFAULT '1' COMMENT '客户端类型：1-pc管理后台 2-mobile手机管理后台',
  `token` varchar(32) NOT NULL COMMENT '令牌',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `expire_time` int(10) NOT NULL COMMENT '到期时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理员会话表';

-- Table structure for la_article
DROP TABLE IF EXISTS `la_article`;
CREATE TABLE `la_article` (
`id` int(11) NOT NULL AUTO_INCREMENT COMMENT '文章id',
  `cid` int(11) NOT NULL COMMENT '文章分类',
  `title` varchar(255) NOT NULL COMMENT '文章标题',
  `desc` varchar(255) DEFAULT '' COMMENT '简介',
  `abstract` text COMMENT '文章摘要',
  `image` varchar(128) DEFAULT NULL COMMENT '文章图片',
  `author` varchar(255) DEFAULT '' COMMENT '作者',
  `content` text COMMENT '文章内容',
  `hidden_content` text COMMENT '付费隐藏内容',
  `click_virtual` int(10) DEFAULT '0' COMMENT '虚拟浏览量',
  `click_actual` int(11) DEFAULT '0' COMMENT '实际浏览量',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示:1-是.0-否',
  `sort` int(5) DEFAULT '0' COMMENT '排序',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(11) DEFAULT NULL COMMENT '删除时间',
  `merchant_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '所属商户ID',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '价格',
  `distribution_switch` tinyint(1) NOT NULL DEFAULT '1' COMMENT '分销开关:0-关闭,1-开启',
  `commission_ratio` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT '分销比例(%)',
  `audit_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '审核状态:0-待审核,1-通过,2-拒绝',
  `audit_reason` varchar(255) DEFAULT '' COMMENT '审核原因',
  `audit_time` int(11) DEFAULT NULL COMMENT '审核时间',
  `series_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '系列ID',
  `issue_no` varchar(20) NOT NULL DEFAULT '' COMMENT '期号',
  `is_opened` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否已开奖:0-否,1-是',
  `open_code` varchar(50) DEFAULT '' COMMENT '开奖号码',
  `open_time` int(11) DEFAULT NULL COMMENT '开奖时间',
  `issue_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '期次状态:0-草稿,1-已发布,2-已开奖',
  `tags` varchar(255) DEFAULT '' COMMENT '标签（逗号分隔）',
  PRIMARY KEY (`id`),
  KEY `idx_series_id` (`series_id`),
  KEY `idx_issue_no` (`issue_no`),
  KEY `idx_series_issue` (`series_id`, `issue_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文章表';

-- Table structure for la_article_cate
DROP TABLE IF EXISTS `la_article_cate`;
CREATE TABLE `la_article_cate` (
`id` int(11) NOT NULL AUTO_INCREMENT COMMENT '文章分类id',
  `name` varchar(90) DEFAULT NULL COMMENT '分类名称',
  `sort` int(11) DEFAULT '0' COMMENT '排序',
  `is_show` tinyint(1) DEFAULT '1' COMMENT '是否显示:1-是;0-否',
  `is_series` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否系列:0-否,1-是',
  `series_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '系列价格',
  `total_issues` int(11) NOT NULL DEFAULT '0' COMMENT '总期数',
  `auto_publish` tinyint(1) NOT NULL DEFAULT '0' COMMENT '自动发布:0-否,1-是',
  `publish_interval` int(11) NOT NULL DEFAULT '0' COMMENT '发布间隔(秒)',
  `series_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '系列状态:0-下架,1-上架',
  `lottery_type` varchar(50) DEFAULT '' COMMENT '彩票类型',
  `series_desc` text COMMENT '系列介绍',
  `merchant_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '所属商户ID',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `idx_merchant` (`merchant_id`),
  KEY `idx_series` (`is_series`, `series_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文章分类表';

-- Table structure for la_article_collect
DROP TABLE IF EXISTS `la_article_collect`;
CREATE TABLE `la_article_collect` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
  `article_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '文章ID',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '收藏状态 0-未收藏 1-已收藏',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文章收藏表';

-- Table structure for la_article_order
-- 开发者：杰哥网络科技 qq2711793818 杰哥
DROP TABLE IF EXISTS `la_article_order`;
CREATE TABLE `la_article_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `order_sn` varchar(32) NOT NULL DEFAULT '' COMMENT '订单编号',
  `pay_sn` varchar(64) NOT NULL DEFAULT '' COMMENT '支付编号',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `article_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '文章ID',
  `issue_no` varchar(20) NOT NULL DEFAULT '' COMMENT '购买时的期号',
  `merchant_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商家ID',
  `distributor_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分销员ID',
  `order_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '订单金额',
  `distribution_ratio` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT '分销比例(%)',
  `distribution_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '分销佣金',
  `platform_ratio` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT '平台抽成比例(%)',
  `platform_profit` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '平台收益',
  `merchant_profit` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商户收益',
  `pay_way` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '支付方式:1-微信支付,2-支付宝,3-余额支付',
  `pay_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '支付状态:0-未支付,1-已支付',
  `pay_time` int(11) unsigned DEFAULT NULL COMMENT '支付时间',
  `transaction_id` varchar(64) DEFAULT '' COMMENT '第三方支付流水号',
  `refund_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '退款状态:0-未退款,1-已退款',
  `refund_time` int(11) unsigned DEFAULT NULL COMMENT '退款时间',
  `refund_reason` varchar(255) NOT NULL DEFAULT '' COMMENT '退款原因',
  `create_time` int(11) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_order_sn` (`order_sn`),
  KEY `idx_user_article` (`user_id`,`article_id`),
  KEY `idx_merchant` (`merchant_id`),
  KEY `idx_distributor` (`distributor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文章购买订单表';

-- Table structure for la_article_tag
DROP TABLE IF EXISTS `la_article_tag`;
CREATE TABLE `la_article_tag` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '标签ID',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '标签名称',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建者ID（0=系统预留）',
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
  KEY `idx_user_id` (`user_id`),
  KEY `idx_article_count` (`article_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文章标签表';

-- Table structure for la_article_tag_relation
DROP TABLE IF EXISTS `la_article_tag_relation`;
CREATE TABLE `la_article_tag_relation` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `article_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '文章ID',
  `tag_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '标签ID',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_article_tag` (`article_id`, `tag_id`),
  KEY `idx_tag_id` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文章标签关联表';

-- Table structure for la_config
DROP TABLE IF EXISTS `la_config`;
CREATE TABLE `la_config` (
`id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(30) DEFAULT NULL COMMENT '类型',
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '名称',
  `value` text COMMENT '值',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='配置表';

-- Table structure for la_decorate_page
DROP TABLE IF EXISTS `la_decorate_page`;
CREATE TABLE `la_decorate_page` (
`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `type` tinyint(2) UNSIGNED NOT NULL DEFAULT '10' COMMENT '页面类型 1=商城首页, 2=个人中心, 3=客服设置 4-PC首页',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '页面名称',
  `data` text COMMENT '页面数据',
  `meta` text COMMENT '页面设置',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='装修页面配置表';

-- Table structure for la_decorate_tabbar
DROP TABLE IF EXISTS `la_decorate_tabbar`;
CREATE TABLE `la_decorate_tabbar` (
`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '导航名称',
  `selected` varchar(200) NOT NULL DEFAULT '' COMMENT '未选图标',
  `unselected` varchar(200) NOT NULL DEFAULT '' COMMENT '已选图标',
  `link` varchar(200) DEFAULT NULL COMMENT '链接地址',
  `is_show` tinyint(255) UNSIGNED NOT NULL DEFAULT '1' COMMENT '显示状态',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='装修底部导航表';

-- Table structure for la_dept
DROP TABLE IF EXISTS `la_dept`;
CREATE TABLE `la_dept` (
`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '部门名称',
  `pid` bigint(20) NOT NULL DEFAULT '0' COMMENT '上级部门id',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `leader` varchar(64) DEFAULT NULL COMMENT '负责人',
  `mobile` varchar(16) DEFAULT NULL COMMENT '联系电话',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '部门状态（0停用 1正常）',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='部门表';

-- Table structure for la_dev_crontab
DROP TABLE IF EXISTS `la_dev_crontab`;
CREATE TABLE `la_dev_crontab` (
`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(32) NOT NULL COMMENT '定时任务名称',
  `type` tinyint(1) NOT NULL COMMENT '类型 1-定时任务',
  `system` tinyint(4) DEFAULT '0' COMMENT '是否系统任务 0-否 1-是',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `command` varchar(64) NOT NULL COMMENT '命令内容',
  `params` varchar(64) DEFAULT '' COMMENT '参数',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 1-运行 2-停止 3-错误',
  `expression` varchar(64) NOT NULL COMMENT '运行规则',
  `error` varchar(256) DEFAULT NULL COMMENT '运行失败原因',
  `last_time` int(11) DEFAULT NULL COMMENT '最后执行时间',
  `time` varchar(64) DEFAULT '0' COMMENT '实时执行时长',
  `max_time` varchar(64) DEFAULT '0' COMMENT '最大执行时长',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='计划任务表';

-- Table structure for la_dev_pay_config
DROP TABLE IF EXISTS `la_dev_pay_config`;
CREATE TABLE `la_dev_pay_config` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '模版名称',
  `pay_way` tinyint(1) NOT NULL COMMENT '支付方式:1-余额支付;2-微信支付;3-支付宝支付;4-彩虹易支付;',
  `config` text COMMENT '对应支付配置(json字符串)',
  `icon` varchar(255) DEFAULT NULL COMMENT '图标',
  `sort` int(5) DEFAULT NULL COMMENT '排序',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='支付配置表';

-- Table structure for la_dev_pay_way
DROP TABLE IF EXISTS `la_dev_pay_way`;
CREATE TABLE `la_dev_pay_way` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `pay_config_id` int(11) NOT NULL COMMENT '支付配置ID',
  `scene` tinyint(1) NOT NULL COMMENT '场景:1-微信小程序;2-微信公众号;3-H5;4-PC;5-APP;',
  `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否默认支付:0-否;1-是;',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态:0-关闭;1-开启;',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='支付方式表';

-- Table structure for la_dict_data
DROP TABLE IF EXISTS `la_dict_data`;
CREATE TABLE `la_dict_data` (
`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(255) NOT NULL COMMENT '数据名称',
  `value` varchar(255) NOT NULL COMMENT '数据值',
  `type_id` int(11) NOT NULL COMMENT '字典类型id',
  `type_value` varchar(255) NOT NULL COMMENT '字典类型',
  `sort` int(10) DEFAULT '0' COMMENT '排序值',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 0-停用 1-正常',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='字典数据表';

-- Table structure for la_dict_type
DROP TABLE IF EXISTS `la_dict_type`;
CREATE TABLE `la_dict_type` (
`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '字典名称',
  `type` varchar(255) NOT NULL DEFAULT '' COMMENT '字典类型名称',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 0-停用 1-正常',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='字典类型表';

-- Table structure for la_distribution_apply
DROP TABLE IF EXISTS `la_distribution_apply`;
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

-- Table structure for la_distribution_log
DROP TABLE IF EXISTS `la_distribution_log`;
CREATE TABLE `la_distribution_log` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '获得佣金的用户ID(推广员)',
  `source_user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '贡献佣金的用户ID(下单人)',
  `order_id` int(11) DEFAULT '0' COMMENT '关联订单ID',
  `order_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '订单类型:1-文章订单',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '佣金金额',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态:0-待结算,1-已结算',
  `create_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_order` (`order_id`, `order_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='分销记录表';

-- Table structure for la_file
DROP TABLE IF EXISTS `la_file`;
CREATE TABLE `la_file` (
`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `cid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '类目ID',
  `source_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '上传者id',
  `source` tinyint(1) NOT NULL DEFAULT '0' COMMENT '来源类型[0-后台,1-用户]',
  `type` tinyint(2) UNSIGNED NOT NULL DEFAULT '10' COMMENT '类型[10=图片, 20=视频]',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '文件名称',
  `uri` varchar(200) NOT NULL COMMENT '文件路径',
  `create_time` int(10) UNSIGNED DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文件表';

-- Table structure for la_file_cate
DROP TABLE IF EXISTS `la_file_cate`;
CREATE TABLE `la_file_cate` (
`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父级ID',
  `type` tinyint(2) UNSIGNED NOT NULL DEFAULT '10' COMMENT '类型[10=图片，20=视频，30=文件]',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '分类名称',
  `create_time` int(10) UNSIGNED DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) UNSIGNED DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文件分类表';

-- Table structure for la_generate_column
DROP TABLE IF EXISTS `la_generate_column`;
CREATE TABLE `la_generate_column` (
`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `table_id` int(11) NOT NULL DEFAULT '0' COMMENT '表id',
  `column_name` varchar(100) NOT NULL DEFAULT '' COMMENT '字段名称',
  `column_comment` varchar(300) NOT NULL DEFAULT '' COMMENT '字段描述',
  `column_type` varchar(100) NOT NULL DEFAULT '' COMMENT '字段类型',
  `is_required` tinyint(1) DEFAULT '0' COMMENT '是否必填 0-非必填 1-必填',
  `is_pk` tinyint(1) DEFAULT '0' COMMENT '是否为主键 0-不是 1-是',
  `is_insert` tinyint(1) DEFAULT '0' COMMENT '是否为插入字段 0-不是 1-是',
  `is_update` tinyint(1) DEFAULT '0' COMMENT '是否为更新字段 0-不是 1-是',
  `is_lists` tinyint(1) DEFAULT '0' COMMENT '是否为列表字段 0-不是 1-是',
  `is_query` tinyint(1) DEFAULT '0' COMMENT '是否为查询字段 0-不是 1-是',
  `query_type` varchar(100) DEFAULT '=' COMMENT '查询类型',
  `view_type` varchar(100) DEFAULT 'input' COMMENT '显示类型',
  `dict_type` varchar(255) DEFAULT '' COMMENT '字典类型',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='代码生成表字段信息表';

-- Table structure for la_generate_table
DROP TABLE IF EXISTS `la_generate_table`;
CREATE TABLE `la_generate_table` (
`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `table_name` varchar(200) NOT NULL DEFAULT '' COMMENT '表名称',
  `table_comment` varchar(300) NOT NULL DEFAULT '' COMMENT '表描述',
  `template_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '模板类型 0-单表(curd) 1-树表(curd)',
  `author` varchar(100) DEFAULT '' COMMENT '作者',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `generate_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '生成方式  0-压缩包下载 1-生成到模块',
  `module_name` varchar(100) DEFAULT '' COMMENT '模块名',
  `class_dir` varchar(100) DEFAULT '' COMMENT '类目录名',
  `class_comment` varchar(100) DEFAULT '' COMMENT '类描述',
  `admin_id` int(11) DEFAULT '0' COMMENT '管理员id',
  `menu` text COMMENT '菜单配置',
  `delete` text COMMENT '删除配置',
  `tree` text COMMENT '树表配置',
  `relations` text COMMENT '关联配置',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='代码生成表信息表';

-- Table structure for la_hot_search
DROP TABLE IF EXISTS `la_hot_search`;
CREATE TABLE `la_hot_search` (
`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(200) NOT NULL DEFAULT '' COMMENT '关键词',
  `sort` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '排序号',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='热门搜索表';

-- Table structure for la_jobs
DROP TABLE IF EXISTS `la_jobs`;
CREATE TABLE `la_jobs` (
`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(50) NOT NULL COMMENT '岗位名称',
  `code` varchar(64) NOT NULL COMMENT '岗位编码',
  `sort` int(11) DEFAULT '0' COMMENT '显示顺序',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态（0停用 1正常）',
  `remark` varchar(500) DEFAULT NULL COMMENT '备注',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='岗位表';

-- Table structure for la_merchant
DROP TABLE IF EXISTS `la_merchant`;
CREATE TABLE `la_merchant` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '关联用户ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '商户名称',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT '商户头像',
  `cover` varchar(255) NOT NULL DEFAULT '' COMMENT '店铺封面',
  `logo` varchar(255) NOT NULL DEFAULT '' COMMENT '店铺Logo',
  `desc` varchar(255) DEFAULT '' COMMENT '商户简介',
  `intro` varchar(500) DEFAULT '' COMMENT '店铺简介',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '联系电话',
  `wechat` varchar(50) DEFAULT '' COMMENT '微信号',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT '商家邮箱',
  `email_verify` tinyint(1) NOT NULL DEFAULT 0 COMMENT '邮箱验证状态:0-未验证,1-已验证',
  `email_notify` tinyint(1) NOT NULL DEFAULT 1 COMMENT '邮件通知开关:0-关闭,1-开启',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '当前余额',
  `total_income` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '累计收入',
  `distribution_switch` tinyint(1) NOT NULL DEFAULT '1' COMMENT '分销开关:0-关闭,1-开启',
  `distribution_ratio` decimal(5,2) NOT NULL DEFAULT '10.00' COMMENT '默认分销比例(%)',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态:0-待审核,1-正常,2-已拒绝,3-已禁用',
  `audit_time` int(10) DEFAULT NULL COMMENT '审核时间',
  `audit_reason` varchar(255) DEFAULT '' COMMENT '审核原因',
  `create_time` int(10) DEFAULT NULL,
  `update_time` int(10) DEFAULT NULL,
  `delete_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户表';

-- Table structure for la_merchant_apply
DROP TABLE IF EXISTS `la_merchant_apply`;
CREATE TABLE `la_merchant_apply` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '申请用户ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '商户名称',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '联系电话',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT '邮箱',
  `email_code` varchar(10) NOT NULL DEFAULT '' COMMENT '邮箱验证码',
  `wechat` varchar(50) DEFAULT '' COMMENT '微信号',
  `desc` varchar(255) DEFAULT '' COMMENT '简介',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态:0-待审核,1-通过,2-拒绝',
  `audit_remark` varchar(255) DEFAULT '' COMMENT '审核备注',
  `audit_time` int(10) DEFAULT NULL COMMENT '审核时间',
  `create_time` int(10) DEFAULT NULL,
  `update_time` int(10) DEFAULT NULL,
  `delete_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户入驻申请表';



-- Table structure for la_merchant_complaint
DROP TABLE IF EXISTS `la_merchant_complaint`;
CREATE TABLE `la_merchant_complaint` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '举报类型:1=商家,2=文章',
  `target_id` int(11) NOT NULL DEFAULT 0 COMMENT '目标ID(商家ID或文章ID)',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `merchant_id` int(11) NOT NULL DEFAULT 0 COMMENT '商家ID',
  `content` text NOT NULL COMMENT '举报内容',
  `reason` varchar(100) DEFAULT '' COMMENT '举报原因',
  `images` text COMMENT '图片凭证',
  `contact` varchar(50) DEFAULT '' COMMENT '联系方式',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态:0=待处理,1=已处理',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `delete_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `merchant_id` (`merchant_id`),
  KEY `idx_type_target` (`type`, `target_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户举报表';

-- Table structure for la_merchant_income_log
DROP TABLE IF EXISTS `la_merchant_income_log`;
CREATE TABLE `la_merchant_income_log` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `merchant_id` int(11) UNSIGNED NOT NULL COMMENT '商户ID',
  `source_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '来源类型:1-文章,2-课程',
  `source_id` int(11) NOT NULL DEFAULT '0' COMMENT '来源ID(文章ID或课程ID)',
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变动金额',
  `platform_ratio` decimal(5,2) DEFAULT '0.00' COMMENT '平台抽成比例%',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `create_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户资金明细表';

-- Table structure for la_merchant_follow
DROP TABLE IF EXISTS `la_merchant_follow`;
CREATE TABLE `la_merchant_follow` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
  `merchant_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商家ID',
  `create_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `merchant_id` (`merchant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商家关注表';

-- Table structure for la_notice_record
DROP TABLE IF EXISTS `la_notice_record`;
CREATE TABLE `la_notice_record` (
`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '用户id',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `content` text NOT NULL COMMENT '内容',
  `scene_id` int(10) UNSIGNED DEFAULT '0' COMMENT '场景',
  `read` tinyint(1) DEFAULT '0' COMMENT '已读状态;0-未读,1-已读',
  `recipient` tinyint(1) DEFAULT '0' COMMENT '通知接收对象类型;1-会员;2-商家;3-平台;4-游客(未注册用户)',
  `send_type` tinyint(1) DEFAULT '0' COMMENT '通知发送类型 1-系统通知 2-短信通知 3-微信模板 4-微信小程序',
  `notice_type` tinyint(1) DEFAULT NULL COMMENT '通知类型 1-业务通知 2-验证码',
  `extra` varchar(255) DEFAULT '' COMMENT '其他',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='通知记录表';

-- Table structure for la_notice_setting
DROP TABLE IF EXISTS `la_notice_setting`;
CREATE TABLE `la_notice_setting` (
`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `scene_id` int(10) NOT NULL COMMENT '场景id',
  `scene_name` varchar(255) NOT NULL DEFAULT '' COMMENT '场景名称',
  `scene_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '场景描述',
  `recipient` tinyint(1) NOT NULL DEFAULT '1' COMMENT '接收者 1-用户 2-平台',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '通知类型: 1-业务通知 2-验证码',
  `system_notice` text COMMENT '系统通知设置',
  `sms_notice` text COMMENT '短信通知设置',
  `oa_notice` text COMMENT '公众号通知设置',
  `mnp_notice` text COMMENT '小程序通知设置',
  `support` char(10) NOT NULL DEFAULT '' COMMENT '支持的发送类型 1-系统通知 2-短信通知 3-微信模板消息 4-小程序提醒',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='通知设置表';

-- Table structure for la_official_account_reply
DROP TABLE IF EXISTS `la_official_account_reply`;
CREATE TABLE `la_official_account_reply` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '规则名称',
  `keyword` varchar(64) NOT NULL DEFAULT '' COMMENT '关键词',
  `reply_type` tinyint(1) NOT NULL COMMENT '回复类型 1-关注回复 2-关键字回复 3-默认回复',
  `matching_type` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '匹配方式：1-全匹配；2-模糊匹配',
  `content_type` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '内容类型：1-文本',
  `content` text NOT NULL COMMENT '回复内容',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '启动状态：1-启动；0-关闭',
  `sort` int(11) UNSIGNED NOT NULL DEFAULT '50' COMMENT '排序',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='公众号消息回调表';

-- Table structure for la_operation_log
DROP TABLE IF EXISTS `la_operation_log`;
CREATE TABLE `la_operation_log` (
`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `admin_id` int(11) NOT NULL COMMENT '管理员ID',
  `admin_name` varchar(16) NOT NULL DEFAULT '' COMMENT '管理员名称',
  `account` varchar(16) NOT NULL DEFAULT '' COMMENT '管理员账号',
  `action` varchar(64) DEFAULT '' COMMENT '操作名称',
  `type` varchar(8) NOT NULL COMMENT '请求方式',
  `url` varchar(600) NOT NULL COMMENT '访问链接',
  `params` text COMMENT '请求数据',
  `result` text COMMENT '请求结果',
  `ip` varchar(39) NOT NULL DEFAULT '' COMMENT 'ip地址',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统日志表';

-- Table structure for la_recharge_order
DROP TABLE IF EXISTS `la_recharge_order`;
CREATE TABLE `la_recharge_order` (
`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `sn` varchar(64) NOT NULL COMMENT '订单编号',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `pay_sn` varchar(255) DEFAULT '' COMMENT '支付编号-冗余字段，针对微信同一主体不同客户端支付需用不同订单号预留。',
  `pay_way` tinyint(2) NOT NULL DEFAULT '2' COMMENT '支付方式 2-微信支付 3-支付宝支付',
  `pay_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支付状态：0-待支付；1-已支付',
  `pay_time` int(10) DEFAULT NULL COMMENT '支付时间',
  `order_amount` decimal(10,2) NOT NULL COMMENT '充值金额',
  `order_terminal` tinyint(1) DEFAULT '1' COMMENT '终端 1-PC端 2-移动端 3-小程序',
  `transaction_id` varchar(128) DEFAULT NULL COMMENT '第三方平台交易流水号',
  `refund_status` tinyint(1) DEFAULT '0' COMMENT '退款状态 0-未退款 1-已退款',
  `refund_transaction_id` varchar(255) DEFAULT NULL COMMENT '退款交易流水号',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='充值订单表';

-- Table structure for la_refund_log
DROP TABLE IF EXISTS `la_refund_log`;
CREATE TABLE `la_refund_log` (
`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `sn` varchar(32) DEFAULT NULL COMMENT '编号',
  `record_id` int(11) NOT NULL COMMENT '退款记录id',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '关联用户',
  `handle_id` int(11) NOT NULL DEFAULT '0' COMMENT '处理人id（管理员id）',
  `order_amount` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '订单总的应付款金额，冗余字段',
  `refund_amount` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '本次退款金额',
  `refund_status` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '退款状态 0-退款中 1-退款成功 2-退款失败',
  `refund_msg` text COMMENT '退款信息',
  `create_time` int(10) UNSIGNED DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='退款日志表';

-- Table structure for la_refund_record
DROP TABLE IF EXISTS `la_refund_record`;
CREATE TABLE `la_refund_record` (
`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `sn` varchar(32) NOT NULL DEFAULT '' COMMENT '退款编号',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '关联用户',
  `order_id` int(11) NOT NULL DEFAULT '0' COMMENT '来源订单id',
  `order_sn` varchar(32) NOT NULL COMMENT '来源单号',
  `order_type` varchar(255) DEFAULT 'order' COMMENT '订单来源 order-商品订单 recharge-充值订单',
  `order_amount` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '订单总的应付款金额，冗余字段',
  `refund_amount` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '本次退款金额',
  `transaction_id` varchar(255) DEFAULT NULL COMMENT '第三方平台交易流水号',
  `refund_way` tinyint(1) NOT NULL DEFAULT '1' COMMENT '退款方式 1-线上退款 2-线下退款',
  `refund_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '退款类型 1-后台退款',
  `refund_status` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '退款状态 0-退款中 1-退款成功 2-退款失败',
  `create_time` int(10) UNSIGNED DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='退款记录表';

-- Table structure for la_sms_log
DROP TABLE IF EXISTS `la_sms_log`;
CREATE TABLE `la_sms_log` (
`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `scene_id` int(11) NOT NULL COMMENT '场景id',
  `mobile` varchar(11) NOT NULL COMMENT '手机号码',
  `content` varchar(255) NOT NULL COMMENT '发送内容',
  `code` varchar(32) DEFAULT NULL COMMENT '发送关键字（注册、找回密码）',
  `is_verify` tinyint(1) DEFAULT '0' COMMENT '是否已验证；0-否；1-是',
  `check_num` int(5) DEFAULT '0' COMMENT '验证次数',
  `send_status` tinyint(1) NOT NULL COMMENT '发送状态：0-发送中；1-发送成功；2-发送失败',
  `send_time` int(10) NOT NULL COMMENT '发送时间',
  `results` text COMMENT '短信结果',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='短信记录表';

-- Table structure for la_system_menu
DROP TABLE IF EXISTS `la_system_menu`;
CREATE TABLE `la_system_menu` (
`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `pid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '上级菜单',
  `type` char(2) NOT NULL DEFAULT '' COMMENT '权限类型: M=目录，C=菜单，A=按钮',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '菜单名称',
  `icon` varchar(100) NOT NULL DEFAULT '' COMMENT '菜单图标',
  `sort` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '菜单排序',
  `perms` varchar(100) NOT NULL DEFAULT '' COMMENT '权限标识',
  `paths` varchar(100) NOT NULL DEFAULT '' COMMENT '路由地址',
  `component` varchar(200) NOT NULL DEFAULT '' COMMENT '前端组件',
  `selected` varchar(200) NOT NULL DEFAULT '' COMMENT '选中路径',
  `params` varchar(200) NOT NULL DEFAULT '' COMMENT '路由参数',
  `is_cache` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否缓存: 0=否, 1=是',
  `is_show` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '是否显示: 0=否, 1=是',
  `is_disable` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否禁用: 0=否, 1=是',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统菜单表';

-- Table structure for la_system_notice
DROP TABLE IF EXISTS `la_system_notice`;
CREATE TABLE `la_system_notice` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '公告标题',
  `content` text COMMENT '公告内容',
  `recipient` tinyint(1) NOT NULL DEFAULT '1' COMMENT '接收对象:1-全员,2-仅商户',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态:1-显示,0-隐藏',
  `popup_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '弹出频率:1-每天一次,2-每次弹出',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公告类型:1-普通,2-重要,3-活动',
  `is_top` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否置顶:0-否,1-是',
  `cover` varchar(255) NOT NULL DEFAULT '' COMMENT '封面图片',
  `views` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '阅读量',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `create_time` int(10) DEFAULT NULL,
  `update_time` int(10) DEFAULT NULL,
  `delete_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统公告表';

-- Table structure for la_notice_read
DROP TABLE IF EXISTS `la_notice_read`;
CREATE TABLE `la_notice_read` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `notice_id` int(11) UNSIGNED NOT NULL COMMENT '公告ID',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `create_time` int(10) DEFAULT NULL COMMENT '阅读时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_notice_user` (`notice_id`, `user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='公告阅读记录表';

-- Table structure for la_system_role
DROP TABLE IF EXISTS `la_system_role`;
CREATE TABLE `la_system_role` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(16) NOT NULL DEFAULT '' COMMENT '名称',
  `desc` varchar(128) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '描述',
  `sort` int(11) DEFAULT '0' COMMENT '排序',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='角色表';

-- Table structure for la_system_role_menu
DROP TABLE IF EXISTS `la_system_role_menu`;
CREATE TABLE `la_system_role_menu` (
`role_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '角色ID',
  `menu_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '菜单ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='角色菜单关系表';

-- Table structure for la_user
DROP TABLE IF EXISTS `la_user`;
CREATE TABLE `la_user` (
`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `inviter_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '邀请人ID',
  `current_merchant_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '当前显示的商家ID',
  `commission` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '当前可提现佣金',
  `total_commission` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '累计获得佣金',
  `sn` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '编号',
  `avatar` varchar(200) NOT NULL DEFAULT '' COMMENT '头像',
  `real_name` varchar(32) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `nickname` varchar(32) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `account` varchar(32) NOT NULL DEFAULT '' COMMENT '用户账号',
  `password` varchar(32) NOT NULL DEFAULT '' COMMENT '用户密码',
  `mobile` varchar(32) NOT NULL DEFAULT '' COMMENT '用户电话',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT '邮箱',
  `email_verify` tinyint(1) NOT NULL DEFAULT 0 COMMENT '邮箱验证状态:0-未验证,1-已验证',
  `sex` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户性别: [1=男, 2=女]',
  `channel` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '注册渠道: [1-微信小程序 2-微信公众号 3-手机H5 4-电脑PC 5-苹果APP 6-安卓APP]',
  `is_disable` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否禁用: [0=否, 1=是]',
  `login_ip` varchar(200) NOT NULL DEFAULT '' COMMENT '最后登录IP',
  `login_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `is_new_user` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否是新注册用户: [1-是, 0-否]',
  `user_money` decimal(10,2) UNSIGNED DEFAULT '0.00' COMMENT '用户余额',
  `total_recharge_amount` decimal(10,2) UNSIGNED DEFAULT '0.00' COMMENT '累计充值',
  `is_distributor` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否分销员: [0=否, 1=是]',
  `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) UNSIGNED DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户表';

-- Table structure for la_user_account_log
DROP TABLE IF EXISTS `la_user_account_log`;
CREATE TABLE `la_user_account_log` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `sn` varchar(32) NOT NULL DEFAULT '' COMMENT '流水号',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `change_object` tinyint(1) NOT NULL DEFAULT '0' COMMENT '变动对象',
  `change_type` smallint(5) NOT NULL COMMENT '变动类型',
  `action` tinyint(1) NOT NULL DEFAULT '0' COMMENT '动作 1-增加 2-减少',
  `change_amount` decimal(10,2) NOT NULL COMMENT '变动数量',
  `left_amount` decimal(10,2) NOT NULL DEFAULT '100.00' COMMENT '变动后数量',
  `source_sn` varchar(255) DEFAULT NULL COMMENT '关联单号',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `extra` text COMMENT '预留扩展字段',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户账户变动日志表';

-- Table structure for la_user_auth
DROP TABLE IF EXISTS `la_user_auth`;
CREATE TABLE `la_user_auth` (
`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `openid` varchar(128) NOT NULL COMMENT '微信openid',
  `unionid` varchar(128) DEFAULT '' COMMENT '微信unionid',
  `terminal` tinyint(1) NOT NULL DEFAULT '1' COMMENT '客户端类型：1-微信小程序；2-微信公众号；3-手机H5；4-电脑PC；5-苹果APP；6-安卓APP',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户授权表';

-- Table structure for la_coupon
DROP TABLE IF EXISTS `la_coupon`;
CREATE TABLE `la_coupon` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '优惠券ID',
  `merchant_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商户ID，0为平台优惠券',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '优惠券名称',
  `money` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '优惠金额',
  `condition_money` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '使用门槛金额，0为无门槛',
  `total_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '发放总量',
  `send_count` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '已领取数量',
  `use_time_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '有效期类型：1=固定时间，2=领券后天数',
  `use_time_start` int(10) UNSIGNED DEFAULT NULL COMMENT '有效期开始时间（时间戳）',
  `use_time_end` int(10) UNSIGNED DEFAULT NULL COMMENT '有效期结束时间（时间戳）',
  `use_days` int(11) UNSIGNED DEFAULT NULL COMMENT '领券后有效天数',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：0=停用，1=正常',
  `create_time` int(10) UNSIGNED DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) UNSIGNED DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `idx_merchant_id` (`merchant_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='优惠券表';

-- Table structure for la_user_coupon
DROP TABLE IF EXISTS `la_user_coupon`;
CREATE TABLE `la_user_coupon` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `coupon_id` int(11) UNSIGNED NOT NULL COMMENT '优惠券ID',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '优惠金额',
  `condition_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '使用门槛',
  `use_time_start` int(10) DEFAULT NULL COMMENT '有效期开始',
  `use_time_end` int(10) DEFAULT NULL COMMENT '有效期结束',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态:0-未使用,1-已使用,2-已过期',
  `use_time` int(10) DEFAULT NULL COMMENT '使用时间',
  `create_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户优惠券记录表';

-- Table structure for la_user_follow_merchant
DROP TABLE IF EXISTS `la_user_follow_merchant`;
CREATE TABLE `la_user_follow_merchant` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `merchant_id` int(11) UNSIGNED NOT NULL COMMENT '商户ID',
  `is_push` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否接收推送:1-是,0-否',
  `create_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户关注商户表';

-- Table structure for la_user_merchant
DROP TABLE IF EXISTS `la_user_merchant`;
CREATE TABLE `la_user_merchant` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `merchant_id` int(11) NOT NULL DEFAULT '0' COMMENT '商家ID',
  `inviter_id` int(11) NOT NULL DEFAULT '0' COMMENT '邀请人ID',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户商家绑定关系表';

-- Table structure for la_user_session
DROP TABLE IF EXISTS `la_user_session`;
CREATE TABLE `la_user_session` (
`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `terminal` tinyint(1) NOT NULL DEFAULT '1' COMMENT '客户端类型：1-微信小程序；2-微信公众号；3-手机H5；4-电脑PC；5-苹果APP；6-安卓APP',
  `token` varchar(32) NOT NULL COMMENT '令牌',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `expire_time` int(10) NOT NULL COMMENT '到期时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户会话表';

-- Table structure for la_withdraw_apply
DROP TABLE IF EXISTS `la_withdraw_apply`;
CREATE TABLE `la_withdraw_apply` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID(推广员)',
  `merchant_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商户ID',
  `source` tinyint(1) NOT NULL DEFAULT '1' COMMENT '来源:1-商户收益,2-推广佣金',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '提现金额',
  `left_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '提现后余额',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '提现方式:1-微信,2-支付宝,3-银行卡',
  `account_info` text COMMENT '账户信息JSON',
  `bank_name` varchar(100) DEFAULT '' COMMENT '银行名称',
  `bank_branch` varchar(100) DEFAULT '' COMMENT '开户支行',
  `bank_account` varchar(50) DEFAULT '' COMMENT '银行账号',
  `bank_user` varchar(50) DEFAULT '' COMMENT '持卡人',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态:0-待审核,1-已拒绝,2-已通过,3-已打款',
  `audit_time` int(10) DEFAULT NULL COMMENT '审核时间',
  `audit_reason` varchar(255) DEFAULT '' COMMENT '拒绝原因',
  `create_time` int(10) DEFAULT NULL,
  `update_time` int(10) DEFAULT NULL,
  `delete_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_merchant_id` (`merchant_id`),
  KEY `idx_source` (`source`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='提现申请表';

-- Table structure for la_withdraw_account
DROP TABLE IF EXISTS `la_withdraw_account`;
CREATE TABLE `la_withdraw_account` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID（推广员）',
  `merchant_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商户ID',
  `type` tinyint(1) NOT NULL DEFAULT '2' COMMENT '账户类型：1-微信，2-支付宝，3-银行卡',
  `account` varchar(100) NOT NULL DEFAULT '' COMMENT '账号',
  `real_name` varchar(50) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `bank_name` varchar(100) DEFAULT '' COMMENT '银行名称',
  `bank_branch` varchar(100) DEFAULT '' COMMENT '开户支行',
  `qrcode` varchar(255) DEFAULT '' COMMENT '收款码图片',
  `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否默认：0-否，1-是',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：0-禁用，1-启用',
  `create_time` int(10) DEFAULT NULL,
  `update_time` int(10) DEFAULT NULL,
  `delete_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_merchant_id` (`merchant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='收款账户表';

-- ----------------------------
-- Records of la_article
-- ----------------------------
BEGIN;
INSERT INTO `la_article` (`id`, `cid`, `title`, `desc`, `abstract`, `image`, `author`, `content`, `click_virtual`, `click_actual`, `is_show`, `sort`, `create_time`, `update_time`, `delete_time`, `merchant_id`, `price`, `commission_ratio`, `audit_status`) VALUES (1, 3, '让生活更精致！五款居家好物推荐，实用性超高', '##好物推荐🔥', '随着当代生活节奏的忙碌，很多人在闲暇之余都想好好的享受生活。随着科技的发展，也出现了越来越多可以帮助我们提升幸福感，让生活变得更精致的产品，下面周周就给大家盘点五款居家必备的好物，都是实用性很高的产品，周周可以保证大家买了肯定会喜欢。', 'resource/image/adminapi/default/article01.png', '红花', '<p>拥有一台投影仪，闲暇时可以在家里直接看影院级别的大片，光是想想都觉得超级爽。市面上很多投影仪大几千，其实周周觉得没必要，选泰捷这款一千多的足够了，性价比非常高。</p><p>泰捷的专业度很高，在电视TV领域研发已经十年，有诸多专利和技术创新，荣获国内外多项技术奖项，拿下了腾讯创新工场投资，打造的泰捷视频TV端和泰捷电视盒子都获得了极高评价。</p><p>这款投影仪的分辨率在3000元内无敌，做到了真1080P高分辨率，也就是跟市场售价三千DLP投影仪一样的分辨率，真正做到了分毫毕现，像桌布的花纹、天空的云彩等，这些细节都清晰可见。</p><p>亮度方面，泰捷达到了850ANSI流明，同价位一般是200ANSI。这是因为泰捷为了提升亮度和LCD技术透射率低的问题，首创高功率LED灯源，让其亮度做到同价位最好。专业媒体也进行了多次对比，效果与3000元价位投影仪相当。</p><p>操作系统周周也很喜欢，完全不卡。泰捷作为资深音视频品牌，在系统优化方面有十年的研发经验，打造出的“零极”系统是业内公认效率最高、速度最快的系统，用户也评价它流畅度能一台顶三台，而且为了解决行业广告多这一痛点，系统内不植入任何广告。</p>', 1, 2, 1, 0, 1663317759, 1727070911, NULL, 0, 0.00, 0.00, 1), (2, 2, '埋葬UI设计师的坟墓不是内卷，而是免费模式', '', '本文从另外一个角度，聊聊作者对UI设计师职业发展前景的担忧，欢迎从事UI设计的同学来参与讨论，会有赠书哦', 'resource/image/adminapi/default/article02.jpeg', '小明', '<p><br></p><p style=\"text-align: justify;\">一个职业，卷，根本就没什么大不了的，尤其是成熟且收入高的职业，不卷才不符合事物发展的规律。何况 UI 设计师的人力市场到今天也和 5 年前一样，还是停留在大型菜鸡互啄的场面。远不能和医疗、证券、教师或者演艺练习生相提并论。</p><p style=\"text-align: justify;\">真正会让我对UI设计师发展前景觉得悲观的事情就只有一件 —— 国内的互联网产品免费机制。这也是一个我一直以来想讨论的话题，就在这次写一写。</p><p style=\"text-align: justify;\">国内互联网市场的发展，是一部浩瀚的 “免费经济” 发展史。虽然今天免费已经是深入国内民众骨髓的认知，但最早的中文互联网也是需要付费的，网游也都是要花钱的。</p><p style=\"text-align: justify;\">只是自有国情在此，付费确实阻碍了互联网行业的扩张和普及，一批创业家就开始通过免费的模式为用户提供服务，从而扩大了自己的产品覆盖面和普及程度。</p><p style=\"text-align: justify;\">印象最深的就是免费急先锋周鸿祎，和现在鲜少出现在公众视野不同，一零年前他是当之无愧的互联网教主，因为他开发出了符合中国国情的互联网产品 “打法”，让 360 的发展如日中天。</p><p style=\"text-align: justify;\">就是他在自传中提到：</p><p style=\"text-align: justify;\">只要是在互联网上每个人都需要的服务，我们就认为它是基础服务，基础服务一定是免费的，这样的话不会形成价值歧视。就是说，只要这种服务是每个人都一定要用的，我一定免费提供，而且是无条件免费。增值服务不是所有人都需要的，这个比例可能会相当低，它只是百分之几甚至更少比例的人需要，所以这种服务一定要收费……</p><p style=\"text-align: justify;\">这就是互联网的游戏规则，它决定了要想建立一个有效的商业模式，就一定要有海量的用户基数……</p>', 2, 4, 1, 0, 1663322854, 1727071178, NULL, 0, 0.00, 0.00, 1), (3, 1, '金山电池公布“沪广深市民绿色生活方式”调查结果', '', '60%以上受访者认为高质量的10分钟足以完成“自我充电”', 'resource/image/adminapi/default/article03.png', '中网资讯科技', '<p style=\"text-align: left;\"><strong>深圳，2021年10月22日）</strong>生活在一线城市的沪广深市民一向以效率见称，工作繁忙和快节奏的生活容易缺乏充足的休息。近日，一项针对沪广深市民绿色生活方式而展开的网络问卷调查引起了大家的注意。问卷的问题设定集中于市民对休息时间的看法，以及从对循环充电电池的使用方面了解其对绿色生活方式的态度。该调查采用随机抽样的模式，并对最终收集的1,500份有效问卷进行专业分析后发现，超过60%的受访者表示，在每天的工作时段能拥有10分钟高质量的休息时间，就可以高效“自我充电”。该调查结果反映出，在快节奏时代下，人们需要高质量的休息时间，也要学会利用高效率的休息方式和工具来应对快节奏的生活，以时刻保持“满电”状态。</p><p style=\"text-align: left;\">　　<strong>60%以上受访者认为高质量的10分钟足以完成“自我充电”</strong></p><p style=\"text-align: left;\">　　这次调查超过1,500人，主要聚焦18至85岁的沪广深市民，了解他们对于休息时间的观念及使用充电电池的习惯，结果发现：</p><p style=\"text-align: left;\">　　· 90%以上有工作受访者每天工作时间在7小时以上，平均工作时间为8小时，其中43%以上的受访者工作时间超过9小时</p><p style=\"text-align: left;\">　　· 70%受访者认为在工作期间拥有10分钟“自我充电”时间不是一件困难的事情</p><p style=\"text-align: left;\">　　· 60%受访者认为在工作期间有10分钟休息时间足以为自己快速充电</p><p style=\"text-align: left;\">　　临床心理学家黄咏诗女士在发布会上分享为自己快速充电的实用技巧，她表示：“事实上，只要选择正确的休息方法，10分钟也足以为自己充电。以喝咖啡为例，我们可以使用心灵休息法 ── 静观呼吸，慢慢感受咖啡的温度和气味，如果能配合着聆听流水或海洋的声音，能够有效放松大脑及心灵。”</p><p style=\"text-align: left;\">　　这次调查结果反映出沪广深市民的希望在繁忙的工作中适时停下来，抽出10分钟喝杯咖啡、聆听音乐或小睡片刻，为自己充电。金山电池全新推出的“绿再十分充”超快速充电器仅需10分钟就能充好电，喝一杯咖啡的时间既能完成“自我充电”，也满足设备使用的用电需求，为提升工作效率和放松身心注入新能量。</p><p style=\"text-align: left;\">　　<strong>金山电池推出10分钟超快电池充电器*绿再十分充，以创新科技为市场带来革新体验</strong></p><p style=\"text-align: left;\">　　该问卷同时从沪广深市民对循环充电电池的使用方面进行了调查，以了解其对绿色生活方式的态度：</p><p style=\"text-align: left;\">　　· 87%受访者目前没有使用充电电池，其中61%表示会考虑使用充电电池</p><p style=\"text-align: left;\">　　· 58%受访者过往曾使用过充电电池，却只有20%左右市民仍在使用</p><p style=\"text-align: left;\">　　· 60%左右受访者认为充电电池尚未被广泛使用，主要障碍来自于充电时间过长、缺乏相关教育</p><p style=\"text-align: left;\">　　· 90%以上受访者认为充电电池充满电需要1小时或更长的时间</p><p style=\"text-align: left;\">　　金山电池一直致力于为大众提供安全可靠的充电电池，并与消费者的需求和生活方式一起演变及进步。今天，金山电池宣布推出10分钟超快电池充电器*绿再十分充，只需10分钟*即可将4粒绿再十分充充电电池充好电，充电速度比其他品牌提升3倍**。充电器的LED灯可以显示每粒电池的充电状态和模式，并提示用户是否错误插入已损坏电池或一次性电池。尽管其体型小巧，却具备多项创新科技 ，如拥有独特的充电算法以优化充电电流，并能根据各个电池类型、状况和温度用最短的时间为充电电池充好电;绿再十分充内置横流扇，有效防止电池温度过热和提供低噪音的充电环境等。<br></p>', 11, 4, 1, 0, 1663322665, 1727071154, NULL, 0, 0.00, 0.00, 1); 
COMMIT;

-- ----------------------------
-- Records of la_article_cate
-- ----------------------------
BEGIN;
INSERT INTO `la_article_cate` (`id`, `name`, `sort`, `is_show`, `create_time`, `update_time`, `delete_time`) VALUES (1, '科技', 0, 1, 1663317280, 1663317280, NULL), (2, '生活', 0, 1, 1663317280, 1663321464, NULL), (3, '好物', 0, 1, 1727070858, 1727070858, NULL); 
COMMIT;

-- ----------------------------
-- Records of la_decorate_page
-- ----------------------------
-- 开发者：杰哥网络科技 QQ: 2711793818 杰哥
-- 商城首页：默认显示"当前商家信息"组件
-- 个人中心：用户信息（主题色背景）+ 商家中心入口 + 我的服务（去掉广告图），页面背景白色
BEGIN;
INSERT INTO `la_decorate_page` (`id`, `type`, `name`, `data`, `meta`, `create_time`, `update_time`) VALUES (1, 1, '商城首页', '[{\"title\":\"当前商家信息\",\"name\":\"current-merchant\",\"content\":{\"show_switch\":true,\"show_actions\":true,\"show_tabs\":true,\"show_search\":true,\"show_content_list\":true,\"show_coupon\":true,\"show_apply_btn\":true,\"show_has_merchant\":true,\"enabled\":1},\"styles\":{\"primary_color\":\"#FF2D3A\",\"title_color\":\"#333\",\"desc_color\":\"#666\",\"margin_top\":0,\"margin_bottom\":10,\"padding_horizontal\":12,\"border_radius\":12}}]', '[{\"title\":\"页面设置\",\"name\":\"page-meta\",\"content\":{\"title\":\"首页\",\"bg_type\":\"1\",\"bg_color\":\"#f5f5f5\",\"bg_image\":\"\",\"text_color\":\"2\",\"title_type\":\"1\",\"title_img\":\"\"},\"styles\":{}}]', 1661757188, 1710989700), (2, 2, '个人中心', '[{\"title\":\"用户信息\",\"name\":\"user-info\",\"disabled\":1,\"content\":{\"background_type\":2,\"background_color\":\"\",\"text_color\":\"#ffffff\"},\"styles\":{}},{\"title\":\"商家中心入口\",\"name\":\"merchant-center\",\"content\":{\"enabled\":1},\"styles\":{\"margin_top\":10,\"margin_bottom\":10,\"padding_horizontal\":12,\"border_radius\":12,\"primary_color\":\"#EF4444\"}},{\"title\":\"我的服务\",\"name\":\"my-service\",\"content\":{\"style\":1,\"title\":\"我的服务\",\"data\":[{\"image\":\"/resource/image/adminapi/default/user_collect.png\",\"name\":\"我的收藏\",\"link\":{\"path\":\"/pages/collection/collection\",\"name\":\"我的收藏\",\"type\":\"shop\"},\"is_show\":\"1\"},{\"image\":\"/resource/image/adminapi/default/user_setting.png\",\"name\":\"个人设置\",\"link\":{\"path\":\"/pages/user_set/user_set\",\"name\":\"个人设置\",\"type\":\"shop\"},\"is_show\":\"1\"},{\"image\":\"/resource/image/adminapi/default/user_kefu.png\",\"name\":\"联系客服\",\"link\":{\"path\":\"/pages/customer_service/customer_service\",\"name\":\"联系客服\",\"type\":\"shop\"},\"is_show\":\"1\"},{\"image\":\"/resource/image/adminapi/default/wallet.png\",\"name\":\"我的钱包\",\"link\":{\"path\":\"/packages/pages/user_wallet/user_wallet\",\"name\":\"我的钱包\",\"type\":\"shop\"},\"is_show\":\"1\"}],\"enabled\":1},\"styles\":{}}]', '[{\"title\":\"页面设置\",\"name\":\"page-meta\",\"content\":{\"title\":\"个人中心\",\"bg_type\":\"1\",\"bg_color\":\"#ffffff\",\"bg_image\":\"\",\"text_color\":\"1\",\"title_type\":\"2\",\"title_img\":\"/resource/image/adminapi/default/page_mate_title.png\"},\"styles\":{}}]', 1661757188, 1710933097), (3, 3, '客服设置', '[{\"title\":\"客服设置\",\"name\":\"customer-service\",\"content\":{\"title\":\"添加客服二维码\",\"time\":\"早上 9:30 - 19:00\",\"mobile\":\"1888888888\",\"qrcode\":\"/resource/image/adminapi/default/kefu01.png\",\"remark\":\"长按添加客服或拨打客服热线\"},\"styles\":{}}]', '', 1661757188, 1710929953), (4, 4, 'PC设置', '[{\"id\":\"lajcn8d0hzhed\",\"title\":\"首页轮播图\",\"name\":\"pc-banner\",\"content\":{\"enabled\":1,\"data\":[{\"image\":\"/resource/image/adminapi/default/banner003.png\",\"name\":\"\",\"link\":{\"path\":\"/pages/news/news\",\"name\":\"文章资讯\",\"type\":\"shop\"}},{\"image\":\"/resource/image/adminapi/default/banner002.png\",\"name\":\"\",\"link\":{\"path\":\"/pages/collection/collection\",\"name\":\"我的收藏\",\"type\":\"shop\"}},{\"image\":\"/resource/image/adminapi/default/banner001.png\",\"name\":\"\",\"link\":{}}]},\"styles\":{\"position\":\"absolute\",\"left\":\"40\",\"top\":\"75px\",\"width\":\"750px\",\"height\":\"340px\"}}]', '', 1661757188, 1710990175), (5, 5, '系统风格', '{\"themeColorId\":3,\"topTextColor\":\"white\",\"navigationBarColor\":\"#A74BFD\",\"themeColor1\":\"#A74BFD\",\"themeColor2\":\"#CB60FF\",\"buttonColor\":\"white\"}', '', 1710410915, 1710990415); 
COMMIT;

-- ----------------------------
-- Records of la_decorate_tabbar
-- ----------------------------
BEGIN;
INSERT INTO `la_decorate_tabbar` (`id`, `name`, `selected`, `unselected`, `link`, `is_show`, `create_time`, `update_time`) VALUES (1, '首页', 'resource/image/adminapi/default/tabbar_home_sel.png', 'resource/image/adminapi/default/tabbar_home.png', '{"path":"/pages/index/index","name":"商城首页","type":"shop"}', 1, 1662688157, 1662688157), (2, '订单', 'resource/image/adminapi/default/tabbar_order_sel.png', 'resource/image/adminapi/default/tabbar_order.png', '{"path":"/pages/order/order","name":"订单中心","type":"shop","canTab":"1"}', 1, 1662688157, 1662688157), (3, '消息', 'resource/image/adminapi/default/tabbar_text_sel.png', 'resource/image/adminapi/default/tabbar_text.png', '{"path":"/pages/message/index","name":"消息中心","type":"shop","canTab":"1"}', 1, 1662688157, 1662688157), (4, '我的', 'resource/image/adminapi/default/tabbar_me_sel.png', 'resource/image/adminapi/default/tabbar_me.png', '{"path":"/pages/user/user","name":"个人中心","type":"shop","canTab":"1"}', 1, 1662688157, 1662688157);
COMMIT;

-- ----------------------------
-- Records of la_dept
-- ----------------------------
BEGIN;
INSERT INTO `la_dept` (`id`, `name`, `pid`, `sort`, `leader`, `mobile`, `status`, `create_time`, `update_time`, `delete_time`) VALUES (1, '公司', 0, 0, 'boss', '12345698745', 1, 1650592684, 1653640368, NULL); 
COMMIT;

-- ----------------------------
-- Records of la_dev_pay_config
-- ----------------------------
BEGIN;
INSERT INTO `la_dev_pay_config` (`id`, `name`, `pay_way`, `config`, `icon`, `sort`, `remark`) VALUES (1, '余额支付', 1, '', '/resource/image/adminapi/default/balance_pay.png', 128, '余额支付备注'), (2, '微信支付', 2, '{\"interface_version\":\"v3\",\"merchant_type\":\"ordinary_merchant\",\"mch_id\":\"\",\"pay_sign_key\":\"\",\"apiclient_cert\":\"\",\"apiclient_key\":\"\"}', '/resource/image/adminapi/default/wechat_pay.png', 123, '微信支付备注'), (3, '支付宝支付', 3, '{\"mode\":\"normal_mode\",\"merchant_type\":\"ordinary_merchant\",\"app_id\":\"\",\"private_key\":\"\",\"ali_public_key\":\"\"}', '/resource/image/adminapi/default/ali_pay.png', 123, '支付宝支付'), (4, '彩虹易支付', 4, '{\"app_id\":\"\",\"app_secret\":\"\",\"pay_key\":\"\"}', '/resource/image/adminapi/default/rainbow_pay.png', 123, '彩虹易支付备注'); 
COMMIT;

-- ----------------------------
-- Records of la_dev_pay_way
-- ----------------------------
BEGIN;
INSERT INTO `la_dev_pay_way` (`id`, `pay_config_id`, `scene`, `is_default`, `status`) VALUES (1, 1, 1, 0, 1), (2, 2, 1, 1, 1), (3, 1, 2, 0, 1), (4, 2, 2, 1, 1), (5, 1, 3, 0, 1), (6, 2, 3, 1, 1), (7, 3, 3, 0, 1), (8, 4, 1, 0, 1), (9, 4, 2, 0, 1), (10, 4, 3, 0, 1), (11, 4, 4, 0, 1), (12, 4, 5, 0, 1); 
COMMIT;

-- ----------------------------
-- Records of la_dict_data
-- ----------------------------
BEGIN;
INSERT INTO `la_dict_data` (`id`, `name`, `value`, `type_id`, `type_value`, `sort`, `status`, `remark`, `create_time`, `update_time`, `delete_time`) VALUES (1, '隐藏', '0', 1, 'show_status', 0, 1, '', 1656381543, 1656381543, NULL), (2, '显示', '1', 1, 'show_status', 0, 1, '', 1656381550, 1656381550, NULL), (3, '进行中', '0', 2, 'business_status', 0, 1, '', 1656381410, 1656381410, NULL), (4, '成功', '1', 2, 'business_status', 0, 1, '', 1656381437, 1656381437, NULL), (5, '失败', '2', 2, 'business_status', 0, 1, '', 1656381449, 1656381449, NULL), (6, '待处理', '0', 3, 'event_status', 0, 1, '', 1656381212, 1656381212, NULL), (7, '已处理', '1', 3, 'event_status', 0, 1, '', 1656381315, 1656381315, NULL), (8, '拒绝处理', '2', 3, 'event_status', 0, 1, '', 1656381331, 1656381331, NULL), (9, '禁用', '1', 4, 'system_disable', 0, 1, '', 1656312030, 1656312030, NULL), (10, '正常', '0', 4, 'system_disable', 0, 1, '', 1656312040, 1656312040, NULL), (11, '未知', '0', 5, 'sex', 0, 1, '', 1656062988, 1656062988, NULL), (12, '男', '1', 5, 'sex', 0, 1, '', 1656062999, 1656062999, NULL), (13, '女', '2', 5, 'sex', 0, 1, '', 1656063009, 1656063009, NULL); 
COMMIT;

-- ----------------------------
-- Records of la_dict_type
-- ----------------------------
BEGIN;
INSERT INTO `la_dict_type` (`id`, `name`, `type`, `status`, `remark`, `create_time`, `update_time`, `delete_time`) VALUES (1, '显示状态', 'show_status', 1, '', 1656381520, 1656381520, NULL), (2, '业务状态', 'business_status', 1, '', 1656381393, 1656381393, NULL), (3, '事件状态', 'event_status', 1, '', 1656381075, 1656381075, NULL), (4, '禁用状态', 'system_disable', 1, '', 1656311838, 1656311838, NULL), (5, '用户性别', 'sex', 1, '', 1656062946, 1656380925, NULL); 
COMMIT;

-- ----------------------------
-- Records of la_notice_setting
-- ----------------------------
BEGIN;
INSERT INTO `la_notice_setting` (`id`, `scene_id`, `scene_name`, `scene_desc`, `recipient`, `type`, `system_notice`, `sms_notice`, `oa_notice`, `mnp_notice`, `support`, `update_time`) VALUES (1, 101, '登录验证码', '用户手机号码登录时发送', 1, 2, '{\"type\":\"system\",\"title\":\"\",\"content\":\"\",\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\"]}', '{\"type\":\"sms\",\"template_id\":\"SMS_123456\",\"content\":\"您正在登录，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"status\":\"1\",\"is_show\":\"1\"}', '{\"type\":\"oa\",\"template_id\":\"\",\"template_sn\":\"\",\"name\":\"\",\"first\":\"\",\"remark\":\"\",\"tpl\":[],\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\",\"配置路径：小程序后台 > 功能 > 订阅消息\"]}', '{\"type\":\"mnp\",\"template_id\":\"\",\"template_sn\":\"\",\"name\":\"\",\"tpl\":[],\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\",\"配置路径：小程序后台 > 功能 > 订阅消息\"]}', '2', NULL), (2, 102, '绑定手机验证码', '用户绑定手机号码时发送', 1, 2, '{\"type\":\"system\",\"title\":\"\",\"content\":\"\",\"status\":\"0\",\"is_show\":\"\"}', '{\"type\":\"sms\",\"template_id\":\"SMS_123456\",\"content\":\"您正在绑定手机号，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"status\":\"1\",\"is_show\":\"1\"}', '{\"type\":\"oa\",\"template_id\":\"\",\"template_sn\":\"\",\"name\":\"\",\"first\":\"\",\"remark\":\"\",\"tpl\":[],\"status\":\"0\",\"is_show\":\"\"}', '{\"type\":\"mnp\",\"template_id\":\"\",\"template_sn\":\"\",\"name\":\"\",\"tpl\":[],\"status\":\"0\",\"is_show\":\"\"}', '2', NULL), (3, 103, '变更手机验证码', '用户变更手机号码时发送', 1, 2, '{\"type\":\"system\",\"title\":\"\",\"content\":\"\",\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\"]}', '{\"type\":\"sms\",\"template_id\":\"SMS_123456\",\"content\":\"您正在变更手机号，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"status\":\"1\",\"is_show\":\"1\"}', '{\"type\":\"oa\",\"template_id\":\"\",\"template_sn\":\"\",\"name\":\"\",\"first\":\"\",\"remark\":\"\",\"tpl\":[],\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\",\"配置路径：小程序后台 > 功能 > 订阅消息\"]}', '{\"type\":\"mnp\",\"template_id\":\"\",\"template_sn\":\"\",\"name\":\"\",\"tpl\":[],\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\",\"配置路径：小程序后台 > 功能 > 订阅消息\"]}', '2', NULL), (4, 104, '找回登录密码验证码', '用户找回登录密码号码时发送', 1, 2, '{\"type\":\"system\",\"title\":\"\",\"content\":\"\",\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\"]}', '{\"type\":\"sms\",\"template_id\":\"SMS_123456\",\"content\":\"您正在找回登录密码，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"status\":\"1\",\"is_show\":\"1\"}', '{\"type\":\"oa\",\"template_id\":\"\",\"template_sn\":\"\",\"name\":\"\",\"first\":\"\",\"remark\":\"\",\"tpl\":[],\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\",\"配置路径：小程序后台 > 功能 > 订阅消息\"]}', '{\"type\":\"mnp\",\"template_id\":\"\",\"template_sn\":\"\",\"name\":\"\",\"tpl\":[],\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\",\"配置路径：小程序后台 > 功能 > 订阅消息\"]}', '2', NULL), (5, 105, '商家入驻验证码', '用户申请商家入驻时发送', 1, 2, '{\"type\":\"system\",\"title\":\"\",\"content\":\"\",\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\"]}', '{\"type\":\"sms\",\"template_id\":\"SMS_123456\",\"content\":\"您正在申请商家入驻，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"status\":\"1\",\"is_show\":\"1\"}', '{\"type\":\"oa\",\"template_id\":\"\",\"template_sn\":\"\",\"name\":\"\",\"first\":\"\",\"remark\":\"\",\"tpl\":[],\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\",\"配置路径：小程序后台 > 功能 > 订阅消息\"]}', '{\"type\":\"mnp\",\"template_id\":\"\",\"template_sn\":\"\",\"name\":\"\",\"tpl\":[],\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\",\"配置路径：小程序后台 > 功能 > 订阅消息\"]}', '2', NULL); 
COMMIT;

-- ============================================================
-- 系统菜单数据
-- 开发者：杰哥网络科技
-- QQ: 2711793818 杰哥
-- ============================================================

BEGIN;
INSERT INTO `la_system_menu` (`id`, `pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`) VALUES
(4, 0, 'M', '权限管理', 'el-icon-Lock', 300, '', 'permission', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(5, 0, 'C', '工作台', 'el-icon-Monitor', 1000, 'workbench/index', 'workbench', 'workbench/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(6, 4, 'C', '菜单', 'el-icon-Operation', 100, 'auth.menu/lists', 'menu', 'permission/menu/index', '', '', 1, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(7, 4, 'C', '管理员', 'local-icon-shouyiren', 80, 'auth.admin/lists', 'admin', 'permission/admin/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(8, 4, 'C', '角色', 'el-icon-Female', 90, 'auth.role/lists', 'role', 'permission/role/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(12, 8, 'A', '新增', '', 1, 'auth.role/add', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(14, 8, 'A', '编辑', '', 1, 'auth.role/edit', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(15, 8, 'A', '删除', '', 1, 'auth.role/delete', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(16, 6, 'A', '新增', '', 1, 'auth.menu/add', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(17, 6, 'A', '编辑', '', 1, 'auth.menu/edit', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(18, 6, 'A', '删除', '', 1, 'auth.menu/delete', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(19, 7, 'A', '新增', '', 1, 'auth.admin/add', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(20, 7, 'A', '编辑', '', 1, 'auth.admin/edit', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(21, 7, 'A', '删除', '', 1, 'auth.admin/delete', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(23, 28, 'M', '开发工具', 'el-icon-EditPen', 40, '', 'dev_tools', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(24, 23, 'C', '代码生成器', 'el-icon-DocumentAdd', 1, 'tools.generator/generateTable', 'code', 'dev_tools/code/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(25, 0, 'M', '组织管理', 'el-icon-OfficeBuilding', 400, '', 'organization', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(26, 25, 'C', '部门管理', 'el-icon-Coordinate', 100, 'dept.dept/lists', 'department', 'organization/department/index', '', '', 1, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(27, 25, 'C', '岗位管理', 'el-icon-PriceTag', 90, 'dept.jobs/lists', 'post', 'organization/post/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(28, 0, 'M', '系统设置', 'el-icon-Setting', 200, '', 'setting', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(29, 28, 'M', '网站设置', 'el-icon-Basketball', 100, '', 'website', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(30, 29, 'C', '网站信息', '', 1, 'setting.web.web_setting/getWebsite', 'information', 'setting/website/information', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(31, 29, 'C', '网站备案', '', 1, 'setting.web.web_setting/getCopyright', 'filing', 'setting/website/filing', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(32, 29, 'C', '政策协议', '', 1, 'setting.web.web_setting/getAgreement', 'protocol', 'setting/website/protocol', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(33, 28, 'C', '存储设置', 'el-icon-FolderOpened', 70, 'setting.storage/lists', 'storage', 'setting/storage/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(34, 23, 'C', '字典管理', 'el-icon-Box', 1, 'setting.dict.dict_type/lists', 'dict', 'setting/dict/type/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(35, 28, 'M', '系统维护', 'el-icon-SetUp', 50, '', 'system', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(36, 35, 'C', '系统日志', '', 90, 'setting.system.log/lists', 'journal', 'setting/system/journal', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(37, 35, 'C', '系统缓存', '', 80, '', 'cache', 'setting/system/cache', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(38, 35, 'C', '系统环境', '', 70, 'setting.system.system/info', 'environment', 'setting/system/environment', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(39, 24, 'A', '导入数据表', '', 1, 'tools.generator/selectTable', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(40, 24, 'A', '代码生成', '', 1, 'tools.generator/generate', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(41, 23, 'C', '编辑数据表', '', 1, 'tools.generator/edit', 'code/edit', 'dev_tools/code/edit', '/dev_tools/code', '', 1, 0, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(42, 24, 'A', '同步表结构', '', 1, 'tools.generator/syncColumn', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(43, 24, 'A', '删除数据表', '', 1, 'tools.generator/delete', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(44, 24, 'A', '预览代码', '', 1, 'tools.generator/preview', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(45, 26, 'A', '新增', '', 1, 'dept.dept/add', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(46, 26, 'A', '编辑', '', 1, 'dept.dept/edit', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(47, 26, 'A', '删除', '', 1, 'dept.dept/delete', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(48, 27, 'A', '新增', '', 1, 'dept.jobs/add', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(49, 27, 'A', '编辑', '', 1, 'dept.jobs/edit', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(50, 27, 'A', '删除', '', 1, 'dept.jobs/delete', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(51, 30, 'A', '保存', '', 1, 'setting.web.web_setting/setWebsite', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(52, 31, 'A', '保存', '', 1, 'setting.web.web_setting/setCopyright', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(53, 32, 'A', '保存', '', 1, 'setting.web.web_setting/setAgreement', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(54, 33, 'A', '设置', '', 1, 'setting.storage/setup', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(55, 34, 'A', '新增', '', 1, 'setting.dict.dict_type/add', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(56, 34, 'A', '编辑', '', 1, 'setting.dict.dict_type/edit', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(57, 34, 'A', '删除', '', 1, 'setting.dict.dict_type/delete', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(58, 62, 'A', '新增', '', 1, 'setting.dict.dict_data/add', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(59, 62, 'A', '编辑', '', 1, 'setting.dict.dict_data/edit', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(60, 62, 'A', '删除', '', 1, 'setting.dict.dict_data/delete', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(61, 37, 'A', '清除系统缓存', '', 1, 'setting.system.cache/clear', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(62, 23, 'C', '字典数据管理', '', 1, 'setting.dict.dict_data/lists', 'dict/data', 'setting/dict/data/index', '/dev_tools/dict', '', 1, 0, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(63, 158, 'M', '素材管理', 'el-icon-Picture', 0, '', 'material', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(64, 63, 'C', '素材中心', 'el-icon-PictureRounded', 0, '', 'index', 'material/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(66, 26, 'A', '详情', '', 0, 'dept.dept/detail', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(67, 27, 'A', '详情', '', 0, 'dept.jobs/detail', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(68, 6, 'A', '详情', '', 0, 'auth.menu/detail', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(69, 7, 'A', '详情', '', 0, 'auth.admin/detail', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(70, 158, 'M', '文章资讯', 'el-icon-ChatLineSquare', 90, '', 'article', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(71, 70, 'C', '文章管理', 'el-icon-ChatDotSquare', 0, 'article.article/lists', 'lists', 'article/lists/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(72, 70, 'C', '文章添加/编辑', '', 0, 'article.article/add:edit', 'lists/edit', 'article/lists/edit', '/article/lists', '', 0, 0, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(73, 70, 'C', '文章栏目', 'el-icon-CollectionTag', 0, 'article.articleCate/lists', 'column', 'article/column/index', '', '', 1, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(74, 71, 'A', '新增', '', 0, 'article.article/add', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(75, 71, 'A', '详情', '', 0, 'article.article/detail', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(76, 71, 'A', '删除', '', 0, 'article.article/delete', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(77, 71, 'A', '修改状态', '', 0, 'article.article/updateStatus', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(78, 73, 'A', '添加', '', 0, 'article.articleCate/add', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(79, 73, 'A', '删除', '', 0, 'article.articleCate/delete', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(80, 73, 'A', '详情', '', 0, 'article.articleCate/detail', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(81, 73, 'A', '修改状态', '', 0, 'article.articleCate/updateStatus', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(82, 0, 'M', '渠道设置', 'el-icon-Message', 500, '', 'channel', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(83, 82, 'C', 'h5设置', 'el-icon-Cellphone', 100, 'channel.web_page_setting/getConfig', 'h5', 'channel/h5', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(84, 83, 'A', '保存', '', 0, 'channel.web_page_setting/setConfig', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(85, 82, 'M', '微信公众号', 'local-icon-dingdan', 80, '', 'wx_oa', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(86, 85, 'C', '公众号配置', '', 0, 'channel.official_account_setting/getConfig', 'config', 'channel/wx_oa/config', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(87, 85, 'C', '菜单管理', '', 0, 'channel.official_account_menu/detail', 'menu', 'channel/wx_oa/menu', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(88, 86, 'A', '保存', '', 0, 'channel.official_account_setting/setConfig', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(89, 86, 'A', '保存并发布', '', 0, 'channel.official_account_menu/save', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(90, 85, 'C', '关注回复', '', 0, 'channel.official_account_reply/lists', 'follow', 'channel/wx_oa/reply/follow_reply', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(91, 85, 'C', '关键字回复', '', 0, '', 'keyword', 'channel/wx_oa/reply/keyword_reply', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(93, 85, 'C', '默认回复', '', 0, '', 'default', 'channel/wx_oa/reply/default_reply', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(94, 82, 'C', '微信小程序', 'local-icon-weixin', 90, 'channel.mnp_settings/getConfig', 'weapp', 'channel/weapp', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(95, 94, 'A', '保存', '', 0, 'channel.mnp_settings/setConfig', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(96, 0, 'M', '装修管理', 'el-icon-Brush', 600, '', 'decoration', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(97, 175, 'C', '页面装修', 'el-icon-CopyDocument', 100, 'decorate.page/detail', 'pages', 'decoration/pages/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(98, 97, 'A', '保存', '', 0, 'decorate.page/save', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(99, 175, 'C', '底部导航', 'el-icon-Position', 90, 'decorate.tabbar/detail', 'tabbar', 'decoration/tabbar', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(100, 99, 'A', '保存', '', 0, 'decorate.tabbar/save', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(101, 158, 'M', '消息管理', 'el-icon-ChatDotRound', 80, '', 'message', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(102, 101, 'C', '通知设置', '', 0, 'notice.notice/settingLists', 'notice', 'message/notice/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(103, 102, 'A', '详情', '', 0, 'notice.notice/detail', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(104, 101, 'C', '通知设置编辑', '', 0, 'notice.notice/set', 'notice/edit', 'message/notice/edit', '/message/notice', '', 0, 0, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(105, 71, 'A', '编辑', '', 0, 'article.article/edit', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(107, 101, 'C', '短信设置', '', 0, 'notice.sms_config/getConfig', 'short_letter', 'message/short_letter/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(108, 107, 'A', '设置', '', 0, 'notice.sms_config/setConfig', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(109, 107, 'A', '详情', '', 0, 'notice.sms_config/detail', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(110, 28, 'C', '热门搜索', 'el-icon-Search', 60, 'setting.hot_search/getConfig', 'search', 'setting/search/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(111, 110, 'A', '保存', '', 0, 'setting.hot_search/setConfig', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(112, 28, 'M', '用户设置', 'local-icon-keziyuyue', 90, '', 'user', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(113, 112, 'C', '用户设置', '', 0, 'setting.user.user/getConfig', 'setup', 'setting/user/setup', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(114, 113, 'A', '保存', '', 0, 'setting.user.user/setConfig', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(115, 112, 'C', '登录注册', '', 0, 'setting.user.user/getRegisterConfig', 'login_register', 'setting/user/login_register', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(116, 115, 'A', '保存', '', 0, 'setting.user.user/setRegisterConfig', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(117, 0, 'M', '用户管理', 'el-icon-User', 900, '', 'consumer', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(118, 117, 'C', '用户列表', 'local-icon-user_guanli', 100, 'user.user/lists', 'lists', 'consumer/lists/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(119, 117, 'C', '用户详情', '', 90, 'user.user/detail', 'lists/detail', 'consumer/lists/detail', '/consumer/lists', '', 0, 0, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(120, 119, 'A', '编辑', '', 0, 'user.user/edit', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(140, 82, 'C', '微信开发平台', 'local-icon-notice_buyer', 70, 'channel.open_setting/getConfig', 'open_setting', 'channel/open_setting', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(141, 140, 'A', '保存', '', 0, 'channel.open_setting/setConfig', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(142, 176, 'C', 'PC端装修', 'el-icon-Monitor', 8, '', 'pc', 'decoration/pc', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(143, 35, 'C', '定时任务', '', 100, 'crontab.crontab/lists', 'scheduled_task', 'setting/system/scheduled_task/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(144, 35, 'C', '定时任务添加/编辑', '', 0, 'crontab.crontab/add:edit', 'scheduled_task/edit', 'setting/system/scheduled_task/edit', '/setting/system/scheduled_task', '', 0, 0, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(145, 143, 'A', '添加', '', 0, 'crontab.crontab/add', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(146, 143, 'A', '编辑', '', 0, 'crontab.crontab/edit', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(147, 143, 'A', '删除', '', 0, 'crontab.crontab/delete', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(148, 0, 'M', '模板示例', 'el-icon-SetUp', 100, '', 'template', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(149, 148, 'M', '组件示例', 'el-icon-Coin', 0, '', 'component', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(150, 149, 'C', '富文本', '', 90, '', 'rich_text', 'template/component/rich_text', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(151, 149, 'C', '上传文件', '', 80, '', 'upload', 'template/component/upload', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(152, 149, 'C', '图标', '', 100, '', 'icon', 'template/component/icon', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(153, 149, 'C', '文件选择器', '', 60, '', 'file', 'template/component/file', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(154, 149, 'C', '链接选择器', '', 50, '', 'link', 'template/component/link', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(155, 149, 'C', '超出自动打点', '', 40, '', 'overflow', 'template/component/overflow', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(156, 149, 'C', '悬浮input', '', 70, '', 'popover_input', 'template/component/popover_input', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(157, 119, 'A', '余额调整', '', 0, 'user.user/adjustMoney', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(158, 0, 'M', '应用管理', 'el-icon-Postcard', 800, '', 'app', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(159, 158, 'C', '用户充值', 'local-icon-fukuan', 100, 'recharge.recharge/getConfig', 'recharge', 'app/recharge/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(160, 159, 'A', '保存', '', 0, 'recharge.recharge/setConfig', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(161, 28, 'M', '支付设置', 'local-icon-set_pay', 80, '', 'pay', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(162, 161, 'C', '支付方式', '', 0, 'setting.pay.pay_way/getPayWay', 'method', 'setting/pay/method/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(163, 161, 'C', '支付配置', '', 0, 'setting.pay.pay_config/lists', 'config', 'setting/pay/config/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(164, 162, 'A', '设置支付方式', '', 0, 'setting.pay.pay_way/setPayWay', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(165, 163, 'A', '配置', '', 0, 'setting.pay.pay_config/setConfig', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(166, 0, 'M', '财务管理', 'local-icon-user_gaikuang', 700, '', 'finance', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(167, 166, 'C', '充值记录', 'el-icon-Wallet', 90, 'recharge.recharge/lists', 'recharge_record', 'finance/recharge_record', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(168, 166, 'C', '余额明细', 'local-icon-qianbao', 100, 'finance.account_log/lists', 'balance_details', 'finance/balance_details', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(169, 167, 'A', '退款', '', 0, 'recharge.recharge/refund', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(170, 166, 'C', '退款记录', 'local-icon-heshoujilu', 0, 'finance.refund/record', 'refund_record', 'finance/refund_record', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(171, 170, 'A', '重新退款', '', 0, 'recharge.recharge/refundAgain', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(172, 170, 'A', '退款日志', '', 0, 'finance.refund/log', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(173, 175, 'C', '系统风格', 'el-icon-Brush', 80, '', 'style', 'decoration/style/style', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(175, 96, 'M', '移动端', '', 100, '', 'mobile', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(176, 96, 'M', 'PC端', '', 90, '', 'pc', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(177, 29, 'C', '站点统计', '', 0, 'setting.web.web_setting/getSiteStatistics', 'statistics', 'setting/website/statistics', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(178, 177, 'A', '保存', '', 0, 'setting.web.web_setting/saveSiteStatistics', '', '', '', '', 1, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(179, 0, 'M', '商家管理', 'el-icon-Shop', 100, '', 'merchant', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(182, 179, 'C', '商户文章', 'el-icon-Reading', 10, 'merchant/article', 'article', 'article/merchant/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(184, 179, 'C', '商户资金', 'el-icon-Wallet', 40, 'merchant/finance', 'finance', 'finance/merchant/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(185, 0, 'M', '分销管理', 'el-icon-Share', 103, '', 'distribution', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(186, 185, 'C', '分销记录', 'el-icon-Tickets', 1, 'distribution.distribution/lists', 'lists', 'distribution/lists/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(187, 0, 'C', '邀请管理', 'el-icon-Connection', 104, 'user.invite/lists', 'user/invite', 'user/invite/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(188, 0, 'M', '消息管理', 'el-icon-ChatDotRound', 105, '', 'notice', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(189, 188, 'C', '系统公告', 'el-icon-Bell', 1, 'notice.system_notice/lists', 'notice/system', 'notice/system/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(191, 185, 'C', '分销申请', 'el-icon-DocumentChecked', 0, 'distribution.distribution_apply/lists', 'apply', 'distribution/apply/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(192, 166, 'C', '文章订单', 'el-icon-Checked', 0, '', 'article/order', 'article/order/index', '', '', 1, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(193, 0, 'M', '系列管理', 'el-icon-List', 85, 'series.Series/lists', 'series', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(198, 193, 'C', '期次管理', 'el-icon-Document', 90, 'series.Issue/lists', 'issue', 'series/issue/list', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(199, 198, 'A', '添加', '', 1, 'series.Issue/add', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(200, 198, 'A', '编辑', '', 1, 'series.Issue/edit', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(201, 198, 'A', '删除', '', 1, 'series.Issue/delete', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(202, 198, 'A', '发布', '', 1, 'series.Issue/publish', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(203, 193, 'C', '系列列表', 'el-icon-List', 100, 'series.Series/lists', 'list', 'series/list', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(204, 203, 'A', '添加', '', 1, 'series.Series/add', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(205, 203, 'A', '编辑', '', 1, 'series.Series/edit', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(206, 203, 'A', '删除', '', 1, 'series.Series/delete', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(207, 203, 'A', '详情', '', 1, 'series.Series/detail', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(208, 203, 'A', '状态', '', 1, 'series.Series/status', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(210, 179, 'M', '商户权限', '', 0, 'merchant/permission', '', '', '', '', 0, 0, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(215, 179, 'A', '审核商户', '', 60, 'merchant/audit', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(216, 179, 'A', '设置状态', '', 61, 'merchant/status', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(217, 179, 'A', '审核申请', '', 62, 'merchant/audit/apply', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(218, 179, 'A', '审核提现', '', 63, 'merchant/audit/withdraw', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(219, 179, 'A', '处理投诉', '', 64, 'merchant/complaint/handle', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(220, 179, 'C', '商户设置', 'el-icon-Shop', 30, 'merchant/setting', 'merchant', 'setting/merchant/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(221, 220, 'A', '保存', '', 1, 'setting.merchant.merchant/setConfig', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(222, 179, 'C', '入驻申请', 'el-icon-Document', 100, 'merchant/apply', 'apply', 'merchant/apply/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(223, 179, 'C', '商户列表', 'el-icon-User', 20, 'merchant/list', 'lists', 'merchant/lists/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(224, 179, 'C', '提现管理', 'el-icon-Money', 50, 'merchant/withdraw', 'withdraw', 'merchant/withdraw/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(225, 179, 'C', '投诉管理', 'el-icon-ChatDotRound', 70, 'merchant/complaint', 'complaint', 'merchant/complaint/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(226, 179, 'C', '商户详情', '', 0, 'merchant/detail', 'detail', 'merchant/detail/index', '/merchant/lists', '', 0, 0, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(227, 179, 'A', '统计数据', '', 4, 'merchant/stats', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(229, 179, 'A', '商户订单', '', 3, 'merchant/order', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(230, 222, 'A', '删除申请', '', 1, 'merchant.apply/delete', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(231, 179, 'A', '提现详情', '', 51, 'merchant/withdraw/detail', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(232, 179, 'A', '提现统计', '', 52, 'merchant/withdraw/stats', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(233, 225, 'A', '删除投诉', '', 1, 'merchant.complaint/del', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(234, 179, 'A', '编辑商户', '', 1, 'merchant/edit', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(235, 28, 'C', '提现设置', 'el-icon-Money', 101, 'setting.withdraw/getConfig', 'withdraw', 'setting/withdraw/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(236, 235, 'A', '保存设置', '', 0, 'setting.withdraw/setConfig', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(300, 0, 'M', '聊天管理', 'el-icon-ChatDotRound', 50, '', 'chat', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(301, 300, 'C', '聊天室管理', 'el-icon-ChatLineRound', 100, 'chat.chat_room/lists', 'room', 'chat/room/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(302, 301, 'A', '新增', '', 1, 'chat.chat_room/add', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(303, 301, 'A', '编辑', '', 1, 'chat.chat_room/edit', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(304, 301, 'A', '删除', '', 1, 'chat.chat_room/delete', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(305, 301, 'A', '状态', '', 1, 'chat.chat_room/status', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(306, 300, 'C', '公共聊天室', 'el-icon-ChatDotRound', 95, 'chat.chat_public/lists', 'public', 'chat/public/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(307, 300, 'C', '私聊管理', 'el-icon-User', 90, 'chat.chat_private/lists', 'private', 'chat/private/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(311, 300, 'C', '违禁词管理', 'el-icon-Warning', 70, 'chat.chat_banned_word/lists', 'banned-word', 'chat/banned-word/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(312, 311, 'A', '新增', '', 1, 'chat.chat_banned_word/add', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(313, 311, 'A', '编辑', '', 1, 'chat.chat_banned_word/edit', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(314, 311, 'A', '删除', '', 1, 'chat.chat_banned_word/delete', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(315, 311, 'A', '状态', '', 1, 'chat.chat_banned_word/status', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(316, 300, 'C', '禁言管理', 'el-icon-Mute', 60, 'chat.chat_ban/lists', 'ban', 'chat/ban/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(317, 316, 'A', '新增', '', 1, 'chat.chat_ban/add', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(318, 316, 'A', '解除', '', 1, 'chat.chat_ban/cancel', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(319, 300, 'C', '聊天设置', 'el-icon-Setting', 50, 'chat.chat_setting/getConfig', 'setting', 'chat/setting/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(320, 319, 'A', '保存', '', 1, 'chat.chat_setting/setConfig', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(321, 319, 'A', '服务状态', '', 1, 'chat.chat_service/status', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(322, 319, 'A', '启动服务', '', 1, 'chat.chat_service/start', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(323, 319, 'A', '停止服务', '', 1, 'chat.chat_service/stop', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(326, 28, 'C', '邮箱配置', 'el-icon-Message', 75, 'setting.email/config', 'email', 'setting/email/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(327, 326, 'A', '保存', '', 1, 'setting.email/setConfig', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(328, 326, 'A', '发送测试', '', 2, 'setting.email/test', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(370, 326, 'A', '获取开关配置', '', 3, 'setting.email/getSwitchConfig', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(371, 326, 'A', '保存开关配置', '', 4, 'setting.email/setSwitchConfig', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(329, 0, 'C', '待处理审批', 'el-icon-Bell', 999, 'pending_approval/lists', 'pending_approval', 'pending_approval/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(330, 329, 'A', '查看列表', '', 0, 'pending_approval/lists', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(331, 329, 'A', '统计数据', '', 0, 'pending_approval/statistics', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(332, 329, 'A', '快捷审批', '', 0, 'pending_approval/audit', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(333, 329, 'A', '审批详情', '', 0, 'pending_approval/detail', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(343, 0, 'C', '文章审核', 'el-icon-Document', 50, 'article.audit/lists', 'article/audit', 'article/audit/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(344, 343, 'A', '查看列表', '', 0, 'article.audit/lists', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(345, 343, 'A', '审核文章', '', 0, 'article.audit/audit', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(346, 343, 'A', '批量审核', '', 0, 'article.audit/batchAudit', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(340, 0, 'C', '文章水印设置', '', 45, 'setting.article_watermark/getConfig', 'article_watermark', 'setting/article_watermark/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(341, 340, 'A', '获取配置', '', 0, 'setting.article_watermark/getConfig', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(342, 340, 'A', '保存配置', '', 0, 'setting.article_watermark/setConfig', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(347, 0, 'C', '文章提示设置', '', 46, 'setting.article_tips/getConfig', 'article_tips', 'setting/article_tips/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(348, 347, 'A', '获取配置', '', 0, 'setting.article_tips/getConfig', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(349, 347, 'A', '保存配置', '', 0, 'setting.article_tips/setConfig', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(366, 0, 'C', '商家提现', 'el-icon-Money', 60, 'finance.merchant_withdraw/lists', 'finance/merchant_withdraw', 'finance/merchant_withdraw', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(367, 366, 'A', '查看列表', '', 0, 'finance.merchant_withdraw/lists', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(368, 366, 'A', '审核提现', '', 0, 'finance.merchant_withdraw/audit', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(369, 366, 'A', '查看详情', '', 0, 'finance.merchant_withdraw/detail', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;

-- Table structure for la_series_order
DROP TABLE IF EXISTS `la_series_order`;
CREATE TABLE `la_series_order` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(32) NOT NULL DEFAULT '' COMMENT '订单编号',
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
  `series_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '系列ID',
  `order_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单金额',
  `pay_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支付状态:0-未支付,1-已支付',
  `pay_time` int(11) DEFAULT NULL COMMENT '支付时间',
  `pay_type` varchar(20) DEFAULT '' COMMENT '支付方式',
  `transaction_id` varchar(64) DEFAULT '' COMMENT '第三方支付流水号',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_order_sn` (`order_sn`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_series_id` (`series_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系列订单表';

-- Table structure for la_user_series_permission
DROP TABLE IF EXISTS `la_user_series_permission`;
CREATE TABLE `la_user_series_permission` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
  `series_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '系列ID',
  `order_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '订单ID',
  `expire_time` int(11) DEFAULT NULL COMMENT '过期时间(null表示永久)',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_series` (`user_id`, `series_id`),
  KEY `idx_series_id` (`series_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户系列权限表';

-- Table structure for la_open_result_log
DROP TABLE IF EXISTS `la_open_result_log`;
CREATE TABLE `la_open_result_log` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `lottery_type` varchar(50) NOT NULL DEFAULT '' COMMENT '彩票类型',
  `issue_no` varchar(20) NOT NULL DEFAULT '' COMMENT '期号',
  `open_code` varchar(50) DEFAULT '' COMMENT '开奖号码',
  `open_time` int(11) DEFAULT NULL COMMENT '开奖时间',
  `sync_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '同步状态:0-失败,1-成功',
  `sync_time` int(11) DEFAULT NULL COMMENT '同步时间',
  `error_msg` varchar(255) DEFAULT '' COMMENT '错误信息',
  `matched_series_id` int(11) DEFAULT '0' COMMENT '匹配的系列ID',
  `matched_article_id` int(11) DEFAULT '0' COMMENT '匹配的文章ID',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_lottery_issue` (`lottery_type`, `issue_no`),
  KEY `idx_sync_time` (`sync_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='开奖同步日志表';

INSERT INTO `la_config` (`type`, `name`, `value`, `create_time`, `update_time`) VALUES
('merchant', 'open_audit', '1', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('merchant', 'min_distribution_ratio', '0', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('merchant', 'max_distribution_ratio', '50', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('pay_desc', 'recharge_desc', '会员充值', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('pay_desc', 'article_desc', '会员服务', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('pay_desc', 'order_desc', '商品订单', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- Table structure for la_series
DROP TABLE IF EXISTS `la_series`;
CREATE TABLE `la_series` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `merchant_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商户ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '系列名称',
  `lottery_type` varchar(50) NOT NULL DEFAULT '' COMMENT '彩票类型:fc3d-福彩3D,pl3-排列三,ssq-双色球,dlt-大乐透',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '系列价格',
  `total_issues` int(11) NOT NULL DEFAULT '0' COMMENT '总期数',
  `current_issue` int(11) NOT NULL DEFAULT '0' COMMENT '当前期数',
  `series_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '系列状态:0-已结束,1-进行中',
  `auto_publish` tinyint(1) NOT NULL DEFAULT '0' COMMENT '自动发布:0-否,1-是',
  `publish_interval` int(11) NOT NULL DEFAULT '0' COMMENT '发布间隔(秒)',
  `desc` text COMMENT '系列介绍',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `idx_merchant_id` (`merchant_id`),
  KEY `idx_lottery_type` (`lottery_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系列表';

-- Table structure for la_issue
DROP TABLE IF EXISTS `la_issue`;
CREATE TABLE `la_issue` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `series_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '系列ID',
  `merchant_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商户ID',
  `issue_no` varchar(20) NOT NULL DEFAULT '' COMMENT '期号',
  `content` text COMMENT '内容',
  `open_code` varchar(50) DEFAULT '' COMMENT '开奖号码',
  `is_opened` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否已开奖:0-否,1-是',
  `open_time` int(11) DEFAULT NULL COMMENT '开奖时间',
  `issue_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '期次状态:0-草稿,1-已发布,2-已开奖',
  `publish_time` int(11) DEFAULT NULL COMMENT '发布时间',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `idx_series_id` (`series_id`),
  KEY `idx_merchant_id` (`merchant_id`),
  KEY `idx_issue_no` (`issue_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='期次表';

-- Table structure for la_user_realname
DROP TABLE IF EXISTS `la_user_realname`;
CREATE TABLE `la_user_realname` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
  `real_name` varchar(50) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `id_card` varchar(20) NOT NULL DEFAULT '' COMMENT '身份证号',
  `id_card_front` varchar(255) DEFAULT '' COMMENT '身份证正面',
  `id_card_back` varchar(255) DEFAULT '' COMMENT '身份证背面',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态:0-待审核,1-认证通过,2-认证失败',
  `audit_remark` varchar(255) DEFAULT '' COMMENT '审核备注',
  `audit_time` int(11) DEFAULT NULL COMMENT '审核时间',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_id` (`user_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户实名认证表';

-- Table structure for la_chat_message
DROP TABLE IF EXISTS `la_chat_message`;
CREATE TABLE `la_chat_message` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `room_id` varchar(50) NOT NULL DEFAULT 'public' COMMENT '聊天室ID，public为公共频道',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '发送用户ID',
  `nickname` varchar(100) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '用户头像',
  `content` text NOT NULL COMMENT '消息内容',
  `msg_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '消息类型：1-文字 2-图片 3-系统消息',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否删除：0-否 1-是',
  `create_time` int(10) UNSIGNED DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_room_id` (`room_id`),
  KEY `idx_create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='聊天消息表';

-- Table structure for la_chat_room
DROP TABLE IF EXISTS `la_chat_room`;
CREATE TABLE `la_chat_room` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '聊天室名称',
  `room_id` varchar(50) NOT NULL DEFAULT '' COMMENT '聊天室ID',
  `description` varchar(255) DEFAULT '' COMMENT '聊天室描述',
  `max_users` int(11) NOT NULL DEFAULT 1000 COMMENT '最大用户数',
  `is_public` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否公开：0-否 1-是',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：0-禁用 1-启用',
  `create_time` int(10) UNSIGNED DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_room_id` (`room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='聊天室配置表';

-- 初始化公共聊天室
INSERT INTO `la_chat_room` (`name`, `room_id`, `description`, `max_users`, `is_public`, `status`, `create_time`) 
VALUES ('公共频道', 'public', '所有人都可以参与的公共聊天室', 10000, 1, 1, UNIX_TIMESTAMP());

-- Table structure for la_email_log
DROP TABLE IF EXISTS `la_email_log`;
CREATE TABLE `la_email_log` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `scene_id` int(10) NOT NULL DEFAULT 0 COMMENT '场景ID',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT '接收邮箱',
  `title` varchar(200) NOT NULL DEFAULT '' COMMENT '邮件标题',
  `content` text COMMENT '邮件内容',
  `code` varchar(10) NOT NULL DEFAULT '' COMMENT '验证码',
  `send_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '发送状态:0-发送中,1-成功,2-失败',
  `send_time` int(10) DEFAULT NULL COMMENT '发送时间',
  `is_verify` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否已验证:0-否,1-是',
  `error_msg` varchar(255) DEFAULT '' COMMENT '错误信息',
  `create_time` int(10) DEFAULT NULL,
  `update_time` int(10) DEFAULT NULL,
  `delete_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_scene` (`scene_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='邮件发送记录表';

-- Records of la_config (功能开关配置)
INSERT INTO `la_config` (`type`, `name`, `value`, `create_time`) VALUES
('system', 'register_open', '1', UNIX_TIMESTAMP()),
('system', 'register_verify_type', 'email', UNIX_TIMESTAMP()),
('system', 'merchant_apply_open', '1', UNIX_TIMESTAMP()),
('system', 'merchant_apply_verify_type', 'email', UNIX_TIMESTAMP()),
('system', 'distributor_apply_verify_type', 'email', UNIX_TIMESTAMP()),
('system', 'withdraw_verify_type', 'email', UNIX_TIMESTAMP()),
('system', 'email_notify_open', '0', UNIX_TIMESTAMP()),
('system', 'sms_notify_open', '1', UNIX_TIMESTAMP()),
('login', 'login_way', '["1","2"]', UNIX_TIMESTAMP()),
('login', 'coerce_mobile', '1', UNIX_TIMESTAMP()),
('login', 'third_auth', '1', UNIX_TIMESTAMP()),
('login', 'wechat_auth', '1', UNIX_TIMESTAMP()),
('login', 'qq_auth', '0', UNIX_TIMESTAMP()),
('login', 'social_login', '0', UNIX_TIMESTAMP()),
('login', 'login_agreement', '1', UNIX_TIMESTAMP()),
('social_login', 'appid', '', UNIX_TIMESTAMP()),
('social_login', 'appkey', '', UNIX_TIMESTAMP()),
('social_login', 'qq_enable', '0', UNIX_TIMESTAMP()),
('social_login', 'wx_enable', '0', UNIX_TIMESTAMP()),
('social_login', 'alipay_enable', '0', UNIX_TIMESTAMP()),
('social_login', 'baidu_enable', '0', UNIX_TIMESTAMP()),
('social_login', 'microsoft_enable', '0', UNIX_TIMESTAMP()),
('email', 'email_config', '{"smtp_server":"","port":465,"username":"","password":"","from_email":"","from_name":"系统通知","admin_email":"","encrypt":"ssl"}', UNIX_TIMESTAMP());

-- 邮件通知独立开关配置
INSERT INTO `la_config` (`type`, `name`, `value`, `create_time`) VALUES
('email_switch', 'merchant_apply_admin_notify', '1', UNIX_TIMESTAMP()),
('email_switch', 'merchant_audit_notify', '1', UNIX_TIMESTAMP()),
('email_switch', 'order_notify', '1', UNIX_TIMESTAMP()),
('email_switch', 'withdraw_notify', '1', UNIX_TIMESTAMP()),
('email_switch', 'distribution_apply_notify', '1', UNIX_TIMESTAMP()),
('email_switch', 'distribution_audit_notify', '1', UNIX_TIMESTAMP());

-- Records of la_notice_setting (邮件通知场景)
INSERT INTO `la_notice_setting` (`scene_id`, `scene_name`, `scene_desc`, `recipient`, `type`, `system_notice`, `sms_notice`, `oa_notice`, `mnp_notice`, `support`, `update_time`) VALUES
(201, '用户注册邮箱验证', '用户注册时发送邮箱验证码', 1, 2, '{}', '{}', '{}', '{}', '5', NULL),
(202, '商家入驻邮箱验证', '商家申请入驻时发送邮箱验证码', 1, 2, '{}', '{}', '{}', '{}', '5', NULL),
(203, '商家订单通知', '用户购买文章时通知商家', 2, 1, '{}', '{}', '{}', '{}', '5', NULL),
(204, '商家提现通知', '商家提现成功/失败时通知', 2, 1, '{}', '{}', '{}', '{}', '5', NULL),
(205, '商家绑定邮箱验证', '商家绑定/修改邮箱时发送验证码', 1, 2, '{}', '{}', '{}', '{}', '5', NULL),
(206, '用户绑定邮箱验证', '用户绑定/修改邮箱时发送验证码', 1, 2, '{}', '{}', '{}', '{}', '5', NULL),
(207, '商家入驻审核通知', '商家入驻审核结果通知', 1, 1, '{}', '{}', '{}', '{}', '5', NULL);

-- 文章详情提示配置
INSERT INTO `la_config` (`type`, `name`, `value`, `create_time`) VALUES
('article_tips', 'top_tips', '<p>购买前请仔细阅读文章介绍，确认内容符合您的需求。</p>', UNIX_TIMESTAMP()),
('article_tips', 'top_tips_show', '1', UNIX_TIMESTAMP()),
('article_tips', 'bottom_tips', '<p><strong>购买须知：</strong></p><p>1. 本商品为虚拟商品，购买后不支持退款。</p><p>2. 购买后可永久查看文章内容。</p><p>3. 如有问题请联系客服。</p>', UNIX_TIMESTAMP()),
('article_tips', 'bottom_tips_show', '1', UNIX_TIMESTAMP());

-- 文章水印配置
INSERT INTO `la_config` (`type`, `name`, `value`, `create_time`) VALUES
('article_watermark', 'enable', '0', UNIX_TIMESTAMP()),
('article_watermark', 'text', '杰哥网络科技', UNIX_TIMESTAMP()),
('article_watermark', 'contact', 'QQ:2711793818', UNIX_TIMESTAMP()),
('article_watermark', 'position', 'right_bottom', UNIX_TIMESTAMP()),
('article_watermark', 'opacity', '0.15', UNIX_TIMESTAMP());

-- PC端访问配置
INSERT INTO `la_config` (`type`, `name`, `value`, `create_time`) VALUES
('pc_setting', 'pc_open', '1', UNIX_TIMESTAMP()),
('pc_setting', 'pc_close_tips', 'PC端暂未开放，请使用手机访问', UNIX_TIMESTAMP());

-- =============================================
-- 私聊功能表
-- 开发者公众号：杰哥网络科技
-- QQ: 2711793818 杰哥
-- =============================================

-- Table structure for la_chat_conversation
DROP TABLE IF EXISTS `la_chat_conversation`;
CREATE TABLE `la_chat_conversation` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `conversation_id` varchar(64) NOT NULL DEFAULT '' COMMENT '会话ID，格式：private_{小ID}_{大ID}',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `target_id` int(11) UNSIGNED NOT NULL COMMENT '对方ID（商家ID或用户ID）',
  `target_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '对方类型：1-商家 2-用户',
  `last_message` varchar(500) NOT NULL DEFAULT '' COMMENT '最后一条消息内容',
  `last_message_time` int(10) UNSIGNED DEFAULT NULL COMMENT '最后消息时间',
  `unread_count` int(11) NOT NULL DEFAULT 0 COMMENT '未读消息数',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否删除：0-否 1-是',
  `create_time` int(10) UNSIGNED DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_conversation_user` (`conversation_id`, `user_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_target_id` (`target_id`),
  KEY `idx_last_message_time` (`last_message_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='私聊会话表';

-- Table structure for la_chat_ban
DROP TABLE IF EXISTS `la_chat_ban`;
CREATE TABLE `la_chat_ban` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '被禁言用户ID',
  `user_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '用户类型：1-普通用户 2-商家',
  `ban_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '禁言类型：1-私聊禁言 2-公共聊天禁言 3-全部禁言',
  `reason` varchar(255) NOT NULL DEFAULT '' COMMENT '禁言原因',
  `admin_id` int(11) UNSIGNED NOT NULL COMMENT '操作管理员ID',
  `expire_time` int(10) UNSIGNED DEFAULT NULL COMMENT '禁言到期时间，NULL表示永久',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：0-已解除 1-禁言中',
  `create_time` int(10) UNSIGNED DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_user_type` (`user_type`),
  KEY `idx_status` (`status`),
  KEY `idx_expire_time` (`expire_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='聊天禁言记录表';

-- Table structure for la_chat_banned_word
DROP TABLE IF EXISTS `la_chat_banned_word`;
CREATE TABLE `la_chat_banned_word` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `word` varchar(100) NOT NULL DEFAULT '' COMMENT '违禁词/敏感词',
  `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '类型：1-违禁词 2-敏感词',
  `replace_word` varchar(100) NOT NULL DEFAULT '' COMMENT '替换词，为空则直接拦截',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态：0-禁用 1-启用',
  `create_time` int(10) UNSIGNED DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_word` (`word`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='聊天违禁词表';

-- Table structure for la_chat_setting
DROP TABLE IF EXISTS `la_chat_setting`;
CREATE TABLE `la_chat_setting` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `chat_enabled` tinyint(1) NOT NULL DEFAULT 1 COMMENT '开启聊天功能：0-否 1-是',
  `chat_notice` varchar(500) NOT NULL DEFAULT '' COMMENT '聊天室公告',
  `max_message_length` int(11) NOT NULL DEFAULT 500 COMMENT '消息最大长度',
  `message_interval` int(11) NOT NULL DEFAULT 1 COMMENT '消息发送间隔（秒）',
  `enable_banned_word` tinyint(1) NOT NULL DEFAULT 1 COMMENT '开启违禁词过滤：0-否 1-是',
  `enable_ip_blacklist` tinyint(1) NOT NULL DEFAULT 0 COMMENT '开启IP黑名单：0-否 1-是',
  `show_online_count` tinyint(1) NOT NULL DEFAULT 1 COMMENT '显示在线人数：0-否 1-是',
  `create_time` int(10) UNSIGNED DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='聊天设置表';

-- 初始化聊天设置
INSERT INTO `la_chat_setting` (`chat_enabled`, `chat_notice`, `max_message_length`, `message_interval`, `enable_banned_word`, `enable_ip_blacklist`, `show_online_count`, `create_time`) 
VALUES (1, '欢迎来到聊天室，请文明发言！', 500, 1, 1, 0, 1, UNIX_TIMESTAMP());

-- ============================================================
-- 数据库索引优化
-- 开发者：杰哥网络科技
-- QQ: 2711793818 杰哥
-- 注意：以下索引仅添加CREATE TABLE中未定义的索引
-- ============================================================

-- 用户表索引
ALTER TABLE `la_user` ADD INDEX `idx_inviter_id` (`inviter_id`);
ALTER TABLE `la_user` ADD INDEX `idx_create_time` (`create_time`);
ALTER TABLE `la_user` ADD INDEX `idx_mobile` (`mobile`);
ALTER TABLE `la_user` ADD INDEX `idx_account` (`account`);
ALTER TABLE `la_user` ADD INDEX `idx_sn` (`sn`);

-- 账户流水表索引
ALTER TABLE `la_user_account_log` ADD INDEX `idx_user_id` (`user_id`);
ALTER TABLE `la_user_account_log` ADD INDEX `idx_create_time` (`create_time`);
ALTER TABLE `la_user_account_log` ADD INDEX `idx_change_type` (`change_type`);

-- 商户表索引（无索引，全部添加）
ALTER TABLE `la_merchant` ADD INDEX `idx_user_id` (`user_id`);
ALTER TABLE `la_merchant` ADD INDEX `idx_status` (`status`);
ALTER TABLE `la_merchant` ADD INDEX `idx_create_time` (`create_time`);

-- 文章表索引（补充cid、merchant_id、audit_status、is_show、create_time，其他已存在）
ALTER TABLE `la_article` ADD INDEX `idx_cid` (`cid`);
ALTER TABLE `la_article` ADD INDEX `idx_merchant_id` (`merchant_id`);
ALTER TABLE `la_article` ADD INDEX `idx_audit_status` (`audit_status`);
ALTER TABLE `la_article` ADD INDEX `idx_is_show` (`is_show`);
ALTER TABLE `la_article` ADD INDEX `idx_create_time` (`create_time`);

-- 文章订单表索引（补充pay_status、pay_time、create_time，其他已存在）
ALTER TABLE `la_article_order` ADD INDEX `idx_pay_status` (`pay_status`);
ALTER TABLE `la_article_order` ADD INDEX `idx_pay_time` (`pay_time`);
ALTER TABLE `la_article_order` ADD INDEX `idx_create_time` (`create_time`);

-- 充值订单表索引
ALTER TABLE `la_recharge_order` ADD INDEX `idx_user_id` (`user_id`);
ALTER TABLE `la_recharge_order` ADD INDEX `idx_pay_status` (`pay_status`);
ALTER TABLE `la_recharge_order` ADD INDEX `idx_pay_time` (`pay_time`);
ALTER TABLE `la_recharge_order` ADD INDEX `idx_create_time` (`create_time`);

-- 提现申请表索引（已存在于CREATE TABLE，跳过）

-- 分销日志表索引（补充source_user_id、create_time，user_id和order已存在）
ALTER TABLE `la_distribution_log` ADD INDEX `idx_source_user_id` (`source_user_id`);
ALTER TABLE `la_distribution_log` ADD INDEX `idx_create_time` (`create_time`);

-- 用户商户关系表索引
ALTER TABLE `la_user_merchant` ADD INDEX `idx_user_id` (`user_id`);
ALTER TABLE `la_user_merchant` ADD INDEX `idx_merchant_id` (`merchant_id`);
ALTER TABLE `la_user_merchant` ADD INDEX `idx_inviter_id` (`inviter_id`);

-- ============================================================
-- 推送关键词功能表
-- 开发者：杰哥网络科技 qq2711793818 杰哥
-- ============================================================

-- 用户推送关键词表
DROP TABLE IF EXISTS `la_user_push_keyword`;
CREATE TABLE `la_user_push_keyword` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
  `merchant_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商家ID',
  `keyword` varchar(50) NOT NULL DEFAULT '' COMMENT '关键词',
  `is_enable` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否启用:1-启用,0-禁用',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `idx_user_merchant` (`user_id`, `merchant_id`),
  KEY `idx_keyword` (`keyword`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户推送关键词表';

-- 推送消息记录表
DROP TABLE IF EXISTS `la_push_message`;
CREATE TABLE `la_push_message` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
  `merchant_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商家ID',
  `article_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '文章ID',
  `keyword` varchar(50) NOT NULL DEFAULT '' COMMENT '匹配的关键词',
  `title` varchar(200) NOT NULL DEFAULT '' COMMENT '推送标题',
  `content` varchar(500) DEFAULT '' COMMENT '推送内容',
  `is_read` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否已读:0-未读,1-已读',
  `push_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '推送方式:1-站内消息,2-WebSocket,3-邮件',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_merchant_id` (`merchant_id`),
  KEY `idx_article_id` (`article_id`),
  KEY `idx_is_read` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='推送消息记录表';

-- 商户关注表索引（la_merchant_follow已存在于CREATE TABLE，跳过）

-- 用户关注商户表索引（la_user_follow_merchant需要添加）
ALTER TABLE `la_user_follow_merchant` ADD INDEX `idx_user_id` (`user_id`);
ALTER TABLE `la_user_follow_merchant` ADD INDEX `idx_merchant_id` (`merchant_id`);

-- 操作日志表索引
ALTER TABLE `la_operation_log` ADD INDEX `idx_admin_id` (`admin_id`);
ALTER TABLE `la_operation_log` ADD INDEX `idx_create_time` (`create_time`);

-- 用户会话表索引（补充user_id，无create_time字段）
ALTER TABLE `la_user_session` ADD INDEX `idx_user_id` (`user_id`);

-- 文章分类表索引（idx_series已包含is_series，跳过）

-- ============================================================
-- 数据库初始化完成
-- 表总数：71 个（新增 la_user_push_keyword、la_push_message）
-- 索引优化：32 个（新增）
-- 开发者：杰哥网络科技
-- QQ: 2711793818 杰哥
-- ============================================================

