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
namespace app\adminapi\logic\user;

use app\common\cache\UserTokenCache;
use app\common\enum\user\AccountLogEnum;
use app\common\enum\user\UserTerminalEnum;
use app\common\logic\AccountLogLogic;
use app\common\logic\BaseLogic;
use app\common\model\user\User;
use app\common\model\user\UserSession;
use app\common\service\ConfigService;
use think\facade\Config;
use think\facade\Db;

/**
 * 用户逻辑层
 * Class UserLogic
 * @package app\adminapi\logic\user
 */
class UserLogic extends BaseLogic
{

    /**
     * @notes 添加用户
     * @param array $params
     * @return bool
     * @author 杰哥
     * @date 2024/02/02
     */
    public static function add(array $params)
    {
        Db::startTrans();
        try {
            $userSn = User::createUserSn();
            
            // 修正密码盐获取逻辑，避免配置为空导致报错
            $passwordSalt = Config::get('project.unique_identification');
            if (empty($passwordSalt)) {
                $passwordSalt = 'likeadmin';
            }
            
            // 确保 create_password 函数存在
            if (!function_exists('create_password')) {
                // 如果函数不存在，使用简单md5作为兜底（仅测试用，实际上common.php肯定加载了）
                $password = md5($params['password'] . $passwordSalt);
            } else {
                $password = create_password($params['password'], $passwordSalt);
            }

            $avatar = ConfigService::get('default_image', 'user_avatar');

            // 检查账号是否存在
            $exist = User::where('account', $params['account'])->find();
            if ($exist) {
                // throw new \Exception('账号已存在');
                self::setError('账号已存在');
                return false;
            }

            $user = User::create([
                'sn' => $userSn,
                'avatar' => $avatar,
                'nickname' => $params['nickname'] ?: '用户' . $userSn,
                'account' => $params['account'],
                'password' => $password,
                'channel' => UserTerminalEnum::PC,
                'mobile' => $params['mobile'] ?? '',
                'real_name' => $params['real_name'] ?? '',
                'create_time' => time(),
                'inviter_id' => 0, 
                'login_ip' => request()->ip(),
                'login_time' => time(),
                'is_new_user' => 1,
                'user_money' => 0,
            ]);

            Db::commit();
            return true;
        } catch (\Throwable $e) {
            Db::rollback();
            self::setError($e->getMessage() . ' File:' . $e->getFile() . ' Line:' . $e->getLine());
            return false;
        }
    }

    /**
     * @notes 用户详情
     * @param int $userId
     * @return array
     * @author 段誉
     * @date 2022/9/22 16:32
     */
    public static function detail(int $userId): array
    {
        $field = [
            'id', 'sn', 'account', 'nickname', 'avatar', 'real_name',
            'sex', 'mobile', 'create_time', 'login_time', 'channel',
            'user_money', 'inviter_id',
        ];

        $user = User::where(['id' => $userId])->field($field)
            ->findOrEmpty();

        $user['channel'] = UserTerminalEnum::getTermInalDesc($user['channel']);
        $user->sex = $user->getData('sex');

        $userArr = $user->toArray();

        $userArr['is_merchant'] = 0;
        $userArr['merchant_id'] = 0;
        $merchant = Db::name('merchant')
            ->where('user_id', $userId)
            ->where('status', 1)
            ->field('id, name')
            ->find();
        if ($merchant) {
            $userArr['is_merchant'] = 1;
            $userArr['merchant_id'] = $merchant['id'];
            $userArr['merchant_name'] = $merchant['name'];
        }

        $userArr['inviter_info'] = null;
        if ($userArr['inviter_id'] > 0) {
            $inviter = User::where('id', $userArr['inviter_id'])
                ->field('id, sn, nickname, avatar, mobile')
                ->find();
            if ($inviter) {
                $userArr['inviter_info'] = [
                    'id' => $inviter['id'],
                    'sn' => $inviter['sn'],
                    'nickname' => $inviter['nickname'],
                    'avatar' => $inviter['avatar'],
                    'mobile' => $inviter['mobile'] ?? '',
                ];
            }
        }

        return $userArr;
    }


    /**
     * @notes 更新用户信息
     * @param array $params
     * @return User
     * @author 段誉
     * @date 2022/9/22 16:38
     */
    public static function setUserInfo(array $params)
    {
        return User::update([
            'id' => $params['id'],
            $params['field'] => $params['value']
        ]);
    }


    /**
     * @notes 调整用户余额
     * @param array $params
     * @return bool|string
     * @author 段誉
     * @date 2023/2/23 14:25
     */
    public static function adjustUserMoney(array $params)
    {
        Db::startTrans();
        try {
            $user = User::findOrEmpty($params['user_id']);
            if ($user->isEmpty()) {
                Db::rollback();
                self::setError('用户不存在');
                return false;
            }
            
            if (AccountLogEnum::INC == $params['action']) {
                //调整可用余额
                $user->user_money = bcadd(strval($user->user_money), strval($params['num']), 2);
                $user->save();
                //记录日志
                AccountLogLogic::add(
                    $user->id,
                    AccountLogEnum::UM_INC_ADMIN,
                    AccountLogEnum::INC,
                    $params['num'],
                    '',
                    $params['remark'] ?? ''
                );
            } else {
                // 检查余额是否足够
                if (bccomp(strval($user->user_money), strval($params['num']), 2) < 0) {
                    Db::rollback();
                    self::setError('用户余额不足');
                    return false;
                }
                $user->user_money = bcsub(strval($user->user_money), strval($params['num']), 2);
                $user->save();
                //记录日志
                AccountLogLogic::add(
                    $user->id,
                    AccountLogEnum::UM_DEC_ADMIN,
                    AccountLogEnum::DEC,
                    $params['num'],
                    '',
                    $params['remark'] ?? ''
                );
            }

            Db::commit();
            return true;

        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 删除用户
     * @param int $userId
     * @author 杰哥
     * @date 2024/02/02
     */
    public static function delete(int $userId)
    {
        // 开发者公众号：杰哥网络科技 qq2711793818 杰哥
        // 先清除用户的所有session和token缓存
        $userSessions = UserSession::where('user_id', $userId)->select();
        $userTokenCache = new UserTokenCache();
        foreach ($userSessions as $session) {
            // 删除token缓存
            $userTokenCache->deleteUserInfo($session->token);
        }
        // 删除session记录
        UserSession::where('user_id', $userId)->delete();
        
        // 软删除用户
        User::destroy($userId);
    }

}