<?php
$dsn = 'mysql:host=localhost;port=3306;dbname=hyl_ddgg888_my;charset=utf8mb4';
try {
    $pdo = new PDO($dsn, 'root', 'root');
    
    // 检查 merchant_id 是否存在
    $stmt = $pdo->query("SHOW COLUMNS FROM `la_coupon` LIKE 'merchant_id'");
    $exists = $stmt->fetch();
    
    if (!$exists) {
        $sql = "ALTER TABLE `la_coupon` ADD COLUMN `merchant_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商户ID' AFTER `id`";
        $pdo->exec($sql);
        echo "Added merchant_id column to la_coupon table.\n";
    } else {
        echo "merchant_id column already exists.\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
