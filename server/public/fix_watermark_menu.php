<?php
/**
 * 修复文章水印设置菜单
 * 开发者：杰哥网络科技
 * qq2711793818 杰哥
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

echo "<h2>修复文章水印设置菜单</h2>";
echo "<p>开发者：杰哥网络科技。 qq2711793818 杰哥</p>";

// 查找系统设置的ID
$stmt = $pdo->query("SELECT id FROM {$prefix}system_menu WHERE name = '系统设置' LIMIT 1");
$settingId = $stmt->fetchColumn();

if (!$settingId) {
    echo "<p style='color:red;'>找不到【系统设置】菜单！</p>";
    exit;
}

echo "<p>系统设置的ID是: {$settingId}</p>";

// 更新菜单的PID
echo "<h3>1. 更新菜单PID（让菜单位于【系统设置】下）</h3>";
$sql = "UPDATE {$prefix}system_menu SET pid = {$settingId} WHERE id = 340";
$pdo->exec($sql);
echo "<p style='color:green;'>✓ 菜单PID已更新为 {$settingId}（系统设置）</p>";

// 检查系统设置下的子菜单
echo "<h3>2. 系统设置下的子菜单</h3>";
$stmt = $pdo->query("SELECT id, name, sort FROM {$prefix}system_menu WHERE pid = {$settingId} ORDER BY sort");
$childMenus = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr><th>ID</th><th>名称</th><th>排序</th></tr>";
foreach ($childMenus as $m) {
    echo "<tr>";
    echo "<td>{$m['id']}</td>";
    echo "<td>{$m['name']}</td>";
    echo "<td>{$m['sort']}</td>";
    echo "</tr>";
}
echo "</table>";

echo "<hr>";
echo "<p style='color:green;'><strong>修复完成！</strong></p>";
echo "<p>请刷新后台页面，进入【系统设置】查看【文章水印设置】菜单。</p>";
