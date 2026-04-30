<?php
$dsn = 'mysql:host=127.0.0.1;port=3306;dbname=hyl_ddgg888_my;charset=utf8mb4';
try {
    $pdo = new PDO($dsn, 'root', 'root');
    $sql = "INSERT IGNORE INTO `la_decorate_page` (`id`, `type`, `name`, `data`, `meta`) VALUES (4, 4, '订单中心', '[]', '[]')";
    $pdo->exec($sql);
    echo "Done";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
