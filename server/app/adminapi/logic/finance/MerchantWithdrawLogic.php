<?php
namespace app\adminapi\logic\finance;

use app\common\logic\BaseLogic;
use app\common\logic\EmailNotifyLogic;
use app\common\model\finance\WithdrawApply;
use app\common\model\merchant\Merchant;
use think\facade\Db;

class MerchantWithdrawLogic extends BaseLogic
{
    /**
     * @notes 提现列表
     * @param $params
     * @return array
     * @author 杰哥
     * @date 2026-02-01
     */
    public static function lists($params)
    {
        $where = [];
        $where[] = ['w.delete_time', '=', null];
        
        if (isset($params['status']) && $params['status'] != '') {
            $where[] = ['w.status', '=', $params['status']];
        }
        if (!empty($params['keyword'])) {
            $where[] = ['m.name|u.nickname|u.mobile', 'like', '%' . $params['keyword'] . '%'];
        }
        if (isset($params['source']) && $params['source'] != '') {
            $where[] = ['w.source', '=', $params['source']];
        }

        $count = Db::name('withdraw_apply')->alias('w')->where($where)->count();
        $lists = Db::name('withdraw_apply')
            ->alias('w')
            ->leftJoin('merchant m', 'w.merchant_id = m.id')
            ->leftJoin('user u', 'w.user_id = u.id')
            ->where($where)
            ->field('w.*, m.name as merchant_name, u.nickname, u.mobile as user_mobile')
            ->page($params['page_no'] ?? 1, $params['page_size'] ?? 10)
            ->order('w.id', 'desc')
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['source_text'] = $item['source'] == 1 ? '商户收益' : '推广佣金';
            $item['status_text'] = self::getStatusText($item['status']);
            $item['type_text'] = self::getTypeText($item['type']);
            $item['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
            $item['audit_time'] = $item['audit_time'] ? date('Y-m-d H:i:s', $item['audit_time']) : '';
            
            // 解析账户信息JSON
            if (!empty($item['account_info'])) {
                $accountInfo = json_decode($item['account_info'], true);
                if ($accountInfo) {
                    $item['account_info'] = self::formatAccountInfo($item['type'], $accountInfo);
                }
            }
        }

        return [
            'lists' => $lists,
            'count' => $count,
            'page_no' => $params['page_no'] ?? 1,
            'page_size' => $params['page_size'] ?? 10,
        ];
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

    private static function getTypeText($type)
    {
        $texts = [
            1 => '微信零钱',
            2 => '支付宝',
            3 => '银行卡'
        ];
        return $texts[$type] ?? '未知';
    }

    /**
     * 格式化账户信息显示
     */
    private static function formatAccountInfo($type, $accountInfo)
    {
        if (empty($accountInfo)) {
            return '';
        }

        switch ($type) {
            case 1: // 微信零钱
                return '微信: ' . ($accountInfo['nickname'] ?? $accountInfo['real_name'] ?? '未知');
            case 2: // 支付宝
                $name = $accountInfo['real_name'] ?? '';
                $account = $accountInfo['account'] ?? '';
                return $name ? "{$name} | {$account}" : $account;
            case 3: // 银行卡
                $bankName = $accountInfo['bank_name'] ?? '';
                $cardNo = $accountInfo['card_no'] ?? '';
                $cardNo = substr($cardNo, -4); // 只显示后4位
                return $bankName ? "{$bankName} | 尾号{$cardNo}" : "尾号{$cardNo}";
            default:
                return json_encode($accountInfo);
        }
    }

    public static function statistics()
    {
        $total = Db::name('withdraw_apply')->where('delete_time', null)->sum('money');
        $pending = Db::name('withdraw_apply')->where('delete_time', null)->where('status', 0)->sum('money');
        $success = Db::name('withdraw_apply')->where('delete_time', null)->where('status', 3)->sum('money');
        $pendingCount = Db::name('withdraw_apply')->where('delete_time', null)->where('status', 0)->count();
        $merchantTotal = Db::name('withdraw_apply')->where('delete_time', null)->where('source', 1)->sum('money');
        $commissionTotal = Db::name('withdraw_apply')->where('delete_time', null)->where('source', 2)->sum('money');

        return [
            'total' => number_format($total, 2, '.', ''),
            'pending' => number_format($pending, 2, '.', ''),
            'success' => number_format($success, 2, '.', ''),
            'pending_count' => $pendingCount,
            'merchant_total' => number_format($merchantTotal, 2, '.', ''),
            'commission_total' => number_format($commissionTotal, 2, '.', ''),
        ];
    }

    /**
     * @notes 提现详情
     * @param $id
     * @return array|\think\Model|null
     * @author 杰哥
     * @date 2026-02-01
     */
    public static function detail($id)
    {
        return WithdrawApply::alias('w')
            ->join('merchant m', 'w.merchant_id = m.id')
            ->where('w.id', $id)
            ->field('w.*, m.name as merchant_name')
            ->append(['status_desc', 'type_desc'])
            ->find();
    }

    /**
     * @notes 审核提现
     * @param $params
     * @return bool
     * @author 杰哥
     * @date 2026-02-01
     */
    public static function audit($params)
    {
        $id = $params['id'];
        $status = $params['status'];
        $remark = $params['remark'] ?? '';

        $apply = WithdrawApply::find($id);
        if (!$apply) {
            self::$error = '申请不存在';
            return false;
        }

        if ($status == WithdrawApply::STATUS_PAID) {
            if ($apply->status != WithdrawApply::STATUS_PASS) {
                self::$error = '只有已通过的申请才能确认打款';
                return false;
            }
            
            Db::startTrans();
            try {
                $apply->status = WithdrawApply::STATUS_PAID;
                $apply->audit_time = time();
                $apply->audit_remark = $remark;
                $apply->save();

                Db::commit();
                return true;
            } catch (\Exception $e) {
                Db::rollback();
                self::$error = $e->getMessage();
                return false;
            }
        }

        if ($apply->status != WithdrawApply::STATUS_WAIT) {
            self::$error = '申请状态已变更';
            return false;
        }

        Db::startTrans();
        try {
            if ($status == WithdrawApply::STATUS_PASS) {
                $apply->status = $status;
                $apply->audit_time = time();
                $apply->audit_remark = $remark;
                $apply->save();
            } elseif ($status == WithdrawApply::STATUS_REJECT) {
                $apply->status = WithdrawApply::STATUS_REJECT;
                $apply->audit_time = time();
                $apply->audit_remark = $remark;
                $apply->save();

                // 拒绝时退回余额并记录日志
                if ($apply->source == 1 && $apply->merchant_id > 0) {
                    // 使用行锁防止并发问题
                    $merchant = Merchant::where('id', $apply->merchant_id)->lock(true)->find();
                    if ($merchant) {
                        $merchant->money = bcadd($merchant->money, $apply->money, 2);
                        $merchant->save();
                        
                        // 记录商户资金日志
                        Db::name('merchant_income_log')->insert([
                            'merchant_id' => $apply->merchant_id,
                            'money' => $apply->money,
                            'type' => 2, // 2=提现拒绝退回
                            'source' => 1, // 1=商户收益
                            'remark' => '提现申请被拒绝，金额退回（ID:' . $apply->id . '）',
                            'create_time' => time()
                        ]);
                    }
                } elseif ($apply->source == 2 && $apply->user_id > 0) {
                    // 使用行锁防止并发问题
                    Db::name('user')->where('id', $apply->user_id)->lock(true)->find();
                    Db::name('user')->where('id', $apply->user_id)->inc('commission', $apply->money)->update();
                    
                    // 记录用户资金日志
                    Db::name('user_account_log')->insert([
                        'user_id' => $apply->user_id,
                        'sn' => 'TJ' . date('YmdHis') . rand(1000, 9999),
                        'money' => $apply->money,
                        'type' => 1, // 1=收入
                        'change_type' => 10, // 10=提现拒绝退回
                        'remark' => '提现申请被拒绝，佣金退回（ID:' . $apply->id . '）',
                        'create_time' => time()
                    ]);
                }
            } else {
                throw new \Exception('无效的状态');
            }

            Db::commit();

            try {
                $withdrawInfo = [
                    'amount' => $apply->money,
                    'status' => $status == WithdrawApply::STATUS_PASS ? 1 : 2,
                    'audit_remark' => $remark
                ];
                if ($apply->source == 1 && $apply->merchant_id > 0) {
                    EmailNotifyLogic::sendWithdrawNotify($apply->merchant_id, $withdrawInfo);
                }
            } catch (\Exception $e) {
                \think\facade\Log::error('Withdraw Email Notify Error: ' . $e->getMessage());
            }

            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }
}
