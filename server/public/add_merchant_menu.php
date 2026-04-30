<?php
/**
 * 添加商户管理菜单
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

// 检查是否已有商户管理菜单
$stmt = $pdo->query("SELECT * FROM la_system_menu WHERE name = '商户管理' AND pid = 0");
$existing = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing) {
    echo "商户管理菜单已存在，ID: " . $existing['id'] . "\n";
    $merchantMenuId = $existing['id'];
} else {
    // 添加商户管理主菜单
    $sql = "INSERT INTO la_system_menu (pid, type, name, icon, sort, perms, paths, component, selected, params, is_cache, is_show, is_disable, create_time, update_time) 
            VALUES (0, 'M', '商户管理', 'el-icon-Shop', 150, '', 'merchant', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())";
    $pdo->exec($sql);
    $merchantMenuId = $pdo->lastInsertId();
    echo "添加商户管理主菜单成功，ID: " . $merchantMenuId . "\n";
}

// 子菜单配置
$menus = [
    ['name' => '商户列表', 'icon' => 'el-icon-User', 'sort' => 100, 'perms' => 'merchant.merchant/lists', 'paths' => 'lists', 'component' => 'merchant/lists/index'],
    ['name' => '入驻申请', 'icon' => 'el-icon-Document', 'sort' => 90, 'perms' => 'merchant.apply/lists', 'paths' => 'apply', 'component' => 'merchant/apply/index'],
    ['name' => '提现管理', 'icon' => 'el-icon-Money', 'sort' => 80, 'perms' => 'merchant.withdraw/lists', 'paths' => 'withdraw', 'component' => 'merchant/withdraw/index'],
    ['name' => '投诉管理', 'icon' => 'el-icon-ChatDotRound', 'sort' => 70, 'perms' => 'merchant.complaint/lists', 'paths' => 'complaint', 'component' => 'merchant/complaint/index'],
];

foreach ($menus as $menu) {
    // 检查是否已存在
    $stmt = $pdo->prepare("SELECT id FROM la_system_menu WHERE name = ? AND pid = ?");
    $stmt->execute([$menu['name'], $merchantMenuId]);
    if ($stmt->fetch()) {
        echo "菜单 {$menu['name']} 已存在，跳过\n";
        continue;
    }
    
    $sql = "INSERT INTO la_system_menu (pid, type, name, icon, sort, perms, paths, component, selected, params, is_cache, is_show, is_disable, create_time, update_time) 
            VALUES (?, 'C', ?, ?, ?, ?, ?, ?, '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $merchantMenuId,
        $menu['name'],
        $menu['icon'],
        $menu['sort'],
        $menu['perms'],
        $menu['paths'],
        $menu['component']
    ]);
    echo "添加菜单 {$menu['name']} 成功\n";
}

// 添加按钮权限
$buttonPerms = [
    ['name' => '商户详情', 'perms' => 'merchant.merchant/detail'],
    ['name' => '审核商户', 'perms' => 'merchant.merchant/audit'],
    ['name' => '设置状态', 'perms' => 'merchant.merchant/setStatus'],
    ['name' => '审核申请', 'perms' => 'merchant.apply/audit'],
    ['name' => '审核提现', 'perms' => 'merchant.withdraw/audit'],
    ['name' => '处理投诉', 'perms' => 'merchant.complaint/handle'],
];

// 获取商户列表菜单ID
$stmt = $pdo->prepare("SELECT id FROM la_system_menu WHERE name = '商户列表' AND pid = ?");
$stmt->execute([$merchantMenuId]);
$merchantListMenu = $stmt->fetch(PDO::FETCH_ASSOC);

if ($merchantListMenu) {
    foreach ($buttonPerms as $i => $perm) {
        // 检查是否已存在
        $stmt = $pdo->prepare("SELECT id FROM la_system_menu WHERE perms = ?");
        $stmt->execute([$perm['perms']]);
        if ($stmt->fetch()) {
            echo "按钮权限 {$perm['name']} 已存在，跳过\n";
            continue;
        }
        
        $sql = "INSERT INTO la_system_menu (pid, type, name, icon, sort, perms, paths, component, selected, params, is_cache, is_show, is_disable, create_time, update_time) 
                VALUES (?, 'A', ?, '', ?, ?, '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $merchantListMenu['id'],
            $perm['name'],
            $i + 1,
            $perm['perms']
        ]);
        echo "添加按钮权限 {$perm['name']} 成功\n";
    }
}

echo "\n商户管理菜单添加完成！\n";
echo "请刷新后台页面查看效果。\n";
