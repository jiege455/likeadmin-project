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
// 开发者：杰哥网络科技 qq2711793818 杰哥

namespace app\common\service;

use app\common\enum\YesNoEnum;
use app\common\model\user\User;
use app\common\model\user\UserAuth;
use app\common\enum\user\UserTerminalEnum;
use think\Exception;
use think\facade\Log;

class SocialLoginService
{
    protected string $appid = '';
    protected string $appkey = '';
    protected string $type = '';
    protected string $apiBase = 'https://u.xiaobaixuan.com/connect.php';

    public function __construct(string $type = '')
    {
        $this->type = $type;
        $this->appid = ConfigService::get('social_login', 'appid', '');
        $this->appkey = ConfigService::get('social_login', 'appkey', '');
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getLoginUrl(): string
    {
        $redirectUri = request()->domain() . '/api/login/socialCallback';
        $url = $this->apiBase . '?act=login&appid=' . $this->appid . '&appkey=' . $this->appkey . '&type=' . $this->type . '&redirect_uri=' . urlencode($redirectUri);
        return $url;
    }

    public function getQrcode(): array
    {
        $result = $this->sendRequest([
            'act' => 'login',
            'appid' => $this->appid,
            'appkey' => $this->appkey,
            'type' => $this->type,
            'redirect_uri' => request()->domain() . '/api/login/socialCallback'
        ]);

        if (isset($result['code']) && $result['code'] == 0) {
            return ['url' => $result['url'] ?? '', 'qrcode' => $result['qrcode'] ?? ''];
        }
        return ['url' => '', 'qrcode' => ''];
    }

    public function getUserInfoByCode(string $code): array
    {
        $result = $this->sendRequest([
            'act' => 'callback',
            'appid' => $this->appid,
            'appkey' => $this->appkey,
            'type' => $this->type,
            'code' => $code
        ]);

        if (isset($result['code']) && $result['code'] == 0) {
            return [
                'success' => true,
                'social_uid' => $result['social_uid'] ?? '',
                'access_token' => $result['access_token'] ?? '',
                'nickname' => $result['nickname'] ?? '',
                'avatar' => $result['faceimg'] ?? '',
                'gender' => $result['gender'] ?? '',
                'location' => $result['location'] ?? '',
                'ip' => $result['ip'] ?? '',
            ];
        }

        return [
            'success' => false,
            'msg' => $result['msg'] ?? '获取用户信息失败'
        ];
    }

    public function queryUserInfo(string $socialUid): array
    {
        $result = $this->sendRequest([
            'act' => 'query',
            'appid' => $this->appid,
            'appkey' => $this->appkey,
            'type' => $this->type,
            'social_uid' => $socialUid
        ]);

        if (isset($result['code']) && $result['code'] == 0) {
            return [
                'success' => true,
                'social_uid' => $result['social_uid'] ?? '',
                'access_token' => $result['access_token'] ?? '',
                'nickname' => $result['nickname'] ?? '',
                'avatar' => $result['faceimg'] ?? '',
                'gender' => $result['gender'] ?? '',
                'location' => $result['location'] ?? '',
                'ip' => $result['ip'] ?? '',
            ];
        }

        return [
            'success' => false,
            'msg' => $result['msg'] ?? '查询用户信息失败'
        ];
    }

    protected function sendRequest(array $params): array
    {
        try {
            $url = $this->apiBase . '?' . http_build_query($params);
            $response = file_get_contents($url);
            if (empty($response)) {
                return ['code' => -1, 'msg' => '请求失败'];
            }
            $result = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return ['code' => -1, 'msg' => 'JSON解析失败'];
            }
            return $result;
        } catch (\Exception $e) {
            Log::error('聚合登录请求失败:' . $e->getMessage());
            return ['code' => -1, 'msg' => $e->getMessage()];
        }
    }

    public function authUserLogin(array $userInfo, int $terminal = UserTerminalEnum::WECHAT_H5): array
    {
        $socialUid = $userInfo['social_uid'] ?? '';
        $accessToken = $userInfo['access_token'] ?? '';

        if (empty($socialUid)) {
            throw new Exception('用户信息错误，缺少唯一标识');
        }

        $userAuth = UserAuth::where(['openid' => $socialUid, 'terminal' => $terminal])
            ->findOrEmpty();

        if ($userAuth->isEmpty()) {
            $userAuth = UserAuth::where(['unionid' => $socialUid])
                ->findOrEmpty();
        }

        if (!$userAuth->isEmpty()) {
            $user = User::findOrEmpty($userAuth->user_id);
            if (!$user->isEmpty()) {
                if ($user->is_disable) {
                    throw new Exception('您的账号异常，请联系客服。');
                }
                $user->nickname = $userInfo['nickname'] ?? $user->nickname;
                if (!empty($userInfo['avatar']) && empty($user->avatar)) {
                    $user->avatar = $this->saveAvatar($userInfo['avatar'], $socialUid);
                }
                $user->save();

                $tokenData = UserTokenService::setToken($user->id, $terminal);
                $userData = $user->toArray();
                $userData['token'] = $tokenData['token'] ?? '';
                return $userData;
            }
        }

        $userSn = User::createUserSn();
        $nickname = $userInfo['nickname'] ?? "用户" . $userSn;
        $avatar = '';
        if (!empty($userInfo['avatar'])) {
            $avatar = $this->saveAvatar($userInfo['avatar'], $socialUid);
        }
        if (empty($avatar)) {
            $defaultAvatar = config('project.default_image.user_avatar');
            $avatar = ConfigService::get('default_image', 'user_avatar', $defaultAvatar);
        }

        $user = User::create([
            'sn' => $userSn,
            'account' => 'u' . $userSn,
            'nickname' => $nickname,
            'avatar' => $avatar,
            'channel' => $terminal,
            'is_new_user' => YesNoEnum::YES,
        ]);

        UserAuth::create([
            'user_id' => $user->id,
            'openid' => $socialUid,
            'unionid' => $accessToken,
            'terminal' => $terminal,
        ]);

        $tokenData = UserTokenService::setToken($user->id, $terminal);
        $userData = $user->toArray();
        $userData['token'] = $tokenData['token'] ?? '';
        return $userData;
    }

    protected function saveAvatar(string $avatarUrl, string $openid): string
    {
        $config = [
            'default' => ConfigService::get('storage', 'default', 'local'),
            'engine' => ConfigService::get('storage')
        ];

        $fileName = md5($openid . time()) . '.jpeg';

        if ($config['default'] == 'local') {
            return download_file($avatarUrl, 'uploads/user/avatar/', $fileName);
        } else {
            $avatar = 'uploads/user/avatar/' . $fileName;
            $StorageDriver = new \app\common\service\storage\Driver($config);
            if ($StorageDriver->fetch($avatarUrl, $avatar)) {
                return $avatar;
            }
            return '';
        }
    }

    public static function isEnabled(): bool
    {
        $thirdAuth = ConfigService::get('login', 'third_auth', 0);
        $socialLogin = ConfigService::get('login', 'social_login', 0);
        return $thirdAuth == 1 && $socialLogin == 1;
    }

    public static function getEnabledTypes(): array
    {
        $types = [];
        $typeNames = [
            'qq' => 'QQ',
            'wx' => '微信',
            'alipay' => '支付宝',
            'baidu' => '百度',
            'microsoft' => '微软'
        ];

        foreach ($typeNames as $type => $name) {
            $enabled = ConfigService::get('social_login', $type . '_enable', 0);
            if ($enabled == 1) {
                $types[$type] = $name;
            }
        }

        return $types;
    }
}