<?php
namespace app\common\logic;

use app\common\model\user\User;
use app\common\enum\user\AccountLogEnum;
use think\facade\Db;
use think\facade\Log;

class DistributionLogic extends BaseLogic
{
    const RATIO = 0.10;

    /**
     * 绑定邀请人关系
     * @param $userId int 用户ID
     * @param $inviteCode string 邀请码
     * @param $merchantId int 商家ID
     */
    public static function bindInviter($userId, $inviteCode, $merchantId = 0)
    {
        if (empty($inviteCode) || empty($userId)) {
            return false;
        }

        $user = User::find($userId);
        if (!$user) {
            return false;
        }

        if (!empty($user->inviter_id)) {
            return false;
        }

        $inviter = User::where('sn', $inviteCode)->findOrEmpty();
        if ($inviter->isEmpty()) {
            $inviter = User::where('id', $inviteCode)->findOrEmpty();
        }

        if ($inviter->isEmpty()) {
            return false;
        }

        if ($inviter->id == $userId) {
            return false;
        }

        $user->inviter_id = $inviter->id;
        $user->save();

        if ($merchantId > 0) {
            $existRelation = Db::name('user_merchant')
                ->where(['user_id' => $userId, 'merchant_id' => $merchantId])
                ->find();
            if (!$existRelation) {
                Db::name('user_merchant')->insert([
                    'user_id' => $userId,
                    'merchant_id' => $merchantId,
                    'inviter_id' => $inviter->id,
                    'create_time' => time(),
                    'update_time' => time(),
                ]);
            }
        }

        return true;
    }

    /**
     * 分销结算
     * @param $userId int 下单用户ID
     * @param $amount float 消费金额
     * @param $ratio float 分销比例(百分比)
     * @param $merchantId int 商家ID
     */
    public static function distribute($userId, $amount, $ratio = 0, $merchantId = 0)
    {
        if ($amount <= 0) return;

        if ($ratio <= 0 && $merchantId > 0) {
            $merchant = Db::name('merchant')->find($merchantId);
            if ($merchant && isset($merchant['commission_rate']) && $merchant['commission_rate'] > 0) {
                $ratio = $merchant['commission_rate'];
            }
        }
        
        if ($ratio <= 0) {
            $ratio = self::RATIO * 100;
        }

        $inviterId = 0;
        if ($merchantId > 0) {
            $relation = Db::name('user_merchant')
                ->where(['user_id' => $userId, 'merchant_id' => $merchantId])
                ->find();
            if ($relation) {
                $inviterId = $relation['inviter_id'];
            }
        }

        if (empty($inviterId)) {
            $user = User::findOrEmpty($userId);
            if (!$user->isEmpty() && $user->inviter_id) {
                $inviterId = $user->inviter_id;
            }
        }

        if (empty($inviterId)) return;

        $realRatio = bcdiv($ratio, 100, 4);
        $commission = bcmul($amount, $realRatio, 2);
        
        if (bccomp(strval($commission), '0.01', 2) < 0) return;

        // 使用事务保护
        Db::startTrans();
        try {
            $inviter = User::where('id', $inviterId)->lock(true)->find();
            if (!$inviter) {
                Db::rollback();
                return;
            }

            $inviter->commission = bcadd(strval($inviter->commission), strval($commission), 2);
            $inviter->total_commission = bcadd(strval($inviter->total_commission), strval($commission), 2);
            $inviter->save();

            AccountLogLogic::add(
                $inviter->id,
                AccountLogEnum::UM_INC_DISTRIBUTION,
                AccountLogEnum::INC,
                $commission,
                '',
                '下级消费分销佣金'
            );

            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            Log::error('DistributionLogic Error: ' . $e->getMessage());
        }
    }
}
