@echo off
chcp 65001 >nul
echo ========================================
echo 删除系统开关菜单
echo 开发者公众号：杰哥网络科技
echo QQ: 2711793818 杰哥
echo ========================================
echo.

mysql -uroot -proot 127_0_0_1 -e "DELETE FROM la_system_menu WHERE name='系统开关' AND paths='setting.systemSwitch';"
echo 删除主菜单完成

mysql -uroot -proot 127_0_0_1 -e "DELETE FROM la_system_menu WHERE perms LIKE 'setting.systemSwitch%';"
echo 删除权限菜单完成

mysql -uroot -proot 127_0_0_1 -e "DELETE FROM la_system_menu WHERE paths LIKE 'setting.systemSwitch%';"
echo 删除路径菜单完成

echo.
echo ========================================
echo 删除成功！请刷新后台页面
echo ========================================
pause
