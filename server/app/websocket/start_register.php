<?php
/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * Register服务 - 服务注册中心
 */
use Workerman\Worker;
use GatewayWorker\Register;

$register = new Register('text://0.0.0.0:1238');

$register->name = 'ChatRegister';
