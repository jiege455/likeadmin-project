<?php
/**
 * 添加投诉管理菜单脚本
 * 开发者：杰哥网络科技。
 * qq2711793818 杰哥
 * 
 * 使用方法：在浏览器中访问此文件即可
 */

header('Content-Type: text/html; charset=utf-8');

$dbConfig = require __DIR__ . '/config/database.php';

$host = $dbConfig['connections']['mysql']['hostname'] ?? '127.0.0.1';
$port = $dbConfig['connections']['mysql']['hostport'] ?? '3306';
$dbname = $dbConfig['connections']['mysql']['database'] ?? '';
$username = $dbConfig['connections']['mysql']['username'] ?? 'root';
$password = $dbConfig['connections']['mysql']['password'] ?? '';
$prefix = $dbConfig['connections']['mysql']['prefix'] ?? 'la_';

if (empty($dbname)) {
    die("请先配置数据库连接信息");
}

try {
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("数据库连接失败: " . $e->getMessage());
}

echo "<h2>投诉管理菜单修复脚本</h2>";
echo "<p>开发者：杰哥网络科技。 qq2711793818 杰哥</p>";
echo "<hr>";

$tableName = $prefix . 'system_menu';

try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM {$tableName} WHERE `name` = '投诉管理' AND `paths` = 'merchant/complaint'");
    $exists = $stmt->fetchColumn();
    
    if ($exists > 0) {
        echo "<p style='color:green;'>✓ 投诉管理菜单已存在，无需添加。</p>";
    } else {
        echo "<p style='color:orange;'>! 投诉管理菜单不存在，正在添加...</p>";
        
        $stmt = $pdo->query("SELECT id FROM {$tableName} WHERE `name` = '商家管理' AND `type` = 'M' LIMIT 1");
        $merchantMenuId = $stmt->fetchColumn();
        
        if (!$merchantMenuId) {
            $merchantMenuId = 179;
        }
        
        $sql = "INSERT INTO {$tableName} (`id`, `pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`) VALUES
        (225, {$merchantMenuId}, 'C', '投诉管理', 'el-icon-ChatDotRound', 70, 'merchant.complaint/lists', 'merchant/complaint', 'merchant/complaint/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
        (233, 225, 'A', '删除投诉', '', 1, 'merchant.complaint/del', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())";
        
        $pdo->exec($sql);
        echo "<p style='color:green;'>✓ 投诉管理菜单添加成功！</p>";
    }
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM {$tableName} WHERE `name` = '处理投诉' AND `perms` = 'merchant.complaint/handle'");
    $handleExists = $stmt->fetchColumn();
    
    if ($handleExists == 0) {
        $stmt = $pdo->query("SELECT id FROM {$tableName} WHERE `name` = '投诉管理' AND `paths` = 'merchant/complaint' LIMIT 1");
        $complaintMenuId = $stmt->fetchColumn();
        
        if ($complaintMenuId) {
            $pdo->exec("INSERT INTO {$tableName} (`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`) VALUES ({$complaintMenuId}, 'A', '处理投诉', '', 0, 'merchant.complaint/handle', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())");
            echo "<p style='color:green;'>✓ 处理投诉权限按钮添加成功！</p>";
        }
    }
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM {$prefix}system_role");
    $roleCount = $stmt->fetchColumn();
    
    if ($roleCount > 0) {
        echo "<h3>角色权限设置</h3>";
        echo "<p style='color:blue;'>请在后台【权限管理】-【角色】中，为相应角色勾选【商家管理】-【投诉管理】菜单权限。</p>";
    }
    
    echo "<hr>";
    echo "<h3>验证投诉数据</h3>";
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM {$prefix}merchant_complaint");
    $complaintCount = $stmt->fetchColumn();
    echo "<p>当前投诉记录数：<strong>{$complaintCount}</strong> 条</p>";
    
    if ($complaintCount > 0) {
        $stmt = $pdo->query("SELECT * FROM {$prefix}merchant_complaint ORDER BY id DESC LIMIT 5");
        $complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse:collapse;'>";
        echo "<tr><th>ID</th><th>用户ID</th><th>商家ID</th><th>原因</th><th>内容</th><th>状态</th><th>提交时间</th></tr>";
        foreach ($complaints as $row) {
            $status = $row['status'] == 0 ? '待处理' : '已处理';
            $createTime = date('Y-m-d H:i:s', $row['create_time']);
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['user_id']}</td>";
            echo "<td>{$row['merchant_id']}</td>";
            echo "<td>{$row['reason']}</td>";
            echo "<td>" . mb_substr($row['content'], 0, 30) . "...</td>";
            echo "<td>{$status}</td>";
            echo "<td>{$createTime}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<hr>";
    echo "<p style='color:green;'><strong>修复完成！</strong></p>";
    echo "<p>请刷新后台页面，在【商家管理】菜单下查看【投诉管理】。</p>";
    echo "<p>如果仍看不到菜单，请检查管理员角色是否分配了该菜单权限。</p>";
    
} catch (PDOException $e) {
    echo "<p style='color:red;'>错误: " . $e->getMessage() . "</p>";
}
