<?php
namespace app\adminapi\logic\merchant;

use app\common\logic\BaseLogic;
use app\common\logic\EmailNotifyLogic;
use think\facade\Db;

class WithdrawLogic extends BaseLogic
{
    public static function lists($get)
    {
        $where = [];
        $where[] = ['w.delete_time', '=', null];
        
        if (!empty($get['merchant_name'])) {
            $where[] = ['m.name', 'like', '%' . $get['merchant_name'] . '%'];
        }
        if (isset($get['status']) && $get['status'] !== '') {
            $where[] = ['w.status', '=', $get['status']];
        }
        if (!empty($get['start_time'])) {
            $where[] = ['w.create_time', '>=', strtotime($get['start_time'])];
        }
        if (!empty($get['end_time'])) {
            $where[] = ['w.create_time', '<=', strtotime($get['end_time'] . ' 23:59:59')];
        }
        $where[] = ['w.source', '=', 1];

        $count = Db::name('withdraw_apply')->alias('w')->where($where)->count();
        $lists = Db::name('withdraw_apply')
            ->alias('w')
            ->leftJoin('merchant m', 'w.merchant_id = m.id')
            ->field('w.*, m.name as merchant_name, m.mobile as merchant_mobile')
            ->where($where)
            ->page($get['page_no'] ?? 1, $get['page_size'] ?? 15)
            ->order('w.id', 'desc')
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['status_text'] = self::getStatusText($item['status']);
            $item['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
            $item['audit_time'] = $item['audit_time'] ? date('Y-m-d H:i:s', $item['audit_time']) : '';
            
            $accountInfo = json_decode($item['account_info'] ?? '{}', true);
            $item['bank_name'] = $accountInfo['bank_name'] ?? '';
            $item['bank_branch'] = $accountInfo['bank_branch'] ?? '';
            $item['bank_account'] = $accountInfo['account'] ?? '';
            $item['bank_user'] = $accountInfo['real_name'] ?? '';
            $item['qrcode'] = $accountInfo['qrcode'] ?? '';
            $item['type_text'] = self::getTypeText($item['type'] ?? 2);
        }

        return ['count' => $count, 'lists' => $lists];
    }

    public static function detail($id)
    {
        $item = Db::name('withdraw_apply')
            ->alias('w')
            ->leftJoin('merchant m', 'w.merchant_id = m.id')
            ->field('w.*, m.name as merchant_name, m.mobile as merchant_mobile')
            ->where('w.id', $id)
            ->find();

        if (!$item) return null;

        $item['status_text'] = self::getStatusText($item['status']);
        $item['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
        $item['audit_time'] = $item['audit_time'] ? date('Y-m-d H:i:s', $item['audit_time']) : '';
        
        $accountInfo = json_decode($item['account_info'] ?? '{}', true);
        $item['bank_name'] = $accountInfo['bank_name'] ?? '';
        $item['bank_branch'] = $accountInfo['bank_branch'] ?? '';
        $item['bank_account'] = $accountInfo['account'] ?? '';
        $item['bank_user'] = $accountInfo['real_name'] ?? '';
        $item['qrcode'] = $accountInfo['qrcode'] ?? '';
        $item['type_text'] = self::getTypeText($item['type'] ?? 2);

        return $item;
    }

    private static function getTypeText($type)
    {
        $texts = [
            1 => '微信',
            2 => '支付宝',
            3 => '银行卡'
        ];
        return $texts[$type] ?? '未知';
    }

    public static function statistics()
    {
        $totalAmount = Db::name('withdraw_apply')->where('source', 1)->sum('money');
        $pendingAmount = Db::name('withdraw_apply')->where('source', 1)->where('status', 0)->sum('money');
        $pendingCount = Db::name('withdraw_apply')->where('source', 1)->where('status', 0)->count();
        $successAmount = Db::name('withdraw_apply')->where('source', 1)->whereIn('status', [2, 3])->sum('money');

        return [
            'total_amount' => number_format($totalAmount, 2, '.', ''),
            'pending_amount' => number_format($pendingAmount, 2, '.', ''),
            'pending_count' => $pendingCount,
            'success_amount' => number_format($successAmount, 2, '.', ''),
        ];
    }

    public static function audit($params)
    {
        Db::startTrans();
        try {
            $id = $params['id'];
            $status = $params['status'];
            $reason = $params['reason'] ?? '';

            $withdraw = Db::name('withdraw_apply')->find($id);
            if (!$withdraw) {
                throw new \Exception('提现记录不存在');
            }

            if ($withdraw['status'] != 0) {
                throw new \Exception('该提现已审核');
            }

            $data = [
                'status' => $status,
                'audit_time' => time(),
                'audit_remark' => $reason,
                'update_time' => time()
            ];

            Db::name('withdraw_apply')->where('id', $id)->update($data);

            if ($status == 1) {
                Db::name('merchant')->where('id', $withdraw['merchant_id'])->inc('money', $withdraw['money'])->update();
            }

            if ($status == 3) {
                Db::name('merchant')->where('id', $withdraw['merchant_id'])->inc('total_income', $withdraw['money'])->update();
            }

            try {
                EmailNotifyLogic::sendWithdrawNotify($withdraw['merchant_id'], [
                    'amount' => $withdraw['money'],
                    'status' => $status,
                    'audit_remark' => $reason
                ]);
            } catch (\Exception $e) {
                \think\facade\Log::error('Withdraw Email Notify Error: ' . $e->getMessage());
            }

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
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
