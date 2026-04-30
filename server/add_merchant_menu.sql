-- 添加商户设置菜单到系统设置下
-- 开发者：杰哥网络科技 qq2711793818 杰哥

-- 1. 添加商户设置菜单（pid=28 是系统设置的ID）
INSERT INTO `la_system_menu` (`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`) VALUES
(28, 'C', '商户设置', 'el-icon-Shop', 85, 'setting.merchant.merchant/getConfig', 'merchant', 'setting/merchant/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- 2. 添加保存权限按钮
INSERT INTO `la_system_menu` (`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`) 
SELECT id, 'A', '保存', '', 1, 'setting.merchant.merchant/setConfig', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
FROM `la_system_menu` WHERE name='商户设置' AND paths='merchant' LIMIT 1;

-- 3. 添加分销比例限制配置（如果不存在）
INSERT IGNORE INTO `la_config` (`type`, `name`, `value`, `create_time`, `update_time`) VALUES
('merchant', 'min_distribution_ratio', '0', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('merchant', 'max_distribution_ratio', '50', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
