DELETE FROM la_system_menu WHERE name='系统开关' AND paths='setting.systemSwitch';
DELETE FROM la_system_menu WHERE perms LIKE 'setting.systemSwitch%';
DELETE FROM la_system_menu WHERE paths LIKE 'setting.systemSwitch%';
SELECT '删除完成' as result;
