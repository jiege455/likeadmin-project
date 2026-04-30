<?php
/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * BusinessWorker服务 - 处理业务逻辑
 */
use Workerman\Worker;
use GatewayWorker\BusinessWorker;

$worker = new BusinessWorker();

$worker->name = 'ChatBusinessWorker';

$worker->count = 4;

$worker->registerAddress = '127.0.0.1:1238';

$worker->eventHandler = 'app\websocket\Events';
