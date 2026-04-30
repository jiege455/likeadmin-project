<?php
// 加载 ThinkPHP 引导文件
require __DIR__ . '/server/vendor/autoload.php';

// 初始化应用
$http = (new \think\App())->http;

// 执行清理逻辑
try {
    $count = \think\facade\Db::name('merchant_apply')
        ->where('status', 1) // 1 = 已通过
        ->update(['delete_time' => time()]); // 软删除

    echo "清理成功！共删除了 {$count} 条“已通过”的旧申请记录。\n";
    echo "现在您可以重新提交申请了。";
} catch (\Exception $e) {
    echo "清理失败：" . $e->getMessage();
}
