<?php
/**
 * 商户模块数据库升级脚本
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 * 
 * 使用方法：在浏览器中访问此文件
 * 例如：http://localhost/upgrade_merchant_db.php
 */

// 数据库配置
$config = [
    'host' => '127.0.0.1',
    'port' => 3306,
    'database' => '127_0_0_1',
    'username' => '127_0_0_1',
    'password' => 'Snc6MPime7',
    'charset' => 'utf8mb4',
    'prefix' => 'la_'
];

// 连接数据库
try {
    $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<h2>数据库连接成功</h2>";
} catch (PDOException $e) {
    die("<h2>数据库连接失败: " . $e->getMessage() . "</h2>");
}

// SQL语句列表
$sqls = [
    // 1. 商户表添加审核字段
    "ALTER TABLE `la_merchant` ADD COLUMN `audit_time` int(10) DEFAULT NULL COMMENT '审核时间' AFTER `status`",
    "ALTER TABLE `la_merchant` ADD COLUMN `audit_reason` varchar(255) DEFAULT '' COMMENT '审核原因' AFTER `audit_time`",
    
    // 2. 商户表（如果不存在则创建）
    "CREATE TABLE IF NOT EXISTS `la_merchant` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `user_id` int(11) UNSIGNED NOT NULL COMMENT '关联用户ID',
      `name` varchar(100) NOT NULL DEFAULT '' COMMENT '商户名称',
      `shop_name` varchar(100) NOT NULL DEFAULT '' COMMENT '店铺名称',
      `image` varchar(255) NOT NULL DEFAULT '' COMMENT '商户头像',
      `cover` varchar(255) NOT NULL DEFAULT '' COMMENT '店铺封面',
      `desc` varchar(255) DEFAULT '' COMMENT '商户简介',
      `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '联系电话',
      `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '当前余额',
      `total_income` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '累计收入',
      `distribution_switch` tinyint(1) NOT NULL DEFAULT '1' COMMENT '分销开关:0-关闭,1-开启',
      `distribution_ratio` decimal(5,2) NOT NULL DEFAULT '10.00' COMMENT '默认分销比例(%)',
      `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态:0-待审核,1-正常,2-已拒绝,3-已禁用',
      `audit_time` int(10) DEFAULT NULL COMMENT '审核时间',
      `audit_reason` varchar(255) DEFAULT '' COMMENT '审核原因',
      `create_time` int(10) DEFAULT NULL,
      `update_time` int(10) DEFAULT NULL,
      `delete_time` int(10) DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户表'",
    
    // 3. 商户入驻申请表
    "CREATE TABLE IF NOT EXISTS `la_merchant_apply` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `user_id` int(11) UNSIGNED NOT NULL COMMENT '申请用户ID',
      `name` varchar(100) NOT NULL DEFAULT '' COMMENT '商户名称',
      `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '联系电话',
      `wechat` varchar(50) DEFAULT '' COMMENT '微信号',
      `desc` varchar(255) DEFAULT '' COMMENT '简介',
      `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态:0-待审核,1-通过,2-拒绝',
      `audit_remark` varchar(255) DEFAULT '' COMMENT '审核备注',
      `create_time` int(10) DEFAULT NULL,
      `update_time` int(10) DEFAULT NULL,
      `delete_time` int(10) DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户入驻申请表'",
    
    // 4. 商户关注表
    "CREATE TABLE IF NOT EXISTS `la_merchant_follow` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
      `merchant_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商家ID',
      `create_time` int(10) DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `user_id` (`user_id`),
      KEY `merchant_id` (`merchant_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商家关注表'",
    
    // 5. 商户投诉表
    "CREATE TABLE IF NOT EXISTS `la_merchant_complaint` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `user_id` int(11) NOT NULL COMMENT '用户ID',
      `merchant_id` int(11) NOT NULL COMMENT '商家ID',
      `content` text NOT NULL COMMENT '投诉内容',
      `images` text COMMENT '图片凭证',
      `contact` varchar(50) DEFAULT '' COMMENT '联系方式',
      `status` tinyint(1) DEFAULT '0' COMMENT '状态:0=待处理,1=已处理',
      `create_time` int(11) DEFAULT NULL,
      `update_time` int(11) DEFAULT NULL,
      `delete_time` int(11) DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `user_id` (`user_id`),
      KEY `merchant_id` (`merchant_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商家投诉表'",
    
    // 6. 商户资金明细表
    "CREATE TABLE IF NOT EXISTS `la_merchant_income_log` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `merchant_id` int(11) UNSIGNED NOT NULL COMMENT '商户ID',
      `source_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '来源类型:1-文章,2-课程',
      `source_id` int(11) NOT NULL DEFAULT '0' COMMENT '来源ID(文章ID或课程ID)',
      `amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变动金额',
      `platform_ratio` decimal(5,2) DEFAULT '0.00' COMMENT '平台抽成比例%',
      `remark` varchar(255) DEFAULT '' COMMENT '备注',
      `create_time` int(10) DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商户资金明细表'",
    
    // 7. 文章订单表
    "CREATE TABLE IF NOT EXISTS `la_article_order` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
      `order_sn` varchar(32) NOT NULL DEFAULT '' COMMENT '订单编号',
      `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
      `article_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '文章ID',
      `merchant_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商家ID',
      `distributor_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分销员ID',
      `order_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '订单金额',
      `distribution_ratio` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT '分销比例(%)',
      `distribution_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '分销佣金',
      `platform_ratio` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT '平台抽成比例(%)',
      `platform_profit` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '平台收益',
      `merchant_profit` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商户收益',
      `pay_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '支付状态:0-未支付,1-已支付',
      `pay_time` int(11) unsigned DEFAULT NULL COMMENT '支付时间',
      `transaction_id` varchar(64) DEFAULT '' COMMENT '第三方支付流水号',
      `create_time` int(11) unsigned DEFAULT NULL COMMENT '创建时间',
      `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
      `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
      PRIMARY KEY (`id`),
      UNIQUE KEY `uk_order_sn` (`order_sn`),
      KEY `idx_user_article` (`user_id`,`article_id`),
      KEY `idx_merchant` (`merchant_id`),
      KEY `idx_distributor` (`distributor_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文章购买订单表'",
    
    // 8. 提现申请表
    "CREATE TABLE IF NOT EXISTS `la_withdraw_apply` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID(推广员)',
      `merchant_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商户ID',
      `source` tinyint(1) NOT NULL DEFAULT '1' COMMENT '来源:1-商户收益,2-推广佣金',
      `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '提现金额',
      `left_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '提现后余额',
      `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '提现方式:1-微信,2-支付宝,3-银行卡',
      `account_info` text COMMENT '账户信息JSON',
      `bank_name` varchar(100) DEFAULT '' COMMENT '银行名称',
      `bank_branch` varchar(100) DEFAULT '' COMMENT '开户支行',
      `bank_account` varchar(50) DEFAULT '' COMMENT '银行账号',
      `bank_user` varchar(50) DEFAULT '' COMMENT '持卡人',
      `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态:0-待审核,1-已拒绝,2-已通过,3-已打款',
      `audit_time` int(10) DEFAULT NULL COMMENT '审核时间',
      `audit_reason` varchar(255) DEFAULT '' COMMENT '拒绝原因',
      `create_time` int(10) DEFAULT NULL,
      `update_time` int(10) DEFAULT NULL,
      `delete_time` int(10) DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `idx_user_id` (`user_id`),
      KEY `idx_merchant_id` (`merchant_id`),
      KEY `idx_source` (`source`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='提现申请表'",
    
    // 9. 收款账户表
    "CREATE TABLE IF NOT EXISTS `la_withdraw_account` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID（推广员）',
      `merchant_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商户ID',
      `type` tinyint(1) NOT NULL DEFAULT '2' COMMENT '账户类型：2-支付宝，3-银行卡',
      `account` varchar(100) NOT NULL DEFAULT '' COMMENT '账号',
      `real_name` varchar(50) NOT NULL DEFAULT '' COMMENT '真实姓名',
      `bank_name` varchar(100) DEFAULT '' COMMENT '银行名称',
      `bank_branch` varchar(100) DEFAULT '' COMMENT '开户支行',
      `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否默认：0-否，1-是',
      `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：0-禁用，1-启用',
      `create_time` int(10) DEFAULT NULL,
      `update_time` int(10) DEFAULT NULL,
      `delete_time` int(10) DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `idx_user_id` (`user_id`),
      KEY `idx_merchant_id` (`merchant_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='收款账户表'",
    
    // 10. 分销员申请表
    "CREATE TABLE IF NOT EXISTS `la_distribution_apply` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `user_id` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
      `name` varchar(32) NOT NULL DEFAULT '' COMMENT '真实姓名',
      `mobile` varchar(32) NOT NULL DEFAULT '' COMMENT '手机号',
      `reason` varchar(255) NOT NULL DEFAULT '' COMMENT '申请原因',
      `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态: 0-待审核, 1-审核通过, 2-审核拒绝',
      `audit_remark` varchar(255) DEFAULT '' COMMENT '审核备注',
      `audit_time` int(10) DEFAULT NULL COMMENT '审核时间',
      `create_time` int(10) DEFAULT NULL,
      `update_time` int(10) DEFAULT NULL,
      `delete_time` int(10) DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `user_id` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='分销员申请表'",
    
    // 11. 分销记录表
    "CREATE TABLE IF NOT EXISTS `la_distribution_log` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `user_id` int(11) UNSIGNED NOT NULL COMMENT '获得佣金的用户ID(推广员)',
      `source_user_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '贡献佣金的用户ID(下单人)',
      `order_id` int(11) DEFAULT '0' COMMENT '关联订单ID',
      `order_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '订单类型:1-文章订单',
      `amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '佣金金额',
      `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态:0-待结算,1-已结算',
      `create_time` int(10) DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `idx_user_id` (`user_id`),
      KEY `idx_order` (`order_id`, `order_type`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='分销记录表'",
    
    // 12. 用户商家绑定关系表
    "CREATE TABLE IF NOT EXISTS `la_user_merchant` (
      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
      `merchant_id` int(11) NOT NULL DEFAULT '0' COMMENT '商家ID',
      `inviter_id` int(11) NOT NULL DEFAULT '0' COMMENT '邀请人ID',
      `create_time` int(11) DEFAULT NULL,
      `update_time` int(11) DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户商家绑定关系表'",
];

// 执行SQL
echo "<h3>开始执行SQL升级...</h3>";
echo "<ul>";
$success = 0;
$failed = 0;

foreach ($sqls as $index => $sql) {
    try {
        $pdo->exec($sql);
        echo "<li style='color:green'>✓ SQL " . ($index + 1) . " 执行成功</li>";
        $success++;
    } catch (PDOException $e) {
        // 如果是字段已存在的错误，忽略
        if (strpos($e->getMessage(), 'Duplicate column') !== false || 
            strpos($e->getMessage(), 'already exists') !== false) {
            echo "<li style='color:orange'>⚠ SQL " . ($index + 1) . " 字段/表已存在，跳过</li>";
            $success++;
        } else {
            echo "<li style='color:red'>✗ SQL " . ($index + 1) . " 执行失败: " . $e->getMessage() . "</li>";
            $failed++;
        }
    }
}
echo "</ul>";

// 检查表是否存在
echo "<h3>检查数据表...</h3>";
$tables = [
    'la_merchant',
    'la_merchant_apply',
    'la_merchant_follow',
    'la_merchant_complaint',
    'la_merchant_income_log',
    'la_article_order',
    'la_withdraw_apply',
    'la_withdraw_account',
    'la_distribution_apply',
    'la_distribution_log',
    'la_user_merchant'
];

echo "<ul>";
foreach ($tables as $table) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "<li style='color:green'>✓ 表 $table 存在</li>";
        } else {
            echo "<li style='color:red'>✗ 表 $table 不存在</li>";
        }
    } catch (PDOException $e) {
        echo "<li style='color:red'>✗ 检查表 $table 失败: " . $e->getMessage() . "</li>";
    }
}
echo "</ul>";

echo "<h2 style='color:green'>升级完成！成功: $success, 失败: $failed</h2>";
echo "<p>开发者：杰哥网络科技 QQ：2711793818 杰哥</p>";
