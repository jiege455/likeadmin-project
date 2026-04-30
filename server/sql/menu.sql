-- ----------------------------
-- 后台菜单数据
-- 请根据实际情况调整 id 和 pid
-- ----------------------------

-- 1. 商家管理
INSERT INTO `la_system_menu` (`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `is_show`, `is_disable`, `create_time`, `update_time`) VALUES
(0, 'M', '商家管理', 'el-icon-Shop', 100, '', 'merchant', '', 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

SET @merchant_id = LAST_INSERT_ID();

INSERT INTO `la_system_menu` (`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `is_show`, `is_disable`, `create_time`, `update_time`) VALUES
(@merchant_id, 'C', '入驻申请', 'el-icon-Document', 1, 'merchant.apply/lists', 'merchant/apply', 'merchant/apply/index', 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(@merchant_id, 'C', '商户列表', 'el-icon-User', 2, 'merchant.merchant/lists', 'merchant/lists', 'merchant/lists/index', 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- 2. 文章管理 (添加到现有文章管理下，假设现有文章管理ID为2)
-- 如果要新建顶级菜单，请自行调整
-- 这里演示新建顶级菜单 "商户文章"
INSERT INTO `la_system_menu` (`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `is_show`, `is_disable`, `create_time`, `update_time`) VALUES
(0, 'C', '商户文章', 'el-icon-Reading', 101, 'article.merchant_article/lists', 'article/merchant', 'article/merchant/index', 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- 3. 资金管理
INSERT INTO `la_system_menu` (`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `is_show`, `is_disable`, `create_time`, `update_time`) VALUES
(0, 'M', '资金管理', 'el-icon-Money', 102, '', 'finance', '', 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

SET @finance_id = LAST_INSERT_ID();

INSERT INTO `la_system_menu` (`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `is_show`, `is_disable`, `create_time`, `update_time`) VALUES
(@finance_id, 'C', '商户资金', 'el-icon-Wallet', 1, 'finance.merchant_finance/lists', 'finance/merchant', 'finance/merchant/index', 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- 4. 分销管理
INSERT INTO `la_system_menu` (`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `is_show`, `is_disable`, `create_time`, `update_time`) VALUES
(0, 'M', '分销管理', 'el-icon-Share', 103, '', 'distribution', '', 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

SET @distribution_id = LAST_INSERT_ID();

INSERT INTO `la_system_menu` (`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `is_show`, `is_disable`, `create_time`, `update_time`) VALUES
(@distribution_id, 'C', '分销记录', 'el-icon-Tickets', 1, 'distribution.distribution/lists', 'distribution/lists', 'distribution/lists/index', 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- 5. 邀请管理
INSERT INTO `la_system_menu` (`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `is_show`, `is_disable`, `create_time`, `update_time`) VALUES
(0, 'C', '邀请管理', 'el-icon-Connection', 104, 'user.invite/lists', 'user/invite', 'user/invite/index', 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- 6. 消息管理
INSERT INTO `la_system_menu` (`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `is_show`, `is_disable`, `create_time`, `update_time`) VALUES
(0, 'M', '消息管理', 'el-icon-ChatDotRound', 105, '', 'notice', '', 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

SET @notice_id = LAST_INSERT_ID();

INSERT INTO `la_system_menu` (`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `is_show`, `is_disable`, `create_time`, `update_time`) VALUES
(@notice_id, 'C', '系统公告', 'el-icon-Bell', 1, 'notice.system_notice/lists', 'notice/system', 'notice/system/index', 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- 7. 系统配置 (添加到现有系统配置下，或新建)
-- 假设添加到顶级 "系统配置" (通常ID不确定，这里新建一个顶级作为示例)
INSERT INTO `la_system_menu` (`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `is_show`, `is_disable`, `create_time`, `update_time`) VALUES
(0, 'M', '扩展配置', 'el-icon-Setting', 106, '', 'ext_setting', '', 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

SET @setting_id = LAST_INSERT_ID();

INSERT INTO `la_system_menu` (`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `is_show`, `is_disable`, `create_time`, `update_time`) VALUES
(@setting_id, 'C', '平台抽成', 'el-icon-Coin', 1, 'setting.config/getCommissionConfig', 'setting/commission', 'setting/commission/index', 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(@setting_id, 'C', '邮箱配置', 'el-icon-Message', 2, 'setting.config/getSmtpConfig', 'setting/smtp', 'setting/smtp/index', 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
