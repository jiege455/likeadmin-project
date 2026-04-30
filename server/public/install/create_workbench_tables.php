<?php
// 执行工作台所需的数据库表创建脚本

$servername = "127.0.0.1";
$username = "127_0_0_1";
$password = "Snc6MPime7";
$dbname = "127_0_0_1";
$charset = "utf8mb4";

// 创建数据库连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接
if ($conn->connect_error) {
    die("连接失败：" . $conn->connect_error);
}

$conn->set_charset($charset);

echo "=== 开始创建工作台所需的数据表 ===\n\n";

// 读取 SQL 文件
$sqlFile = __DIR__ . '/db/workbench_tables.sql';
if (!file_exists($sqlFile)) {
    die("SQL 文件不存在：$sqlFile\n");
}

$sqlContent = file_get_contents($sqlFile);

// 分割 SQL 语句（按分号分割）
$sqlStatements = array_filter(array_map('trim', explode(';', $sqlContent)));

$successCount = 0;
$errorCount = 0;

// 执行每个 SQL 语句
foreach ($sqlStatements as $index => $sql) {
    if (empty($sql)) {
        continue; // 跳过空语句
    }
    
    // 跳过纯注释
    $trimmedSql = preg_replace('/--.*$/m', '', $sql);
    $trimmedSql = trim($trimmedSql);
    
    if (empty($trimmedSql)) {
        continue;
    }
    
    if ($conn->query($trimmedSql)) {
        // 提取表名
        preg_match('/CREATE TABLE.*?`([^`]+)`/', $trimmedSql, $matches);
        $tableName = $matches[1] ?? '未知表';
        echo "✓ 成功创建/验证表：{$tableName}\n";
        $successCount++;
    } else {
        $error = $conn->error;
        // 如果表已存在，不算错误
        if (strpos($error, 'already exists') !== false) {
            preg_match('/CREATE TABLE.*?`([^`]+)`/', $trimmedSql, $matches);
            $tableName = $matches[1] ?? '未知表';
            echo "✓ 表已存在：{$tableName}\n";
            $successCount++;
        } else {
            echo "✗ 执行失败：{$error}\n";
            echo "  SQL: " . substr($trimmedSql, 0, 100) . "...\n\n";
            $errorCount++;
        }
    }
}

echo "\n=== 执行完成 ===\n";
echo "成功：{$successCount} 个表\n";
echo "失败：{$errorCount} 个表\n";

$conn->close();
