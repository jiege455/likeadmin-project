<?php
namespace app\api\logic\withdraw;

use app\common\logic\BaseLogic;
use app\adminapi\logic\setting\WithdrawSettingLogic;
use think\facade\Db;

/**
 * 提现账户逻辑
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
class AccountLogic extends BaseLogic
{
    public static function lists($userId, $params)
    {
        $type = $params['type'] ?? '';
        $merchantId = intval($params['merchant_id'] ?? 0);

        $where = [];
        $where[] = ['delete_time', '=', null];
        
        if ($merchantId > 0) {
            $where[] = ['merchant_id', '=', $merchantId];
        } else {
            $where[] = ['user_id', '=', $userId];
        }
        
        if ($type !== '') {
            $where[] = ['type', '=', $type];
        }

        $lists = Db::name('withdraw_account')
            ->where($where)
            ->order('is_default desc, id desc')
            ->select()
            ->toArray();

        $typeTexts = [
            1 => '微信',
            2 => '支付宝',
            3 => '银行卡'
        ];

        foreach ($lists as &$item) {
            $item['type_text'] = $typeTexts[$item['type']] ?? '未知';
            $item['account_mask'] = self::maskAccount($item['account'], $item['type']);
            $item['qrcode'] = $item['qrcode'] ?? '';
        }

        return ['lists' => $lists];
    }

    public static function add($userId, $params)
    {
        $type = intval($params['type'] ?? 2);
        $account = $params['account'] ?? '';
        $realName = $params['real_name'] ?? '';
        $bankName = $params['bank_name'] ?? '';
        $bankBranch = $params['bank_branch'] ?? '';
        $qrcode = $params['qrcode'] ?? '';
        $merchantId = intval($params['merchant_id'] ?? 0);

        if (empty($account)) {
            self::$error = '请输入账号';
            return false;
        }
        if (empty($realName)) {
            self::$error = '请输入真实姓名';
            return false;
        }
        if ($type == 3 && empty($bankName)) {
            self::$error = '请输入银行名称';
            return false;
        }

        $where = [];
        $where[] = ['delete_time', '=', null];
        if ($merchantId > 0) {
            $where[] = ['merchant_id', '=', $merchantId];
        } else {
            $where[] = ['user_id', '=', $userId];
        }
        $count = Db::name('withdraw_account')->where($where)->count();

        Db::startTrans();
        try {
            $insertData = [
                'user_id' => $merchantId > 0 ? 0 : $userId,
                'merchant_id' => $merchantId,
                'type' => $type,
                'account' => $account,
                'real_name' => $realName,
                'bank_name' => $bankName,
                'bank_branch' => $bankBranch,
                'qrcode' => $qrcode,
                'is_default' => $count > 0 ? 0 : 1,
                'status' => 1,
                'create_time' => time(),
                'update_time' => time()
            ];

            Db::name('withdraw_account')->insert($insertData);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }

    public static function edit($userId, $params)
    {
        $id = intval($params['id'] ?? 0);
        $type = intval($params['type'] ?? 2);
        $account = $params['account'] ?? '';
        $realName = $params['real_name'] ?? '';
        $bankName = $params['bank_name'] ?? '';
        $bankBranch = $params['bank_branch'] ?? '';
        $qrcode = $params['qrcode'] ?? '';
        $merchantId = intval($params['merchant_id'] ?? 0);

        if (empty($account)) {
            self::$error = '请输入账号';
            return false;
        }
        if (empty($realName)) {
            self::$error = '请输入真实姓名';
            return false;
        }
        if ($type == 3 && empty($bankName)) {
            self::$error = '请输入银行名称';
            return false;
        }

        $where = [['id', '=', $id], ['delete_time', '=', null]];
        if ($merchantId > 0) {
            $where[] = ['merchant_id', '=', $merchantId];
        } else {
            $where[] = ['user_id', '=', $userId];
        }

        $exist = Db::name('withdraw_account')->where($where)->find();
        if (!$exist) {
            self::$error = '账户不存在';
            return false;
        }

        Db::name('withdraw_account')->where($where)->update([
            'type' => $type,
            'account' => $account,
            'real_name' => $realName,
            'bank_name' => $bankName,
            'bank_branch' => $bankBranch,
            'qrcode' => $qrcode,
            'update_time' => time()
        ]);

        return true;
    }

    public static function delete($userId, $id, $merchantId = 0)
    {
        if ($merchantId <= 0) {
            $merchantId = self::getMerchantId($userId);
        }
        
        $query = Db::name('withdraw_account')
            ->where('id', $id)
            ->where('delete_time', null);
        
        if ($merchantId > 0) {
            $query->where('merchant_id', $merchantId);
        } else {
            $query->where('user_id', $userId);
        }

        $exist = $query->find();

        if (!$exist) {
            self::$error = '账户不存在';
            return false;
        }

        Db::name('withdraw_account')->where('id', $id)->update([
            'delete_time' => time()
        ]);

        return true;
    }

    public static function setDefault($userId, $id, $merchantId = 0)
    {
        if ($merchantId <= 0) {
            $merchantId = self::getMerchantId($userId);
        }
        
        $query = Db::name('withdraw_account')
            ->where('id', $id)
            ->where('delete_time', null);
        
        if ($merchantId > 0) {
            $query->where('merchant_id', $merchantId);
        } else {
            $query->where('user_id', $userId);
        }

        $exist = $query->find();

        if (!$exist) {
            self::$error = '账户不存在';
            return false;
        }

        Db::startTrans();
        try {
            if ($merchantId > 0) {
                Db::name('withdraw_account')
                    ->where('merchant_id', $merchantId)
                    ->where('delete_time', null)
                    ->update(['is_default' => 0]);
            } else {
                Db::name('withdraw_account')
                    ->where('user_id', $userId)
                    ->where('delete_time', null)
                    ->update(['is_default' => 0]);
            }

            Db::name('withdraw_account')->where('id', $id)->update([
                'is_default' => 1,
                'update_time' => time()
            ]);

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }

    public static function detail($userId, $id)
    {
        $merchantId = self::getMerchantId($userId);
        
        $query = Db::name('withdraw_account')
            ->where('id', $id)
            ->where('delete_time', null);
        
        if ($merchantId > 0) {
            $query->where('merchant_id', $merchantId);
        } else {
            $query->where('user_id', $userId);
        }

        return $query->find();
    }

    private static function getMerchantId($userId)
    {
        $merchant = Db::name('merchant')->where('user_id', $userId)->find();
        return $merchant ? $merchant['id'] : 0;
    }

    private static function maskAccount($account, $type)
    {
        if ($type == 2) {
            $atPos = strpos($account, '@');
            if ($atPos !== false) {
                $prefix = substr($account, 0, 3);
                $suffix = substr($account, $atPos - 2);
                return $prefix . '***' . $suffix;
            }
            return substr($account, 0, 3) . '***' . substr($account, -3);
        } else {
            $len = strlen($account);
            if ($len > 8) {
                return substr($account, 0, 4) . '****' . substr($account, -4);
            }
            return $account;
        }
    }

    public static function getEnabledMethods()
    {
        $config = WithdrawSettingLogic::getConfig();
        $methods = [];
        
        $methodNames = [
            1 => '微信零钱',
            2 => '支付宝',
            3 => '银行卡'
        ];
        
        foreach ($config['withdraw_methods'] as $method) {
            if ($method['enabled']) {
                $methods[] = [
                    'type' => $method['type'],
                    'name' => $method['name'] ?? $methodNames[$method['type']] ?? ''
                ];
            }
        }
        
        return ['methods' => $methods];
    }
}
