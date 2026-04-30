@echo off
chcp 65001
cls

echo ========================================
echo     前端项目编译脚本
echo     开发者：杰哥网络科技
echo     QQ: 2711793818 杰哥
echo ========================================
echo.

set BASE_DIR=%~dp0
cd /d %BASE_DIR%

echo [1/4] 正在编译 PC端项目...
echo ----------------------------------------
cd pc
if not exist node_modules (
    echo 正在安装依赖...
    call npm install
    if errorlevel 1 (
        echo ❌ PC端依赖安装失败
        exit /b 1
    )
)
echo 正在编译...
call npm run build
if errorlevel 1 (
    echo ❌ PC端编译失败
    exit /b 1
)
echo ✅ PC端编译成功
cd ..
echo.

echo [2/4] 正在编译 UniApp项目(H5)...
echo ----------------------------------------
cd uniapp
if not exist node_modules (
    echo 正在安装依赖...
    call npm install
    if errorlevel 1 (
        echo ❌ UniApp依赖安装失败
        exit /b 1
    )
)
echo 正在编译...
call npm run build:h5
if errorlevel 1 (
    echo ❌ UniApp编译失败
    exit /b 1
)
echo ✅ UniApp编译成功
cd ..
echo.

echo [3/4] 正在编译 管理后台...
echo ----------------------------------------
cd admin
if not exist node_modules (
    echo 正在安装依赖...
    call npm install
    if errorlevel 1 (
        echo ❌ 管理后台依赖安装失败
        exit /b 1
    )
)
echo 正在编译...
call npm run build
if errorlevel 1 (
    echo ❌ 管理后台编译失败
    exit /b 1
)
echo ✅ 管理后台编译成功
cd ..
echo.

echo [4/4] 清理ThinkPHP缓存...
echo ----------------------------------------
cd server
if exist runtime (
    rmdir /s /q runtime
    echo ✅ 缓存清理成功
) else (
    echo 无需清理缓存
)
cd ..
echo.

echo ========================================
echo     所有项目编译完成！
echo ========================================
echo.
echo 编译输出目录：
echo   - PC端: likeadmin_php-master/server/public/pc/
echo   - UniApp: likeadmin_php-master/server/public/mobile/
echo   - 管理后台: likeadmin_php-master/server/public/admin/
echo.
pause
