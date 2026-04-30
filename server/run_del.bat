mysql -uroot -proot 127_0_0_1 -e "DELETE FROM la_system_menu WHERE name='系统开关' AND paths='setting.systemSwitch'"
mysql -uroot -proot 127_0_0_1 -e "DELETE FROM la_system_menu WHERE perms LIKE 'setting.systemSwitch%'"
mysql -uroot -proot 127_0_0_1 -e "DELETE FROM la_system_menu WHERE paths LIKE 'setting.systemSwitch%'"
echo 删除完成
