<?php
/**
 * 分销逻辑
 * 开发者公众号：杰哥网络科技
 * qq2711793818 杰哥
 */
namespace app\adminapi\logic\distribution;

use app\common\logic\BaseLogic;
use app\common\model\distribution\DistributionLog;
use app\common\model\user\User;
use think\facade\Db;

class DistributionLogic extends BaseLogic
{
    public static function memberLists($params)
    {
        $where = [];
        if (!empty($params['keyword'])) {
            $where[] = ['u.nickname|u.mobile|u.sn', 'like', '%' . $params['keyword'] . '%'];
        }

        $field = 'u.id, u.sn, u.nickname, u.avatar, u.mobile, u.user_money, u.commission, u.create_time';

        $lists = User::alias('u')
            ->where('u.is_distributor', 1)
            ->where($where)
            ->field($field)
            ->order(['u.id' => 'desc'])
            ->paginate([
                'page' => $params['page_no'] ?? 1,
                'list_rows' => $params['page_size'] ?? 15
            ]);

        return $lists;
    }

    public static function statistics()
    {
        $totalDistributors = User::where('is_distributor', 1)->count();
        $totalCommission = DistributionLog::where('status', 1)->sum('commission');
        $pendingCommission = DistributionLog::where('status', 0)->sum('commission');
        $todayCommission = DistributionLog::where('status', 1)
            ->whereTime('create_time', 'today')
            ->sum('commission');

        return [
            'total_distributors' => $totalDistributors,
            'total_commission' => round($totalCommission, 2),
            'pending_commission' => round($pendingCommission, 2),
            'today_commission' => round($todayCommission, 2),
        ];
    }
}
