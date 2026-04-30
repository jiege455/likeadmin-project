<?php
/**
 * 清理缓存并分配商户管理权限
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */

$config = [
    'host' => '127.0.0.1',
    'port' => 3306,
    'database' => '127_0_0_1',
    'username' => '127_0_0_1',
    'password' => 'Snc6MPime7',
    'charset' => 'utf8mb4',
];

try {
    $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "数据库连接成功\n\n";
} catch (PDOException $e) {
    die("数据库连接失败: " . $e->getMessage() . "\n");
}

// 1. 获取商户管理菜单ID
$stmt = $pdo->query("SELECT id FROM la_system_menu WHERE name = '商户管理' AND pid = 0");
$merchantMenu = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$merchantMenu) {
    die("商户管理菜单不存在\n");
}

$merchantMenuId = $merchantMenu['id'];
echo "商户管理菜单ID: {$merchantMenuId}\n";

// 2. 获取所有子菜单ID
$stmt = $pdo->prepare("SELECT id FROM la_system_menu WHERE pid = ? OR id = ?");
$stmt->execute([$merchantMenuId, $merchantMenuId]);
$menuIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo "找到 " . count($menuIds) . " 个菜单项\n";

// 3. 获取超级管理员角色ID
$stmt = $pdo->query("SELECT id FROM la_system_role ORDER BY id ASC LIMIT 1");
$role = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$role) {
    // 创建角色
    $pdo->exec("INSERT INTO la_system_role (name, `desc`, sort, create_time, update_time) VALUES ('超级管理员', '拥有所有权限', 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())");
    $roleId = $pdo->lastInsertId();
    echo "创建超级管理员角色，ID: {$roleId}\n";
} else {
    $roleId = $role['id'];
    echo "超级管理员角色ID: {$roleId}\n";
}

// 4. 为角色分配商户管理菜单权限
$inserted = 0;
foreach ($menuIds as $menuId) {
    // 检查是否已分配
    $stmt = $pdo->prepare("SELECT 1 FROM la_system_role_menu WHERE role_id = ? AND menu_id = ?");
    $stmt->execute([$roleId, $menuId]);
    if ($stmt->fetch()) {
        continue;
    }
    
    $stmt = $pdo->prepare("INSERT INTO la_system_role_menu (role_id, menu_id) VALUES (?, ?)");
    $stmt->execute([$roleId, $menuId]);
    $inserted++;
}

echo "分配权限: {$inserted} 条\n";

// 5. 检查管理员是否关联角色
$stmt = $pdo->query("SELECT id FROM la_admin WHERE root = 1 LIMIT 1");
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if ($admin) {
    $adminId = $admin['id'];
    echo "超级管理员ID: {$adminId}\n";
    
    // 检查管理员角色关联
    $stmt = $pdo->prepare("SELECT 1 FROM la_admin_role WHERE admin_id = ? AND role_id = ?");
    $stmt->execute([$adminId, $roleId]);
    if (!$stmt->fetch()) {
        $pdo->prepare("INSERT INTO la_admin_role (admin_id, role_id) VALUES (?, ?)")->execute([$adminId, $roleId]);
        echo "关联管理员角色成功\n";
    } else {
        echo "管理员角色已关联\n";
    }
}

// 6. 清理运行时缓存
$runtimePath = dirname(__DIR__) . '/runtime';
if (is_dir($runtimePath)) {
    $dirs = ['cache', 'temp', 'log'];
    foreach ($dirs as $dir) {
        $dirPath = $runtimePath . '/' . $dir;
        if (is_dir($dirPath)) {
            deleteDir($dirPath);
            echo "清理缓存目录: {$dir}\n";
        }
    }
}

function deleteDir($dir) {
    if (!is_dir($dir)) return;
    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        is_dir($path) ? deleteDir($path) : @unlink($path);
    }
    @rmdir($dir);
}

echo "\n========================================\n";
echo "权限分配完成！\n";
echo "请按以下步骤操作：\n";
echo "1. 退出后台登录\n";
echo "2. 清理浏览器缓存 (Ctrl+Shift+Delete)\n";
echo "3. 重新登录后台\n";
echo "4. 刷新页面查看商户管理菜单\n";
echo "========================================\n";
