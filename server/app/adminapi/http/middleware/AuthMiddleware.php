<?php
// +----------------------------------------------------------------------
// | likeadmin快速开发前后端分离管理后台（PHP版）
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | gitee下载：https://gitee.com/likeshop_gitee/likeadmin
// | github下载：https://github.com/likeshop-github/likeadmin
// | 访问官网：https://www.likeadmin.cn
// | likeadmin团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeadminTeam
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace app\adminapi\http\middleware;

use app\common\{
    cache\AdminAuthCache,
    service\JsonService
};
use think\facade\Config;
use think\helper\Str;

/**
 * 权限验证中间件
 * Class AuthMiddleware
 * @package app\adminapi\http\middleware
 */
class AuthMiddleware
{
    /**
     * @notes 权限验证
     * @param $request
     * @param \Closure $next
     * @return mixed
     * @author 令狐冲
     * @date 2021/7/2 19:29
     */
    public function handle($request, \Closure $next)
    {
        //不登录访问，无需权限验证
        if ($request->controllerObject->isNotNeedLogin()) {
            return $next($request);
        }

        // 只有在开启登录限制时才校验IP
        if (Config::get('project.admin_login.login_restrictions', 1)) {
            if ($request->adminInfo['login_ip'] != request()->ip()) {
                return JsonService::fail('ip地址发生变化，请重新登录', [], -1);
            }
        }

        //系统默认超级管理员，无需权限验证
        if (1 === $request->adminInfo['root']) {
            return $next($request);
        }

        $adminAuthCache = new AdminAuthCache($request->adminInfo['admin_id']);

        // 当前访问路径
        $accessUri = strtolower($request->controller() . '/' . $request->action());
        // 全部路由
        $allUri = $this->formatUrl($adminAuthCache->getAllUri());

        // 判断该当前访问的uri是否存在
        // 【安全修复】未配置的接口默认拒绝访问，而不是放行
        // 只有超级管理员才能访问未配置的接口
        if (!in_array($accessUri, $allUri)) {
            // 记录未配置的接口访问日志
            \think\facade\Log::write("未配置权限的接口被访问: {$accessUri}, 管理员ID: " . $request->adminInfo['admin_id'], 'warning');
            return JsonService::fail('接口未配置权限，请联系管理员');
        }

        // 当前管理员拥有的路由权限
        $AdminUris = $adminAuthCache->getAdminUri() ?? [];
        $AdminUris = $this->formatUrl($AdminUris);

        if (in_array($accessUri, $AdminUris)) {
            return $next($request);
        }
        return JsonService::fail('权限不足，无法访问或操作');
    }


    /**
     * @notes 格式化URL
     * @param array $data
     * @return array|string[]
     * @author 段誉
     * @date 2022/7/7 15:39
     */
    public function formatUrl(array $data)
    {
        return array_map(function ($item) {
            return strtolower(Str::camel($item));
        }, $data);
    }

}