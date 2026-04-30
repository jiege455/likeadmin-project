<?php
/**
 * 检查文章水印设置
 */
header('Content-Type: text/html; charset=utf-8');

$envFile = __DIR__ . '/../.env';
$envContent = file_get_contents($envFile);
preg_match('/DATABASE\s*=\s*"([^"]+)"/', $envContent, $dbname);
preg_match('/HOSTNAME\s*=\s*"([^"]+)"/', $envContent, $host);
preg_match('/USERNAME\s*=\s*"([^"]+)"/', $envContent, $username);
preg_match('/PASSWORD\s*=\s*"([^"]+)"/', $envContent, $password);
preg_match('/HOSTPORT\s*=\s*"([^"]+)"/', $envContent, $port);
preg_match('/PREFIX\s*=\s*"([^"]+)"/', $envContent, $prefix);

$host = $host[1] ?? '127.0.0.1';
$port = $port[1] ?? '3306';
$dbname = $dbname[1] ?? '';
$username = $username[1] ?? 'root';
$password = $password[1] ?? '';
$prefix = $prefix[1] ?? 'la_';

$pdo = new PDO("mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "<h2>检查结果</h2>";

echo "<h3>1. 菜单记录 (article_watermark)</h3>";
$stmt = $pdo->query("SELECT id, pid, type, name, paths, component, is_show FROM {$prefix}system_menu WHERE paths = 'article_watermark' OR name = '文章水印设置'");
$menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
if ($menus) {
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>ID</th><th>PID</th><th>类型</th><th>名称</th><th>路由</th><th>组件</th><th>显示</th></tr>";
    foreach ($menus as $m) {
        echo "<tr>";
        echo "<td>{$m['id']}</td>";
        echo "<td>{$m['pid']}</td>";
        echo "<td>{$m['type']}</td>";
        echo "<td>{$m['name']}</td>";
        echo "<td>{$m['paths']}</td>";
        echo "<td>{$m['component']}</td>";
        echo "<td>" . ($m['is_show'] ? '是' : '否') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:red;'>没有找到文章水印设置的菜单记录！</p>";
}

echo "<h3>2. 角色菜单权限</h3>";
$stmt = $pdo->query("SELECT rm.role_id, rm.menu_id, sr.name as role_name FROM {$prefix}system_role_menu rm LEFT JOIN {$prefix}system_role sr ON rm.role_id = sr.id WHERE rm.menu_id = 340");
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
if ($roles) {
    echo "<p style='color:green;'>菜单ID 340 有以下角色权限：</p>";
    foreach ($roles as $r) {
        echo "<p>- 角色ID: {$r['role_id']}, 角色名: {$r['role_name']}</p>";
    }
} else {
    echo "<p style='color:red;'>菜单ID 340 没有分配给任何角色！</p>";
}

echo "<h3>3. 顶级菜单（PID=0）</h3>";
$stmt = $pdo->query("SELECT id, name, icon, sort FROM {$prefix}system_menu WHERE pid = 0 AND is_show = 1 ORDER BY sort");
$topMenus = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<p>系统中有 " . count($topMenus) . " 个顶级菜单</p>";
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr><th>ID</th><th>名称</th><th>排序</th></tr>";
foreach ($topMenus as $m) {
    echo "<tr>";
    echo "<td>{$m['id']}</td>";
    echo "<td>{$m['name']}</td>";
    echo "<td>{$m['sort']}</td>";
    echo "</tr>";
}
echo "</table>";
