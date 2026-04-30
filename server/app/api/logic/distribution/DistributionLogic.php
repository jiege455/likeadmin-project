<?php
namespace app\api\logic\distribution;

use app\common\logic\BaseLogic;
use app\common\logic\EmailNotifyLogic;
use app\common\service\ConfigService;
use app\common\service\sms\SmsDriver;
use app\common\enum\notice\NoticeEnum;
use app\common\enum\notice\EmailEnum;
use app\common\model\notice\EmailLog;
use think\facade\Db;

class DistributionLogic extends BaseLogic
{
    public static function getStatus($userId)
    {
        $user = Db::name('user')->where('id', $userId)->find();
        $apply = Db::name('distribution_apply')
            ->where('user_id', $userId)
            ->order('id', 'desc')
            ->find();

        return [
            'is_distributor' => intval($user['is_distributor'] ?? 0),
            'commission' => floatval($user['commission'] ?? 0),
            'total_commission' => floatval($user['total_commission'] ?? 0),
            'apply_status' => $apply ? intval($apply['status']) : -1,
            'apply_reason' => $apply ? ($apply['audit_remark'] ?? '') : '',
        ];
    }

    public static function apply($userId, $params)
    {
        $user = Db::name('user')->where('id', $userId)->find();
        if (!$user) {
            self::$error = '用户不存在';
            return false;
        }

        if ($user['is_distributor'] == 1) {
            self::$error = '您已经是推广员了';
            return false;
        }

        $existApply = Db::name('distribution_apply')
            ->where('user_id', $userId)
            ->where('status', 0)
            ->find();
        if ($existApply) {
            self::$error = '您已提交申请，请等待审核';
            return false;
        }

        $name = $params['name'] ?? '';
        $mobile = $params['mobile'] ?? '';
        $reason = $params['reason'] ?? '';

        if (empty($name)) {
            self::$error = '请输入真实姓名';
            return false;
        }
        if (empty($mobile)) {
            self::$error = '请输入手机号';
            return false;
        }

        $verifyType = ConfigService::get('system', 'distributor_apply_verify_type', 'email');
        $emailNotifyOpen = ConfigService::get('system', 'email_notify_open', 0);
        $smsNotifyOpen = ConfigService::get('system', 'sms_notify_open', 1);

        if ($verifyType === 'email' && $emailNotifyOpen) {
            $email = $params['email'] ?? '';
            $emailCode = $params['email_code'] ?? '';
            if (empty($email)) {
                self::$error = '请输入邮箱';
                return false;
            }
            if (empty($emailCode)) {
                self::$error = '请输入邮箱验证码';
                return false;
            }
            if (!self::verifyEmailCode($email, $emailCode, EmailEnum::DISTRIBUTOR_APPLY)) {
                self::$error = '邮箱验证码错误或已过期';
                return false;
            }
        }

        if ($verifyType === 'mobile' && $smsNotifyOpen) {
            $code = $params['code'] ?? '';
            if (empty($code)) {
                self::$error = '请输入手机验证码';
                return false;
            }
            $smsDriver = new SmsDriver();
            if (!$smsDriver->verify($mobile, $code, NoticeEnum::DISTRIBUTOR_APPLY_CAPTCHA)) {
                self::$error = '手机验证码错误或已过期';
                return false;
            }
        }

        $applyId = Db::name('distribution_apply')->insertGetId([
            'user_id' => $userId,
            'name' => $name,
            'mobile' => $mobile,
            'reason' => $reason,
            'status' => 0,
            'create_time' => time(),
            'update_time' => time()
        ]);

        EmailNotifyLogic::sendDistributionApplyNotifyToAdmin([
            'id' => $applyId,
            'user_id' => $userId,
            'name' => $name,
            'mobile' => $mobile,
            'reason' => $reason,
            'create_time' => time()
        ]);

        return true;
    }

    private static function verifyEmailCode(string $email, string $code, int $sceneId): bool
    {
        $log = EmailLog::where('email', $email)
            ->where('scene_id', $sceneId)
            ->where('send_status', 1)
            ->where('is_verify', 0)
            ->where('send_time', '>', time() - 300)
            ->order('send_time', 'desc')
            ->findOrEmpty();

        if ($log->isEmpty()) {
            return false;
        }

        if ($log->code !== $code) {
            return false;
        }

        $log->is_verify = 1;
        $log->save();

        return true;
    }

    public static function getInfo($userId)
    {
        $user = Db::name('user')->where('id', $userId)->find();
        
        $todayStart = strtotime(date('Y-m-d'));
        $todayCommission = Db::name('distribution_log')
            ->where('user_id', $userId)
            ->where('create_time', '>=', $todayStart)
            ->sum('amount');

        $monthStart = strtotime(date('Y-m-01'));
        $monthCommission = Db::name('distribution_log')
            ->where('user_id', $userId)
            ->where('create_time', '>=', $monthStart)
            ->sum('amount');

        $orderCount = Db::name('distribution_log')
            ->where('user_id', $userId)
            ->count();

        return [
            'is_distributor' => intval($user['is_distributor'] ?? 0),
            'commission' => floatval($user['commission'] ?? 0),
            'total_commission' => floatval($user['total_commission'] ?? 0),
            'today_commission' => floatval($todayCommission),
            'month_commission' => floatval($monthCommission),
            'order_count' => $orderCount,
        ];
    }

    public static function orders($userId, $params)
    {
        $where = [];
        $where[] = ['d.user_id', '=', $userId];

        $count = Db::name('distribution_log')->alias('d')->where($where)->count();
        $lists = Db::name('distribution_log')
            ->alias('d')
            ->leftJoin('user u', 'd.source_user_id = u.id')
            ->leftJoin('article_order o', 'd.order_id = o.id')
            ->leftJoin('article a', 'o.article_id = a.id')
            ->field('d.*, u.nickname, u.avatar, a.title as article_title, o.order_amount')
            ->where($where)
            ->order('d.id', 'desc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 20)
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['avatar'] = $item['avatar'] ? \app\common\service\FileService::getFileUrl($item['avatar']) : '';
            $item['create_time'] = date('Y-m-d H:i', $item['create_time']);
        }

        return ['count' => $count, 'lists' => $lists];
    }

    public static function withdraw($userId, $params)
    {
        $user = Db::name('user')->where('id', $userId)->find();
        if (!$user || $user['is_distributor'] != 1) {
            self::$error = '您还不是推广员';
            return false;
        }

        $money = floatval($params['money'] ?? 0);
        if ($money <= 0) {
            self::$error = '提现金额必须大于0';
            return false;
        }

        $config = self::getWithdrawConfig();
        if ($money < $config['min_withdraw']) {
            self::$error = '最低提现金额为' . $config['min_withdraw'] . '元';
            return false;
        }

        if ($user['commission'] < $money) {
            self::$error = '佣金余额不足';
            return false;
        }

        $accountId = intval($params['account_id'] ?? 0);
        $account = Db::name('withdraw_account')
            ->where('id', $accountId)
            ->where('user_id', $userId)
            ->where('delete_time', null)
            ->find();

        if (!$account) {
            self::$error = '请选择收款账户';
            return false;
        }

        $verifyType = ConfigService::get('system', 'withdraw_verify_type', 'email');
        $emailNotifyOpen = ConfigService::get('system', 'email_notify_open', 0);
        $smsNotifyOpen = ConfigService::get('system', 'sms_notify_open', 1);

        if ($verifyType === 'email' && $emailNotifyOpen) {
            $email = $params['email'] ?? $user['email'] ?? '';
            $emailCode = $params['email_code'] ?? '';
            if (empty($email)) {
                self::$error = '请输入邮箱';
                return false;
            }
            if (empty($emailCode)) {
                self::$error = '请输入邮箱验证码';
                return false;
            }
            if (!self::verifyEmailCode($email, $emailCode, EmailEnum::WITHDRAW)) {
                self::$error = '邮箱验证码错误或已过期';
                return false;
            }
        }

        if ($verifyType === 'mobile' && $smsNotifyOpen) {
            $mobile = $user['mobile'] ?? '';
            $code = $params['code'] ?? '';
            if (empty($mobile)) {
                self::$error = '请先绑定手机号';
                return false;
            }
            if (empty($code)) {
                self::$error = '请输入手机验证码';
                return false;
            }
            $smsDriver = new SmsDriver();
            if (!$smsDriver->verify($mobile, $code, NoticeEnum::WITHDRAW_CAPTCHA)) {
                self::$error = '手机验证码错误或已过期';
                return false;
            }
        }

        Db::startTrans();
        try {
            $user['commission'] = bcsub($user['commission'], $money, 2);
            Db::name('user')->where('id', $userId)->update(['commission' => $user['commission']]);

            $insertData = [
                'user_id' => $userId,
                'merchant_id' => 0,
                'source' => 2,
                'money' => $money,
                'left_money' => $user['commission'],
                'type' => $account['type'],
                'account_info' => json_encode([
                    'account' => $account['account'],
                    'real_name' => $account['real_name'],
                    'bank_name' => $account['bank_name'] ?? '',
                    'bank_branch' => $account['bank_branch'] ?? '',
                    'qrcode' => $account['qrcode'] ?? ''
                ], JSON_UNESCAPED_UNICODE),
                'bank_name' => $account['bank_name'] ?? '',
                'bank_account' => $account['account'],
                'bank_user' => $account['real_name'],
                'status' => 0,
                'create_time' => time(),
                'update_time' => time()
            ];

            Db::name('withdraw_apply')->insert($insertData);

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }

    public static function withdrawConfig()
    {
        return self::getWithdrawConfig();
    }

    private static function getWithdrawConfig()
    {
        $config = Db::name('config')
            ->where('type', 'withdraw')
            ->column('value', 'name');

        return [
            'min_withdraw' => floatval($config['distributor_min_withdraw'] ?? 10),
            'withdraw_fee' => floatval($config['distributor_withdraw_fee'] ?? 0),
            'arrival_days' => intval($config['withdraw_arrival_days'] ?? 3),
        ];
    }

    public static function withdrawList($userId, $params)
    {
        $where = [];
        $where[] = ['user_id', '=', $userId];
        $where[] = ['source', '=', 2];
        $where[] = ['delete_time', '=', null];

        $count = Db::name('withdraw_apply')->where($where)->count();
        $lists = Db::name('withdraw_apply')
            ->where($where)
            ->order('id', 'desc')
            ->page($params['page'] ?? 1, $params['limit'] ?? 20)
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['status_text'] = self::getStatusText($item['status']);
            $item['create_time'] = date('Y-m-d H:i', $item['create_time']);
        }

        return ['count' => $count, 'lists' => $lists];
    }

    private static function getStatusText($status)
    {
        $texts = [
            0 => '待审核',
            1 => '已拒绝',
            2 => '已通过',
            3 => '已打款'
        ];
        return $texts[$status] ?? '未知';
    }
}
