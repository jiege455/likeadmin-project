-- 邮件通知独立开关配置
-- 开发者：杰哥网络科技
-- QQ: 2711793818

INSERT INTO `la_config` (`type`, `name`, `value`, `create_time`, `update_time`) VALUES
('email_switch', 'user_register_notify', '1', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('email_switch', 'merchant_apply_notify', '1', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('email_switch', 'merchant_apply_admin_notify', '1', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('email_switch', 'merchant_audit_notify', '1', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('email_switch', 'order_notify', '1', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('email_switch', 'withdraw_notify', '1', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('email_switch', 'distribution_apply_notify', '1', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('email_switch', 'distribution_audit_notify', '1', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
