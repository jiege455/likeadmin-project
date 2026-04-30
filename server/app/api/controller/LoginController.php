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

namespace app\api\controller;

use app\api\validate\{LoginAccountValidate, RegisterValidate, WebScanLoginValidate, WechatLoginValidate};
use app\api\logic\LoginLogic;
use app\common\service\SocialLoginService;
use app\common\enum\user\UserTerminalEnum;

/**
 * 登录注册
 * Class LoginController
 * @package app\api\controller
 */
class LoginController extends BaseApiController
{

    public array $notNeedLogin = ['register', 'account', 'logout', 'codeUrl', 'oaLogin',  'mnpLogin', 'getScanCode', 'scanLogin', 'getInviterInfo', 'socialLogin', 'socialCallback'];


    /**
     * @notes 注册账号
     * @return \think\response\Json
     * @author 段誉
     * @date 2022/9/7 15:38
     */
    public function register()
    {
        $params = (new RegisterValidate())->post()->goCheck('register');
        $result = LoginLogic::register($params);
        if (false === $result) {
            return $this->fail(LoginLogic::getError());
        }
        return $this->success('注册成功', $result);
    }


    /**
     * @notes 账号密码/手机号密码/手机号验证码登录
     * @return \think\response\Json
     * @author 段誉
     * @date 2022/9/16 10:42
     */
    public function account()
    {
        $params = (new LoginAccountValidate())->post()->goCheck();
        $result = LoginLogic::login($params);
        if (false === $result) {
            return $this->fail(LoginLogic::getError());
        }
        return $this->data($result);
    }


    /**
     * @notes 退出登录
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2022/9/16 10:42
     */
    public function logout()
    {
        LoginLogic::logout($this->userInfo);
        return $this->success();
    }


    /**
     * @notes 获取微信请求code的链接
     * @return \think\response\Json
     * @author 段誉
     * @date 2022/9/15 18:27
     */
    public function codeUrl()
    {
        $url = $this->request->get('url');
        $result = ['url' => LoginLogic::codeUrl($url)];
        return $this->success('获取成功', $result);
    }


    /**
     * @notes 公众号登录
     * @return \think\response\Json
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author 段誉
     * @date 2022/9/20 19:48
     */
    public function oaLogin()
    {
        $params = (new WechatLoginValidate())->post()->goCheck('oa');
        $res = LoginLogic::oaLogin($params);
        if (false === $res) {
            return $this->fail(LoginLogic::getError());
        }
        return $this->success('', $res);
    }


    /**
     * @notes 小程序-登录接口
     * @return \think\response\Json
     * @author 段誉
     * @date 2022/9/20 19:48
     */
    public function mnpLogin()
    {
        $params = (new WechatLoginValidate())->post()->goCheck('mnpLogin');
        $res = LoginLogic::mnpLogin($params);
        if (false === $res) {
            return $this->fail(LoginLogic::getError());
        }
        return $this->success('', $res);
    }


    /**
     * @notes 小程序绑定微信
     * @return \think\response\Json
     * @author 段誉
     * @date 2022/9/20 19:48
     */
    public function mnpAuthBind()
    {
        $params = (new WechatLoginValidate())->post()->goCheck("wechatAuth");
        $params['user_id'] = $this->userId;
        $result = LoginLogic::mnpAuthLogin($params);
        if ($result === false) {
            return $this->fail(LoginLogic::getError());
        }
        return $this->success('绑定成功', [], 1, 1);
    }



    /**
     * @notes 公众号绑定微信
     * @return \think\response\Json
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author 段誉
     * @date 2022/9/20 19:48
     */
    public function oaAuthBind()
    {
        $params = (new WechatLoginValidate())->post()->goCheck("wechatAuth");
        $params['user_id'] = $this->userId;
        $result = LoginLogic::oaAuthLogin($params);
        if ($result === false) {
            return $this->fail(LoginLogic::getError());
        }
        return $this->success('绑定成功', [], 1, 1);
    }


    /**
     * @notes 获取扫码地址
     * @return \think\response\Json
     * @author 段誉
     * @date 2022/10/20 18:25
     */
    public function getScanCode()
    {
        $redirectUri = $this->request->get('url/s');
        $result = LoginLogic::getScanCode($redirectUri);
        if (false === $result) {
            return $this->fail(LoginLogic::getError() ?? '未知错误');
        }
        return $this->success('', $result);
    }


    /**
     * @notes 网站扫码登录
     * @return \think\response\Json
     * @author 段誉
     * @date 2022/10/21 10:28
     */
    public function scanLogin()
    {
        $params = (new WebScanLoginValidate())->post()->goCheck();
        $result = LoginLogic::scanLogin($params);
        if (false === $result) {
            return $this->fail(LoginLogic::getError() ?? '登录失败');
        }
        return $this->success('', $result);
    }


    /**
     * @notes 更新用户头像昵称
     * @return \think\response\Json
     * @author 段誉
     * @date 2023/2/22 11:15
     */
    public function updateUser()
    {
        $params = (new WechatLoginValidate())->post()->goCheck("updateUser");
        LoginLogic::updateUser($params, $this->userId);
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * @notes 根据邀请码获取邀请人信息
     * @return \think\response\Json
     * @author 杰哥网络科技 qq2711793818
     * @date 2025/02/21
     */
    public function getInviterInfo()
    {
        $inviteCode = $this->request->get('invite_code', '');
        if (empty($inviteCode)) {
            return $this->success('', []);
        }
        $result = LoginLogic::getInviterInfo($inviteCode);
        return $this->success('', $result);
    }


    /**
     * @notes 获取聚合登录跳转地址
     * @return \think\response\Json
     * @author 杰哥网络科技 qq2711793818
     */
    public function socialLogin()
    {
        $type = $this->request->get('type', '');
        if (empty($type)) {
            return $this->fail('登录类型不能为空');
        }

        if (!SocialLoginService::isEnabled()) {
            return $this->fail('聚合登录未开启');
        }

        $enabledTypes = SocialLoginService::getEnabledTypes();
        if (!isset($enabledTypes[$type])) {
            return $this->fail('该登录方式未启用');
        }

        $socialLogin = new SocialLoginService($type);
        $url = $socialLogin->getLoginUrl();

        return $this->success('', ['url' => $url]);
    }


    /**
     * @notes 聚合登录回调
     * @return \think\response\Json|\think\response\Redirect
     * @author 杰哥网络科技 qq2711793818
     */
    public function socialCallback()
    {
        $type = $this->request->get('type', '');
        $code = $this->request->get('code', '');

        if (empty($type) || empty($code)) {
            return $this->fail('参数错误');
        }

        if (!SocialLoginService::isEnabled()) {
            return $this->fail('聚合登录未开启');
        }

        try {
            $socialLogin = new SocialLoginService($type);
            $userInfo = $socialLogin->getUserInfoByCode($code);

            if (!$userInfo['success']) {
                return $this->fail($userInfo['msg'] ?? '登录失败');
            }

            $user = $socialLogin->authUserLogin($userInfo, UserTerminalEnum::WECHAT_H5);

            $token = $user['token'] ?? '';
            $userId = $user['id'] ?? 0;
            $nickname = urlencode($user['nickname'] ?? '');
            $avatar = urlencode($user['avatar'] ?? '');

            // 跳转到前端页面并带上登录信息
            $redirectUrl = request()->domain() . '/mobile/#/pages/login/login';
            $redirectUrl .= '?social_login=1&token=' . $token . '&user_id=' . $userId . '&nickname=' . $nickname . '&avatar=' . $avatar;

            return redirect($redirectUrl);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }


}