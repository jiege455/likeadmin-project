<?php
/**
 * 修复文章相关表字段
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */

header('Content-Type:text/plain;charset=utf-8');

$host = '127.0.0.1';
$dbname = 'hyl';
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "连接数据库成功\n\n";

    $tables = [
        'la_article' => [
            'prev_issue_no' => "ALTER TABLE `la_article` ADD `prev_issue_no` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '上一期期号' AFTER `issue_no`",
            'prev_issue_content' => "ALTER TABLE `la_article` ADD `prev_issue_content` TEXT COMMENT '上一期内容' AFTER `hidden_content`",
            'is_series' => "ALTER TABLE `la_article` ADD `is_series` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否系列文章:0-否,1-是' AFTER `desc`",
        ],
        'la_article_cate' => [
            'published_issues' => "ALTER TABLE `la_article_cate` ADD `published_issues` INT(11) NOT NULL DEFAULT 0 COMMENT '已发布期数' AFTER `total_issues`",
        ],
        'la_merchant_follow' => [
            'push_enable' => "ALTER TABLE `la_merchant_follow` ADD `push_enable` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '推送开关:0-关闭,1-开启' AFTER `merchant_id`",
        ],
    ];

    $totalFixed = 0;

    foreach ($tables as $tableName => $fields) {
        echo "检查表: $tableName\n";

        $stmt = $pdo->query("SHOW COLUMNS FROM $tableName");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($fields as $fieldName => $sql) {
            if (in_array($fieldName, $columns)) {
                echo "  - $fieldName: 已存在，跳过\n";
            } else {
                echo "  - $fieldName: 缺失，执行SQL...\n";
                try {
                    $pdo->exec($sql);
                    echo "    成功!\n";
                    $totalFixed++;
                } catch (PDOException $e) {
                    echo "    失败: " . $e->getMessage() . "\n";
                }
            }
        }
        echo "\n";
    }

    if ($totalFixed == 0) {
        echo "没有需要执行的SQL，所有字段都已存在。\n";
    } else {
        echo "共执行了 $totalFixed 条SQL语句。\n";
    }

    echo "\n修复后的表结构:\n";
    foreach (array_keys($tables) as $tableName) {
        $stmt = $pdo->query("SHOW COLUMNS FROM $tableName");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "$tableName: " . implode(', ', $columns) . "\n";
    }

} catch (PDOException $e) {
    echo "数据库错误: " . $e->getMessage() . "\n";
}
