<?php
$dsn = 'mysql:host=localhost;port=3306;dbname=hyl_ddgg888_my;charset=utf8mb4';
try {
    $pdo = new PDO($dsn, 'root', 'root');
    $sql = "INSERT IGNORE INTO `la_decorate_page` (`id`, `type`, `name`, `data`, `meta`) VALUES 
            (9, 9, '领券中心', '[]', '[]');";
    $pdo->exec($sql);
    echo "Done";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
