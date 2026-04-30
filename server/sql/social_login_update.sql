-- 聚合登录配置升级SQL
-- 开发者：杰哥网络科技 qq2711793818
-- 日期：2026-03-25

-- 聚合登录配置（type=login）
INSERT INTO `la_config` (`type`, `name`, `value`, `create_time`, `update_time`) VALUES
('login', 'social_login', '0', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);

-- 聚合登录配置（type=social_login）
INSERT INTO `la_config` (`type`, `name`, `value`, `create_time`, `update_time`) VALUES
('social_login', 'appid', '', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('social_login', 'appkey', '', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('social_login', 'qq_enable', '0', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('social_login', 'wx_enable', '0', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('social_login', 'alipay_enable', '0', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('social_login', 'baidu_enable', '0', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('social_login', 'microsoft_enable', '0', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);

-- 注意：如果上面批量插入有问题，可以使用下面的单独插入语句

-- INSERT INTO `la_config` (`type`, `name`, `value`, `create_time`, `update_time`) VALUES ('social_login', 'appid', '', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);
-- INSERT INTO `la_config` (`type`, `name`, `value`, `create_time`, `update_time`) VALUES ('social_login', 'appkey', '', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);
-- INSERT INTO `la_config` (`type`, `name`, `value`, `create_time`, `update_time`) VALUES ('social_login', 'qq_enable', '0', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);
-- INSERT INTO `la_config` (`type`, `name`, `value`, `create_time`, `update_time`) VALUES ('social_login', 'wx_enable', '0', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);
-- INSERT INTO `la_config` (`type`, `name`, `value`, `create_time`, `update_time`) VALUES ('social_login', 'alipay_enable', '0', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);
-- INSERT INTO `la_config` (`type`, `name`, `value`, `create_time`, `update_time`) VALUES ('social_login', 'baidu_enable', '0', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);
-- INSERT INTO `la_config` (`type`, `name`, `value`, `create_time`, `update_time`) VALUES ('social_login', 'microsoft_enable', '0', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);
