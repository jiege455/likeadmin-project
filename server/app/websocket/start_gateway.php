<?php
/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * Gateway服务 - 处理客户端连接
 */
use Workerman\Worker;
use GatewayWorker\Gateway;

$gateway = new Gateway("websocket://0.0.0.0:8282");

$gateway->name = 'ChatGateway';

$gateway->count = 4;

$gateway->lanIp = '127.0.0.1';

$gateway->startPort = 2900;

$gateway->registerAddress = '127.0.0.1:1238';

$gateway->pingInterval = 55;

$gateway->pingNotResponseLimit = 1;

$gateway->pingData = '{"type":"ping"}';

Worker::$daemonize = false;
