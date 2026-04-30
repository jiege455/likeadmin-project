<?php
/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * GatewayWorker WebSocket服务启动文件
 * 
 * 使用方法：
 * Linux: php start.php start -d
 * Windows: php start.php start (仅支持调试模式)
 */
use Workerman\Worker;

if(strpos(strtolower(PHP_OS), 'win') === 0) {
    echo "========================================\n";
    echo "Windows系统检测到\n";
    echo "注意: Windows下不支持后台运行模式\n";
    echo "请保持此窗口打开以维持服务运行\n";
    echo "========================================\n\n";
}

if(!extension_loaded('pcntl')) {
    echo "警告: pcntl扩展未安装，部分功能可能受限\n";
}

if(!extension_loaded('posix')) {
    echo "警告: posix扩展未安装，部分功能可能受限\n";
}

define('GLOBAL_START', 1);

define('ROOT_PATH', __DIR__ . '/../../');
require_once ROOT_PATH . 'vendor/autoload.php';

require_once ROOT_PATH . 'app/common.php';

foreach(glob(__DIR__ . '/start_*.php') as $start_file) {
    require_once $start_file;
}

Worker::runAll();
