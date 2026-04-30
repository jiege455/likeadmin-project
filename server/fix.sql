-- 开发者公众号：杰哥网络科技
-- QQ: 2711793818 杰哥
-- 直接修复通知设置表

DROP TABLE IF EXISTS la_notice_setting;

CREATE TABLE la_notice_setting (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `scene_id` int(10) NOT NULL COMMENT '场景 id',
  `scene_name` varchar(255) NOT NULL DEFAULT '' COMMENT '场景名称',
  `scene_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '场景描述',
  `recipient` tinyint(1) NOT NULL DEFAULT '1' COMMENT '接收者 1-用户 2-平台',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '通知类型：1-业务通知 2-验证码',
  `system_notice` text COMMENT '系统通知设置',
  `sms_notice` text COMMENT '短信通知设置',
  `oa_notice` text COMMENT '公众号通知设置',
  `mnp_notice` text COMMENT '小程序通知设置',
  `support` char(10) NOT NULL DEFAULT '' COMMENT '支持的发送类型',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='通知设置表';

INSERT INTO la_notice_setting VALUES
(1, 101, '登录验证码', '用户手机号码登录时发送', 1, 2, '{}', '{"status":"1"}', '{}', '{}', '2', NULL),
(2, 102, '绑定手机验证码', '用户绑定手机号码时发送', 1, 2, '{}', '{"status":"1"}', '{}', '{}', '2', NULL),
(3, 103, '变更手机验证码', '用户变更手机号码时发送', 1, 2, '{}', '{"status":"1"}', '{}', '{}', '2', NULL),
(4, 104, '找回登录密码验证码', '用户找回登录密码号码时发送', 1, 2, '{}', '{"status":"1"}', '{}', '{}', '2', NULL),
(5, 105, '商家入驻验证码', '用户申请商家入驻时发送', 1, 2, '{}', '{"status":"1"}', '{}', '{}', '2', NULL);

SELECT '修复完成' as result, COUNT(*) as count FROM la_notice_setting;
