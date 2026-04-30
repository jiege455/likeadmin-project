DELETE FROM `la_system_menu` WHERE `name` = '系统开关' AND `paths` = 'setting.systemSwitch';
DELETE FROM `la_system_menu` WHERE `perms` LIKE 'setting.systemSwitch%';
SELECT '删除成功' as result;
