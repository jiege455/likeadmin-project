<?php

namespace app\api\logic;

use app\common\logic\BaseLogic;
use app\common\model\distribution\DistributionApply;
use app\common\model\user\User;

/**
 * 分销逻辑层
 * Class DistributionLogic
 * @package app\api\logic
 */
class DistributionLogic extends BaseLogic
{
    /**
     * @notes 申请成为分销员
     * @param $userId
     * @param $params
     * @return bool
     */
    public static function apply($userId, $params)
    {
        try {
            // 检查是否已经是分销员
            $user = User::find($userId);
            if ($user['is_distributor'] == 1) {
                throw new \Exception('您已经是分销员，无需重复申请');
            }

            // 检查是否有待审核的申请
            $exist = DistributionApply::where('user_id', $userId)
                ->where('status', DistributionApply::STATUS_WAIT)
                ->find();
            if ($exist) {
                throw new \Exception('您有正在审核中的申请，请耐心等待');
            }

            // 创建申请
            DistributionApply::create([
                'user_id' => $userId,
                'name' => $params['name'],
                'mobile' => $params['mobile'],
                'reason' => $params['reason'] ?? '',
                'status' => DistributionApply::STATUS_WAIT
            ]);

            return true;
        } catch (\Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 获取最新申请状态
     * @param $userId
     * @return array|null
     */
    public static function getApplyStatus($userId)
    {
        $apply = DistributionApply::where('user_id', $userId)
            ->order('id', 'desc')
            ->find();
            
        // 补充用户信息里的分销状态，方便前端判断
        $user = User::find($userId);
        
        return [
            'apply' => $apply ? $apply->toArray() : null,
            'is_distributor' => $user['is_distributor']
        ];
    }
}
