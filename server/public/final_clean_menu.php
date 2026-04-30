<?php
/**
 * 彻底清理重复菜单
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
} catch (PDOException $e) {
    die("数据库连接失败: " . $e->getMessage() . "\n");
}

echo "=== 彻底清理重复菜单 ===\n\n";

$merchantMenuId = 179;

// 删除旧的路径格式菜单 (merchant/xxx 格式的)
$oldIds = [180, 181, 190];
echo "删除旧的菜单：\n";
foreach ($oldIds as $id) {
    $stmt = $pdo->prepare("SELECT name FROM la_system_menu WHERE id = ?");
    $stmt->execute([$id]);
    $menu = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($menu) {
        echo "  删除: ID {$id}, 名称: {$menu['name']}\n";
        $pdo->prepare("DELETE FROM la_system_role_menu WHERE menu_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM la_system_menu WHERE id = ?")->execute([$id]);
    }
}

// 更新新菜单的路径格式为正确的格式
echo "\n更新菜单路径格式...\n";

$pathUpdates = [
    222 => 'merchant/apply',
    223 => 'merchant/lists',
    224 => 'merchant/withdraw',
    225 => 'merchant/complaint',
];

foreach ($pathUpdates as $id => $newPath) {
    $stmt = $pdo->prepare("UPDATE la_system_menu SET paths = ? WHERE id = ?");
    $stmt->execute([$newPath, $id]);
    echo "  更新 ID {$id} 路径为: {$newPath}\n";
}

// 最终检查
echo "\n=== 最终商家管理菜单结构 ===\n\n";
$stmt = $pdo->prepare("SELECT id, name, paths, perms, sort FROM la_system_menu WHERE pid = ? ORDER BY sort DESC");
$stmt->execute([$merchantMenuId]);
$menus = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "商家管理 (ID: {$merchantMenuId})\n";
foreach ($menus as $menu) {
    echo "  └─ ID: {$menu['id']}, 名称: {$menu['name']}, 路径: {$menu['paths']}, 权限: {$menu['perms']}\n";
}

// 清理缓存
$runtimePath = dirname(__DIR__) . '/runtime';
if (is_dir($runtimePath)) {
    foreach (['cache', 'temp', 'log'] as $dir) {
        $dirPath = $runtimePath . '/' . $dir;
        if (is_dir($dirPath)) {
            $files = glob($dirPath . '/*');
            foreach ($files as $file) {
                if (is_file($file)) @unlink($file);
            }
        }
    }
}

echo "\n========================================\n";
echo "清理完成！请重新登录后台查看\n";
echo "========================================\n";
