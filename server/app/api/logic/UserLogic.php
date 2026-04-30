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

namespace app\api\logic;


use app\common\{enum\notice\NoticeEnum,
    enum\user\UserTerminalEnum,
    enum\YesNoEnum,
    logic\BaseLogic,
    model\user\User,
    model\user\UserAuth,
    model\user\UserRealname,
    service\FileService,
    service\sms\SmsDriver,
    service\wechat\WeChatMnpService};
use think\facade\Config;
use think\facade\Db;

/**
 * 会员逻辑层
 * Class UserLogic
 * @package app\shopapi\logic
 */
class UserLogic extends BaseLogic
{

    /**
     * @notes 个人中心
     * @param array $userInfo
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2022/9/16 18:04
     */
    public static function center(array $userInfo): array
    {
        $user = User::where(['id' => $userInfo['user_id']])
            ->field('id,sn,sex,account,nickname,real_name,avatar,mobile,create_time,is_new_user,user_money,password')
            ->findOrEmpty();

        if (in_array($userInfo['terminal'], [UserTerminalEnum::WECHAT_MMP, UserTerminalEnum::WECHAT_OA])) {
            $auth = UserAuth::where(['user_id' => $userInfo['user_id'], 'terminal' => $userInfo['terminal']])->find();
            $user['is_auth'] = $auth ? YesNoEnum::YES : YesNoEnum::NO;
        }

        $user['has_password'] = !empty($user['password']);
        
        $merchant = Db::name('merchant')->where('user_id', $userInfo['user_id'])->find();
        $user['is_merchant'] = !empty($merchant) && ($merchant['status'] ?? 0) == 1 ? 1 : 0;
        
        $apply = Db::name('merchant_apply')->where('user_id', $userInfo['user_id'])->order('id', 'desc')->find();
        $user['merchant_apply_status'] = !empty($apply) ? ($apply['status'] ?? -1) : -1;

        $user->hidden(['password']);
        return $user->toArray();
    }


    /**
     * @notes 个人信息
     * @param $userId
     * @return array
     * @author 段誉
     * @date 2022/9/20 19:45
     */
    public static function info(int $userId)
    {
        $user = User::where(['id' => $userId])
            ->field('id,sn,sex,account,password,nickname,real_name,avatar,mobile,create_time,user_money')
            ->findOrEmpty();
        $user['has_password'] = !empty($user['password']);
        $user['has_auth'] = self::hasWechatAuth($userId);
        $user['version'] = config('project.version');
        
        $realname = UserRealname::where('user_id', $userId)->find();
        $user['is_realname'] = !empty($realname) && ($realname['status'] ?? 0) == UserRealname::STATUS_SUCCESS ? 1 : 0;
        
        $merchant = Db::name('merchant')->where('user_id', $userId)->find();
        $user['is_merchant'] = !empty($merchant) && ($merchant['status'] ?? 0) == 1 ? 1 : 0;

        $apply = Db::name('merchant_apply')->where('user_id', $userId)->order('id', 'desc')->find();
        $user['merchant_apply_status'] = !empty($apply) ? ($apply['status'] ?? -1) : -1;

        // 检查是否关注了当前访问的商家（假设从请求参数中获取 merchant_id，或者根据场景判断）
        // 这里只是一个示例字段，实际需要前端在获取商家主页信息时，单独调用关注状态接口，或者在这里补充逻辑
        // 暂时默认未关注，具体逻辑应放在 MerchantLogic 中
        $user['is_follow_merchant'] = 0; 

        $user->hidden(['password']);
        return $user->toArray();
    }


    /**
     * @notes 设置用户信息
     * @param int $userId
     * @param array $params
     * @return User|false
     * @author 段誉
     * @date 2022/9/21 16:53
     */
    public static function setInfo(int $userId, array $params)
    {
        try {
            if ($params['field'] == "avatar") {
                $params['value'] = FileService::setFileUrl($params['value']);
            }

            return User::update([
                    'id' => $userId,
                    $params['field'] => $params['value']]
            );
        } catch (\Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }


    /**
     * @notes 是否有微信授权信息
     * @param $userId
     * @return bool
     * @author 段誉
     * @date 2022/9/20 19:36
     */
    public static function hasWechatAuth(int $userId)
    {
        //是否有微信授权登录
        $terminal = [UserTerminalEnum::WECHAT_MMP, UserTerminalEnum::WECHAT_OA,UserTerminalEnum::PC];
        $auth = UserAuth::where(['user_id' => $userId])
            ->whereIn('terminal', $terminal)
            ->findOrEmpty();
        return !$auth->isEmpty();
    }


    /**
     * @notes 重置登录密码
     * @param $params
     * @return bool
     * @author 段誉
     * @date 2022/9/16 18:06
     */
    public static function resetPassword(array $params)
    {
        try {
            // 校验验证码
            $smsDriver = new SmsDriver();
            if (!$smsDriver->verify($params['mobile'], $params['code'], NoticeEnum::FIND_LOGIN_PASSWORD_CAPTCHA)) {
                throw new \Exception('验证码错误');
            }

            // 重置密码
            $passwordSalt = Config::get('project.unique_identification');
            $password = create_password($params['password'], $passwordSalt);

            // 更新
            User::where('mobile', $params['mobile'])->update([
                'password' => $password
            ]);

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes 修稿密码
     * @param $params
     * @param $userId
     * @return bool
     * @author 段誉
     * @date 2022/9/20 19:13
     */
    public static function changePassword(array $params, int $userId)
    {
        try {
            $user = User::findOrEmpty($userId);
            if ($user->isEmpty()) {
                throw new \Exception('用户不存在');
            }

            // 密码盐
            $passwordSalt = Config::get('project.unique_identification');

            if (!empty($user['password'])) {
                if (empty($params['old_password'])) {
                    throw new \Exception('请填写旧密码');
                }
                $oldPassword = create_password($params['old_password'], $passwordSalt);
                if ($oldPassword != $user['password']) {
                    throw new \Exception('原密码不正确');
                }
            }

            // 保存密码
            $password = create_password($params['password'], $passwordSalt);
            $user->password = $password;
            $user->save();

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes 获取小程序手机号
     * @param array $params
     * @return bool
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @author 段誉
     * @date 2023/2/27 11:49
     */
    public static function getMobileByMnp(array $params)
    {
        try {
            $response = (new WeChatMnpService())->getUserPhoneNumber($params['code']);
            $phoneNumber = $response['phone_info']['purePhoneNumber'] ?? '';
            if (empty($phoneNumber)) {
                throw new \Exception('获取手机号码失败');
            }

            $user = User::where([
                ['mobile', '=', $phoneNumber],
                ['id', '<>', $params['user_id']]
            ])->findOrEmpty();

            if (!$user->isEmpty()) {
                throw new \Exception('手机号已被其他账号绑定');
            }

            // 绑定手机号
            User::update([
                'id' => $params['user_id'],
                'mobile' => $phoneNumber
            ]);

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes 绑定手机号
     * @param $params
     * @return bool
     * @author 段誉
     * @date 2022/9/21 17:28
     */
    public static function bindMobile(array $params)
    {
        try {
            // 变更手机号场景
            $sceneId = NoticeEnum::CHANGE_MOBILE_CAPTCHA;
            $where = [
                ['id', '=', $params['user_id']],
                ['mobile', '=', $params['mobile']]
            ];

            // 绑定手机号场景
            if ($params['type'] == 'bind') {
                $sceneId = NoticeEnum::BIND_MOBILE_CAPTCHA;
                $where = [
                    ['mobile', '=', $params['mobile']]
                ];
            }

            // 校验短信
            $checkSmsCode = (new SmsDriver())->verify($params['mobile'], $params['code'], $sceneId);
            if (!$checkSmsCode) {
                throw new \Exception('验证码错误');
            }

            $user = User::where($where)->findOrEmpty();
            if (!$user->isEmpty()) {
                throw new \Exception('该手机号已被使用');
            }

            User::update([
                'id' => $params['user_id'],
                'mobile' => $params['mobile'],
            ]);

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 绑定邮箱
     * @param array $params
     * @return bool
     * @author 杰哥网络科技 qq2711793818
     * @date 2025/02/22
     */
    public static function bindEmail(array $params)
    {
        try {
            $email = $params['email'] ?? '';
            $code = $params['code'] ?? '';
            $userId = $params['user_id'];

            if (empty($email)) {
                throw new \Exception('请输入邮箱');
            }
            if (empty($code)) {
                throw new \Exception('请输入验证码');
            }

            // 验证邮箱格式
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('邮箱格式错误');
            }

            // 检查邮箱是否已被其他用户使用
            $existUser = User::where('email', $email)->where('id', '<>', $userId)->findOrEmpty();
            if (!$existUser->isEmpty()) {
                throw new \Exception('该邮箱已被其他账号绑定');
            }

            // 验证邮箱验证码
            $log = \app\common\model\notice\EmailLog::where('email', $email)
                ->where('scene_id', \app\common\enum\notice\EmailEnum::USER_BIND_EMAIL)
                ->where('send_status', 1)
                ->where('is_verify', 0)
                ->where('send_time', '>', time() - 300)
                ->order('send_time', 'desc')
                ->findOrEmpty();

            if ($log->isEmpty()) {
                throw new \Exception('验证码错误或已过期');
            }

            if ($log->code !== $code) {
                throw new \Exception('验证码错误');
            }

            // 标记验证码已使用
            $log->is_verify = 1;
            $log->save();

            // 绑定邮箱
            User::update([
                'id' => $userId,
                'email' => $email,
                'email_verify' => 1,
            ]);

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 通过邮箱验证码重置密码
     * @param array $params
     * @return bool
     * @author 杰哥网络科技 qq2711793818
     * @date 2025/03/12
     */
    public static function resetPasswordByEmail(array $params)
    {
        try {
            $email = $params['email'] ?? '';
            $code = $params['code'] ?? '';
            $password = $params['password'] ?? '';
            $passwordConfirm = $params['password_confirm'] ?? '';

            if (empty($email)) {
                throw new \Exception('请输入邮箱');
            }
            if (empty($code)) {
                throw new \Exception('请输入验证码');
            }
            if (empty($password)) {
                throw new \Exception('请输入新密码');
            }
            if ($password !== $passwordConfirm) {
                throw new \Exception('两次输入的密码不一致');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('邮箱格式错误');
            }

            $user = User::where('email', $email)->findOrEmpty();
            if ($user->isEmpty()) {
                throw new \Exception('该邮箱未绑定任何账号');
            }

            $log = \app\common\model\notice\EmailLog::where('email', $email)
                ->where('scene_id', \app\common\enum\notice\EmailEnum::FORGOT_PASSWORD)
                ->where('send_status', 1)
                ->where('is_verify', 0)
                ->where('send_time', '>', time() - 300)
                ->order('send_time', 'desc')
                ->findOrEmpty();

            if ($log->isEmpty()) {
                throw new \Exception('验证码错误或已过期');
            }

            if ($log->code !== $code) {
                throw new \Exception('验证码错误');
            }

            $log->is_verify = 1;
            $log->save();

            $passwordSalt = Config::get('project.unique_identification');
            $newPassword = create_password($password, $passwordSalt);

            User::where('id', $user->id)->update([
                'password' => $newPassword
            ]);

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

}