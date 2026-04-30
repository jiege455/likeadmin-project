<?php
/**
 * 测试水印API
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

echo "<h2>检查水印配置</h2>";

$stmt = $pdo->query("SELECT * FROM {$prefix}config WHERE type = 'article_watermark'");
$configs = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($configs) {
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>配置项</th><th>值</th></tr>";
    foreach ($configs as $cfg) {
        echo "<tr>";
        echo "<td>{$cfg['name']}</td>";
        echo "<td>{$cfg['value']}</td>";
        echo "</tr>";
    }
    echo "</table>";

    $enable = 0;
    $text = '';
    $contact = '';
    foreach ($configs as $cfg) {
        if ($cfg['name'] === 'enable') $enable = $cfg['value'];
        if ($cfg['name'] === 'text') $text = $cfg['value'];
        if ($cfg['name'] === 'contact') $contact = $cfg['value'];
    }

    echo "<br><h3>JSON格式的水印配置：</h3>";
    echo "<pre>";
    echo json_encode([
        'enable' => (int)$enable,
        'text' => $text,
        'contact' => $contact,
        'position' => 'tile',
        'opacity' => 0.15
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    echo "</pre>";

    if ($enable == 1) {
        echo "<p style='color:green;'>✓ 水印已开启</p>";
    } else {
        echo "<p style='color:red;'>✗ 水印未开启（enable = 0）</p>";
    }
} else {
    echo "<p style='color:red;'>没有找到水印配置！</p>";
}
