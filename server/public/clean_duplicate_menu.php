<?php
/**
 * 清理重复的商户管理菜单
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

echo "=== 清理重复菜单 ===\n\n";

// 保留 ID 179 的"商家管理"，删除 ID 209 的"商户管理"
$keepId = 179;
$deleteId = 209;

// 1. 获取要删除的菜单及其子菜单
$stmt = $pdo->prepare("SELECT id, name FROM la_system_menu WHERE id = ? OR pid = ?");
$stmt->execute([$deleteId, $deleteId]);
$toDelete = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "将要删除的菜单：\n";
foreach ($toDelete as $menu) {
    echo "  - ID: {$menu['id']}, 名称: {$menu['name']}\n";
}

// 2. 删除角色菜单关联
$ids = array_column($toDelete, 'id');
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = $pdo->prepare("DELETE FROM la_system_role_menu WHERE menu_id IN ($placeholders)");
$stmt->execute($ids);
echo "\n删除角色菜单关联: " . $stmt->rowCount() . " 条\n";

// 3. 删除菜单（先删子菜单，再删父菜单）
$stmt = $pdo->prepare("DELETE FROM la_system_menu WHERE pid = ?");
$stmt->execute([$deleteId]);
echo "删除子菜单: " . $stmt->rowCount() . " 条\n";

$stmt = $pdo->prepare("DELETE FROM la_system_menu WHERE id = ?");
$stmt->execute([$deleteId]);
echo "删除主菜单: " . $stmt->rowCount() . " 条\n";

// 4. 检查保留的菜单是否完整
echo "\n=== 检查保留的商家管理菜单 ===\n\n";

$stmt = $pdo->prepare("SELECT id, pid, name, paths, perms, type FROM la_system_menu WHERE id = ? OR pid = ? ORDER BY id");
$stmt->execute([$keepId, $keepId]);
$menus = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "商家管理菜单：\n";
foreach ($menus as $menu) {
    $typeText = ['M' => '目录', 'C' => '菜单', 'A' => '按钮'][$menu['type']] ?? $menu['type'];
    echo "  ID: {$menu['id']}, 类型: {$typeText}, 名称: {$menu['name']}, 路径: {$menu['paths']}\n";
}

// 5. 确保商家管理菜单有完整的子菜单
$existingChildren = [];
$stmt = $pdo->prepare("SELECT paths FROM la_system_menu WHERE pid = ?");
$stmt->execute([$keepId]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $existingChildren[] = $row['paths'];
}

echo "\n现有子菜单路径: " . implode(', ', $existingChildren) . "\n";

// 添加缺失的子菜单
$requiredMenus = [
    ['name' => '入驻申请', 'icon' => 'el-icon-Document', 'sort' => 100, 'perms' => 'merchant.apply/lists', 'paths' => 'apply', 'component' => 'merchant/apply/index'],
    ['name' => '商户列表', 'icon' => 'el-icon-User', 'sort' => 90, 'perms' => 'merchant.merchant/lists', 'paths' => 'lists', 'component' => 'merchant/lists/index'],
    ['name' => '提现管理', 'icon' => 'el-icon-Money', 'sort' => 80, 'perms' => 'merchant.withdraw/lists', 'paths' => 'withdraw', 'component' => 'merchant/withdraw/index'],
    ['name' => '投诉管理', 'icon' => 'el-icon-ChatDotRound', 'sort' => 70, 'perms' => 'merchant.complaint/lists', 'paths' => 'complaint', 'component' => 'merchant/complaint/index'],
];

foreach ($requiredMenus as $menu) {
    if (!in_array($menu['paths'], $existingChildren)) {
        $sql = "INSERT INTO la_system_menu (pid, type, name, icon, sort, perms, paths, component, selected, params, is_cache, is_show, is_disable, create_time, update_time) 
                VALUES (?, 'C', ?, ?, ?, ?, ?, ?, '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $keepId,
            $menu['name'],
            $menu['icon'],
            $menu['sort'],
            $menu['perms'],
            $menu['paths'],
            $menu['component']
        ]);
        echo "添加菜单: {$menu['name']}\n";
    }
}

// 6. 清理缓存
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
    echo "\n缓存已清理\n";
}

echo "\n========================================\n";
echo "清理完成！\n";
echo "请重新登录后台查看效果\n";
echo "========================================\n";
