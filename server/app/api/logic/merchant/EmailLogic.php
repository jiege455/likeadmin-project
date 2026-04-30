<?php

namespace app\api\logic\merchant;

use app\common\logic\BaseLogic;
use think\facade\Db;
use app\common\model\notice\EmailLog;
use app\common\enum\notice\EmailEnum;

class EmailLogic extends BaseLogic
{
    public static function info(int $userId): array
    {
        $merchant = Db::name('merchant')
            ->where('user_id', $userId)
            ->field('email, email_verify, email_notify')
            ->find();

        if (!$merchant) {
            return ['email' => '', 'email_verify' => 0, 'email_notify' => 1];
        }

        return [
            'email' => $merchant['email'] ?? '',
            'email_verify' => (int)($merchant['email_verify'] ?? 0),
            'email_notify' => (int)($merchant['email_notify'] ?? 1),
        ];
    }

    public static function bind(int $userId, array $params): bool|string
    {
        try {
            $email = $params['email'] ?? '';
            $code = $params['code'] ?? '';

            if (empty($email)) {
                return '请输入邮箱地址';
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return '邮箱格式不正确';
            }

            if (empty($code)) {
                return '请输入验证码';
            }

            $log = EmailLog::where('email', $email)
                ->where('scene_id', EmailEnum::MERCHANT_BIND_EMAIL)
                ->where('send_status', 1)
                ->where('is_verify', 0)
                ->where('send_time', '>', time() - 300)
                ->order('send_time', 'desc')
                ->findOrEmpty();

            if ($log->isEmpty()) {
                return '验证码已过期或不存在';
            }

            if ($log->code !== $code) {
                return '验证码错误';
            }

            $log->is_verify = 1;
            $log->save();

            Db::name('merchant')
                ->where('user_id', $userId)
                ->update([
                    'email' => $email,
                    'email_verify' => 1,
                    'update_time' => time()
                ]);

            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public static function updateNotify(int $userId, array $params): bool|string
    {
        try {
            $emailNotify = (int)($params['email_notify'] ?? 1);

            Db::name('merchant')
                ->where('user_id', $userId)
                ->update([
                    'email_notify' => $emailNotify,
                    'update_time' => time()
                ]);

            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
