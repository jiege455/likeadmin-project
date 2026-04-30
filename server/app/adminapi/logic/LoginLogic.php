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

namespace app\adminapi\logic;

use app\common\logic\BaseLogic;
use app\common\model\auth\Admin;
use app\adminapi\service\AdminTokenService;
use app\common\service\FileService;
use think\facade\Config;

/**
 * 登录逻辑
 * Class LoginLogic
 * @package app\adminapi\logic
 */
class LoginLogic extends BaseLogic
{
    /**
     * @notes 管理员账号登录
     * @param $params
     * @return false|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 令狐冲
     * @date 2021/6/30 17:00
     */
    public function login($params)
    {
        $time = time();
        $admin = Admin::where('account', '=', $params['account'])->find();

        // 【安全修复】统一错误提示，防止账号枚举攻击
        $unifiedError = '账号或密码错误，请重新输入';

        if (empty($admin)) {
            self::$error = $unifiedError;
            return false;
        }

        $passwordSalt = Config::get('project.unique_identification');
        $password = create_password($params['password'], $passwordSalt);

        if ($admin->password !== $password) {
            self::$error = $unifiedError;
            return false;
        }

        if ($admin->disable == 1) {
            self::$error = '账号或密码错误，请重新输入';
            return false;
        }

        $admin->login_time = $time;
        $admin->login_ip = request()->ip();
        $admin->save();

        //设置token
        $adminInfo = AdminTokenService::setToken($admin->id, $params['terminal'], $admin->multipoint_login);

        //返回登录信息
        $avatar = $admin->avatar ? $admin->avatar : Config::get('project.default_image.admin_avatar');
        $avatar = FileService::getFileUrl($avatar);
        return [
            'name' => $adminInfo['name'],
            'avatar' => $avatar,
            'role_name' => $adminInfo['role_name'],
            'token' => $adminInfo['token'],
        ];
    }


    /**
     * @notes 退出登录
     * @param $adminInfo
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 令狐冲
     * @date 2021/7/5 14:34
     */
    public function logout($adminInfo)
    {
        //token不存在，不注销
        if (!isset($adminInfo['token'])) {
            return false;
        }
        //设置token过期
        return AdminTokenService::expireToken($adminInfo['token']);
    }
}