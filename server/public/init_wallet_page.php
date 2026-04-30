<?php
$dsn = 'mysql:host=127.0.0.1;port=3306;dbname=hyl_ddgg888_my;charset=utf8mb4';
try {
    $pdo = new PDO($dsn, 'root', 'root');
    $sql = "INSERT IGNORE INTO `la_decorate_page` (`id`, `type`, `name`, `data`, `meta`) VALUES 
            (8, 8, '个人钱包', '[]', '[]');";
    $pdo->exec($sql);
    echo "Done";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
