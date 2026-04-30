-- =====================================================
-- 消息管理通知设置 - 数据库修复脚本（简化版）
-- =====================================================
-- 开发者公众号：杰哥网络科技
-- QQ: 2711793818 杰哥
-- =====================================================

-- 创建通知设置表
CREATE TABLE IF NOT EXISTS `la_notice_setting` (
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
  `support` char(10) NOT NULL DEFAULT '' COMMENT '支持的发送类型 1-系统通知 2-短信通知 3-微信模板消息 4-小程序提醒',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='通知设置表';

-- 插入基础数据
INSERT INTO `la_notice_setting` (`id`, `scene_id`, `scene_name`, `scene_desc`, `recipient`, `type`, `system_notice`, `sms_notice`, `oa_notice`, `mnp_notice`, `support`, `update_time`) VALUES
(1, 101, '登录验证码', '用户手机号码登录时发送', 1, 2, '{"type":"system","title":"","content":"","status":"0","is_show":"","tips":["可选变量 验证码:code"]}', '{"type":"sms","template_id":"SMS_123456","content":"您正在登录，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期 5 分钟。","status":"1","is_show":"1"}', '{"type":"oa","template_id":"","template_sn":"","name":"","first":"","remark":"","tpl":[],"status":"0","is_show":"","tips":["可选变量 验证码:code","配置路径：小程序后台 > 功能 > 订阅消息"]}', '{"type":"mnp","template_id":"","template_sn":"","name":"","tpl":[],"status":"0","is_show":"","tips":["可选变量 验证码:code","配置路径：小程序后台 > 功能 > 订阅消息"]}', '2', NULL),
(2, 102, '绑定手机验证码', '用户绑定手机号码时发送', 1, 2, '{"type":"system","title":"","content":"","status":"0","is_show":""}', '{"type":"sms","template_id":"SMS_123456","content":"您正在绑定手机号，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期 5 分钟。","status":"1","is_show":"1"}', '{"type":"oa","template_id":"","template_sn":"","name":"","first":"","remark":"","tpl":[],"status":"0","is_show":""}', '{"type":"mnp","template_id":"","template_sn":"","name":"","tpl":[],"status":"0","is_show":""}', '2', NULL),
(3, 103, '变更手机验证码', '用户变更手机号码时发送', 1, 2, '{"type":"system","title":"","content":"","status":"0","is_show":"","tips":["可选变量 验证码:code"]}', '{"type":"sms","template_id":"SMS_123456","content":"您正在变更手机号，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期 5 分钟。","status":"1","is_show":"1"}', '{"type":"oa","template_id":"","template_sn":"","name":"","first":"","remark":"","tpl":[],"status":"0","is_show":"","tips":["可选变量 验证码:code","配置路径：小程序后台 > 功能 > 订阅消息"]}', '{"type":"mnp","template_id":"","template_sn":"","name":"","tpl":[],"status":"0","is_show":"","tips":["可选变量 验证码:code","配置路径：小程序后台 > 功能 > 订阅消息"]}', '2', NULL),
(4, 104, '找回登录密码验证码', '用户找回登录密码号码时发送', 1, 2, '{"type":"system","title":"","content":"","status":"0","is_show":"","tips":["可选变量 验证码:code"]}', '{"type":"sms","template_id":"SMS_123456","content":"您正在找回登录密码，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期 5 分钟。","status":"1","is_show":"1"}', '{"type":"oa","template_id":"","template_sn":"","name":"","first":"","remark":"","tpl":[],"status":"0","is_show":"","tips":["可选变量 验证码:code","配置路径：小程序后台 > 功能 > 订阅消息"]}', '{"type":"mnp","template_id":"","template_sn":"","name":"","tpl":[],"status":"0","is_show":"","tips":["可选变量 验证码:code","配置路径：小程序后台 > 功能 > 订阅消息"]}', '2', NULL),
(5, 105, '商家入驻验证码', '用户申请商家入驻时发送', 1, 2, '{"type":"system","title":"","content":"","status":"0","is_show":"","tips":["可选变量 验证码:code"]}', '{"type":"sms","template_id":"SMS_123456","content":"您正在申请商家入驻，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期 5 分钟。","status":"1","is_show":"1"}', '{"type":"oa","template_id":"","template_sn":"","name":"","first":"","remark":"","tpl":[],"status":"0","is_show":"","tips":["可选变量 验证码:code","配置路径：小程序后台 > 功能 > 订阅消息"]}', '{"type":"mnp","template_id":"","template_sn":"","name":"","tpl":[],"status":"0","is_show":"","tips":["可选变量 验证码:code","配置路径：小程序后台 > 功能 > 订阅消息"]}', '2', NULL)
ON DUPLICATE KEY UPDATE
  scene_name = VALUES(scene_name),
  scene_desc = VALUES(scene_desc),
  recipient = VALUES(recipient),
  type = VALUES(type),
  support = VALUES(support);

-- 插入短信配置
INSERT INTO `la_config` (`type`, `name`, `value`, `create_time`, `update_time`)
VALUES
('sms', 'ali', '{"type":"ali","name":"阿里云短信","status":1,"sign":"","app_key":"","secret_key":""}', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('sms', 'tencent', '{"type":"tencent","name":"腾讯云短信","status":0,"sign":"","app_id":"","secret_key":"","secret_id":""}', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('sms', 'juhe', '{"type":"juhe","name":"聚合数据短信","status":0,"sign":"","app_key":"","template_id":""}', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('sms', 'engine', 'ALI', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE
  value = VALUES(value),
  update_time = VALUES(update_time);
