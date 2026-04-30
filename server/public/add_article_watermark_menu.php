<?php
/**
 * 添加文章水印设置菜单脚本
 * 开发者：杰哥网络科技。
 * qq2711793818 杰哥
 *
 * 使用方法：在浏览器中访问此文件即可
 */

header('Content-Type: text/html; charset=utf-8');

$envFile = __DIR__ . '/../.env';
if (!file_exists($envFile)) {
    die("找不到 .env 配置文件");
}

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

if (empty($dbname)) {
    die("请先配置数据库连接信息");
}

try {
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("数据库连接失败: " . $e->getMessage());
}

echo "<h2>文章水印设置菜单安装脚本</h2>";
echo "<p>开发者：杰哥网络科技。 qq2711793818 杰哥</p>";
echo "<hr>";

$menuTable = $prefix . 'system_menu';
$configTable = $prefix . 'config';
$roleMenuTable = $prefix . 'system_role_menu';

try {
    echo "<h3>1. 检查并添加菜单</h3>";

    $stmt = $pdo->query("SELECT COUNT(*) FROM {$menuTable} WHERE `name` = '文章水印设置' AND `paths` = 'article_watermark'");
    $exists = $stmt->fetchColumn();

    if ($exists > 0) {
        echo "<p style='color:green;'>✓ 文章水印设置菜单已存在，无需添加。</p>";
    } else {
        echo "<p style='color:orange;'>! 文章水印设置菜单不存在，正在添加...</p>";

        $sql = "INSERT INTO {$menuTable} (`id`, `pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`) VALUES
        (340, 0, 'C', '文章水印设置', '', 45, 'setting.article_watermark/getConfig', 'article_watermark', 'setting/article_watermark/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
        (341, 340, 'A', '获取配置', '', 0, 'setting.article_watermark/getConfig', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
        (342, 340, 'A', '保存配置', '', 0, 'setting.article_watermark/setConfig', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())";

        $pdo->exec($sql);
        echo "<p style='color:green;'>✓ 文章水印设置菜单添加成功！</p>";
    }

    echo "<h3>2. 检查并添加默认配置</h3>";

    $stmt = $pdo->query("SELECT COUNT(*) FROM {$configTable} WHERE `type` = 'article_watermark'");
    $configExists = $stmt->fetchColumn();

    if ($configExists > 0) {
        echo "<p style='color:green;'>✓ 文章水印配置已存在，无需添加。</p>";
    } else {
        echo "<p style='color:orange;'>! 文章水印配置不存在，正在添加...</p>";

        $sql = "INSERT INTO {$configTable} (`type`, `name`, `value`, `create_time`) VALUES
        ('article_watermark', 'enable', '0', UNIX_TIMESTAMP()),
        ('article_watermark', 'text', '杰哥网络科技', UNIX_TIMESTAMP()),
        ('article_watermark', 'contact', 'QQ:2711793818', UNIX_TIMESTAMP()),
        ('article_watermark', 'position', 'right_bottom', UNIX_TIMESTAMP()),
        ('article_watermark', 'opacity', '0.15', UNIX_TIMESTAMP())";

        $pdo->exec($sql);
        echo "<p style='color:green;'>✓ 文章水印配置添加成功！</p>";
    }

    echo "<h3>3. 为管理员角色添加菜单权限</h3>";

    $stmt = $pdo->query("SELECT role_id FROM {$roleMenuTable} WHERE menu_id = 340 LIMIT 1");
    $hasRoleMenu = $stmt->fetchColumn();

    if ($hasRoleMenu) {
        echo "<p style='color:green;'>✓ 管理员已有文章水印设置菜单权限。</p>";
    } else {
        $stmt = $pdo->query("SELECT id FROM {$prefix}system_role LIMIT 1");
        $adminRoleId = $stmt->fetchColumn();

        if ($adminRoleId) {
            $pdo->exec("INSERT INTO {$roleMenuTable} (`role_id`, `menu_id`) VALUES ({$adminRoleId}, 340), ({$adminRoleId}, 341), ({$adminRoleId}, 342)");
            echo "<p style='color:green;'>✓ 管理员角色（ID:{$adminRoleId}）已添加文章水印设置菜单权限！</p>";
        } else {
            echo "<p style='color:orange;'>! 未找到管理员角色，请手动在后台分配权限。</p>";
        }
    }

    echo "<hr>";
    echo "<h3>4. 验证结果</h3>";

    $stmt = $pdo->query("SELECT * FROM {$menuTable} WHERE `name` = '文章水印设置'");
    $menu = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($menu) {
        echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse:collapse;'>";
        echo "<tr><th>ID</th><th>名称</th><th>路由</th><th>组件</th><th>显示</th></tr>";
        echo "<tr>";
        echo "<td>{$menu['id']}</td>";
        echo "<td>{$menu['name']}</td>";
        echo "<td>{$menu['paths']}</td>";
        echo "<td>{$menu['component']}</td>";
        echo "<td>" . ($menu['is_show'] ? '是' : '否') . "</td>";
        echo "</tr>";
        echo "</table>";
    }

    $stmt = $pdo->query("SELECT * FROM {$configTable} WHERE `type` = 'article_watermark'");
    $configs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($configs) {
        echo "<br><p>水印配置：</p>";
        echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse:collapse;'>";
        echo "<tr><th>配置项</th><th>值</th></tr>";
        foreach ($configs as $cfg) {
            echo "<tr>";
            echo "<td>{$cfg['name']}</td>";
            echo "<td>{$cfg['value']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    echo "<hr>";
    echo "<p style='color:green;'><strong>安装完成！</strong></p>";
    echo "<p>请刷新后台页面，在【系统设置】菜单下查看【文章水印设置】。</p>";
    echo "<p>如果仍看不到菜单，请尝试：</p>";
    echo "<ol>";
    echo "<li>清除浏览器缓存</li>";
    echo "<li>重新登录后台</li>";
    echo "<li>在【权限管理】-【角色】中检查管理员权限</li>";
    echo "</ol>";

} catch (PDOException $e) {
    echo "<p style='color:red;'>错误: " . $e->getMessage() . "</p>";
}
