<?php
namespace app\api\logic\merchant;

use app\common\logic\BaseLogic;
use app\common\model\finance\WithdrawApply;
use app\common\model\merchant\Merchant;
use think\facade\Db;

class WithdrawLogic extends BaseLogic
{
    public static function apply($userId, $params)
    {
        $money = $params['money'] ?? 0;
        $accountId = $params['account_id'] ?? 0;

        if ($money <= 0) {
            self::$error = '提现金额必须大于0';
            return false;
        }

        $merchant = Merchant::where('user_id', $userId)->find();
        if (!$merchant) {
            self::$error = '非商家账号';
            return false;
        }

        $minWithdraw = self::getMinWithdraw();
        if ($money < $minWithdraw) {
            self::$error = '最低提现金额为' . $minWithdraw . '元';
            return false;
        }

        $account = Db::name('withdraw_account')
            ->where('id', $accountId)
            ->where('merchant_id', $merchant->id)
            ->where('delete_time', null)
            ->find();

        if (!$account) {
            self::$error = '请选择收款账户';
            return false;
        }

        Db::startTrans();
        try {
            $merchant = Merchant::where('id', $merchant->id)->lock(true)->find();
            if (!$merchant) {
                Db::rollback();
                self::$error = '商家不存在';
                return false;
            }

            if (bccomp(strval($merchant->money), strval($money), 2) < 0) {
                Db::rollback();
                self::$error = '余额不足';
                return false;
            }

            $merchant->money = bcsub(strval($merchant->money), strval($money), 2);
            $merchant->save();

            $insertData = [
                'merchant_id' => $merchant->id,
                'user_id' => 0,
                'source' => 1,
                'money' => $money,
                'left_money' => $merchant->money,
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
                'status' => WithdrawApply::STATUS_WAIT,
                'create_time' => time(),
                'update_time' => time(),
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

    public static function lists($userId, $params)
    {
        $merchant = Merchant::where('user_id', $userId)->find();
        if (!$merchant) {
            return ['lists' => [], 'count' => 0, 'page_no' => 1, 'page_size' => 10];
        }

        $where = [['merchant_id', '=', $merchant->id]];
        
        $count = WithdrawApply::where($where)->count();
        $lists = WithdrawApply::where($where)
            ->page($params['page_no'] ?? 1, $params['page_size'] ?? 10)
            ->order('id', 'desc')
            ->select()
            ->append(['status_desc', 'type_desc'])
            ->toArray();

        return [
            'lists' => $lists,
            'count' => $count,
            'page_no' => $params['page_no'] ?? 1,
            'page_size' => $params['page_size'] ?? 10,
        ];
    }

    public static function detail($userId, $id)
    {
        $merchant = Merchant::where('user_id', $userId)->find();
        if (!$merchant) return null;

        return WithdrawApply::where('id', $id)
            ->where('merchant_id', $merchant->id)
            ->append(['status_desc', 'type_desc'])
            ->find();
    }

    public static function config()
    {
        return [
            'min_money' => self::getMinWithdraw(),
            'fee_rate' => self::getWithdrawFee(),
            'type' => [
                ['id' => WithdrawApply::TYPE_WECHAT, 'name' => '微信零钱'],
                ['id' => WithdrawApply::TYPE_ALIPAY, 'name' => '支付宝'],
                ['id' => WithdrawApply::TYPE_BANK, 'name' => '银行卡'],
            ]
        ];
    }

    public static function info($userId)
    {
        $merchant = Merchant::where('user_id', $userId)->find();
        if (!$merchant) {
            return null;
        }

        return [
            'merchant' => $merchant->toArray(),
            'merchant_id' => $merchant->id,
            'min_withdraw' => self::getMinWithdraw(),
            'withdraw_fee' => self::getWithdrawFee(),
            'arrival_days' => self::getArrivalDays(),
        ];
    }

    private static function getMinWithdraw()
    {
        $config = Db::name('config')
            ->where('type', 'withdraw')
            ->where('name', 'merchant_min_withdraw')
            ->value('value');
        return floatval($config ?: 100);
    }

    private static function getWithdrawFee()
    {
        $config = Db::name('config')
            ->where('type', 'withdraw')
            ->where('name', 'merchant_withdraw_fee')
            ->value('value');
        return floatval($config ?: 0);
    }

    private static function getArrivalDays()
    {
        $config = Db::name('config')
            ->where('type', 'withdraw')
            ->where('name', 'withdraw_arrival_days')
            ->value('value');
        return intval($config ?: 3);
    }
}
