-- 开发者公众号：杰哥网络科技
-- QQ: 2711793818 杰哥
-- 删除系统开关菜单

DELETE FROM `la_system_menu` WHERE `name` = '系统开关' AND `paths` = 'setting.systemSwitch';
DELETE FROM `la_system_menu` WHERE `perms` LIKE 'setting.systemSwitch%';
DELETE FROM `la_system_menu` WHERE `paths` LIKE 'setting.systemSwitch%';

SELECT '删除完成' as result;
