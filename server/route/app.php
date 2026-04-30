<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// | 开发者：杰哥网络科技
// | QQ: 2711793818 杰哥
// +----------------------------------------------------------------------
use think\facade\Console;
use think\facade\Route;

// 管理后台
Route::rule('admin/:any', function () {
    return view(app()->getRootPath() . 'public/admin/index.html');
})->pattern(['any' => '\w+']);

// 手机端
Route::rule('mobile$', function () {
    return view(app()->getRootPath() . 'public/mobile/index.html');
});
Route::rule('mobile/:path', function () {
    return view(app()->getRootPath() . 'public/mobile/index.html');
})->pattern(['path' => '.*']);

// PC端 - 页面路由配置
// 开发者：杰哥网络科技 QQ:2711793818
// 注意：API请求走ThinkPHP默认路由 /pc/控制器/方法
// 页面路由只处理前端页面刷新时的请求

// PC首页
Route::rule('pc$', function () {
    return view(app()->getRootPath() . 'public/pc/index.html');
});

// PC端页面路由 - 排除API路径（config/index/infoCenter/articleDetail是PcController的API方法）
// 匹配规则：/pc/xxx 且 xxx 不是API方法名
Route::rule('pc/:page', function () {
    return view(app()->getRootPath() . 'public/pc/index.html');
})->pattern(['page' => '^(?!(config|index|infoCenter|articleDetail)$)[\w\-]+$']);

// PC端嵌套路由（支持多级路径如 /pc/user/info）
Route::rule('pc/:path', function () {
    return view(app()->getRootPath() . 'public/pc/index.html');
})->pattern(['path' => '^(?!(config|index|infoCenter|articleDetail))[\w\-/]+$']);

//定时任务
Route::rule('crontab', function () {
    Console::call('crontab');
});





