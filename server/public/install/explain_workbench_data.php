<?php
// 查看工作台数据的详细来源
$conn = new mysqli('127.0.0.1', '127_0_0_1', 'Snc6MPime7', '127_0_0_1');
$conn->set_charset('utf8mb4');

echo "===========================================\n";
echo "    工作台数据来源详细说明\n";
echo "===========================================\n\n";

// 1. 销售额
echo "【1】销售额（今日/总计）\n";
echo "数据来源：3 个订单表的已支付金额总和\n";
echo "  - la_article_order (文章购买订单)\n";
echo "  - la_series_order (系列课程订单)\n";
echo "  - la_recharge_order (充值订单)\n\n";

$result = $conn->query("SELECT COUNT(*) as count, SUM(order_amount) as total FROM la_article_order WHERE pay_status=1");
$row = $result->fetch_assoc();
echo "  文章订单：{$row['count']} 笔，金额：" . number_format($row['total'] ?? 0, 2) . " 元\n";

$result = $conn->query("SELECT COUNT(*) as count, SUM(order_amount) as total FROM la_series_order WHERE pay_status=1");
$row = $result->fetch_assoc();
echo "  系列订单：{$row['count']} 笔，金额：" . number_format($row['total'] ?? 0, 2) . " 元\n";

$result = $conn->query("SELECT COUNT(*) as count, SUM(order_amount) as total FROM la_recharge_order WHERE pay_status=1");
$row = $result->fetch_assoc();
echo "  充值订单：{$row['count']} 笔，金额：" . number_format($row['total'] ?? 0, 2) . " 元\n";

// 2. 订单量
echo "\n【2】成交订单（今日/总计）\n";
echo "数据来源：3 个订单表的已支付订单数量\n";
echo "  - la_article_order (文章购买订单)\n";
echo "  - la_series_order (系列课程订单)\n";
echo "  - la_recharge_order (充值订单)\n\n";

$result = $conn->query("SELECT COUNT(*) as count FROM la_article_order WHERE pay_status=1");
$row = $result->fetch_assoc();
echo "  文章订单：{$row['count']} 笔\n";

$result = $conn->query("SELECT COUNT(*) as count FROM la_series_order WHERE pay_status=1");
$row = $result->fetch_assoc();
echo "  系列订单：{$row['count']} 笔\n";

$result = $conn->query("SELECT COUNT(*) as count FROM la_recharge_order WHERE pay_status=1");
$row = $result->fetch_assoc();
echo "  充值订单：{$row['count']} 笔\n";

// 3. 新增用户
echo "\n【3】新增用户（今日/总计）\n";
echo "数据来源：la_user 表\n";
echo "  统计字段：create_time (用户创建时间)\n\n";

$result = $conn->query("SELECT COUNT(*) as count FROM la_user");
$row = $result->fetch_assoc();
echo "  总用户数：{$row['count']} 人\n";

// 4. 访问量
echo "\n【4】新增访问量（今日/总计）\n";
echo "数据来源：la_user_session 表（用户登录会话）\n";
echo "  统计字段：update_time (会话更新时间)\n\n";

$result = $conn->query("SELECT COUNT(*) as count FROM la_user_session");
$row = $result->fetch_assoc();
echo "  总会话数：{$row['count']} 次\n";

// 5. 访客趋势图
echo "\n【5】访问量趋势图（近 15 天）\n";
echo "数据来源：la_user_session 表\n";
echo "  按天统计每天的会话数量\n\n";

// 6. 销售额趋势图
echo "\n【6】销售额趋势图（近 7 天）\n";
echo "数据来源：3 个订单表的已支付金额\n";
echo "  - la_article_order (文章购买订单)\n";
echo "  - la_series_order (系列课程订单)\n";
echo "  - la_recharge_order (充值订单)\n";
echo "  单位：万元\n\n";

echo "===========================================\n";
echo "说明：\n";
echo "1. 如果你的业务主要是文章/课程销售，主要看文章订单和系列订单\n";
echo "2. 如果用户充值功能使用较多，充值订单也会有数据\n";
echo "3. 访问量基于用户登录会话，用户每次登录都会创建会话\n";
echo "4. 如果数据库是新的，没有订单数据，显示 0 是正常的\n";
echo "===========================================\n";

$conn->close();
