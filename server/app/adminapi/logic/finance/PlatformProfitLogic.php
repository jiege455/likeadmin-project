<?php
namespace app\adminapi\logic\finance;

use app\common\logic\BaseLogic;
use think\facade\Db;

class PlatformProfitLogic extends BaseLogic
{
    public static function statistics()
    {
        $totalOrderAmount = Db::name('article_order')->where('pay_status', 1)->sum('order_amount');
        $totalPlatformProfit = Db::name('article_order')->where('pay_status', 1)->sum('platform_profit');
        $totalMerchantProfit = Db::name('article_order')->where('pay_status', 1)->sum('merchant_profit');
        
        $todayStart = strtotime(date('Y-m-d'));
        $todayOrderAmount = Db::name('article_order')->where('pay_status', 1)->where('pay_time', '>=', $todayStart)->sum('order_amount');
        $todayPlatformProfit = Db::name('article_order')->where('pay_status', 1)->where('pay_time', '>=', $todayStart)->sum('platform_profit');
        
        $monthStart = strtotime(date('Y-m-01'));
        $monthOrderAmount = Db::name('article_order')->where('pay_status', 1)->where('pay_time', '>=', $monthStart)->sum('order_amount');
        $monthPlatformProfit = Db::name('article_order')->where('pay_status', 1)->where('pay_time', '>=', $monthStart)->sum('platform_profit');

        $pendingSettle = Db::name('merchant')
            ->where('money', '>', 0)
            ->sum('money');

        $settledAmount = Db::name('withdraw_apply')->where('status', 3)->sum('money');

        return [
            'total_order_amount' => number_format($totalOrderAmount, 2, '.', ''),
            'total_platform_profit' => number_format($totalPlatformProfit ?: 0, 2, '.', ''),
            'total_merchant_profit' => number_format($totalMerchantProfit ?: 0, 2, '.', ''),
            'today_order_amount' => number_format($todayOrderAmount, 2, '.', ''),
            'today_platform_profit' => number_format($todayPlatformProfit ?: 0, 2, '.', ''),
            'month_order_amount' => number_format($monthOrderAmount, 2, '.', ''),
            'month_platform_profit' => number_format($monthPlatformProfit ?: 0, 2, '.', ''),
            'pending_settle' => number_format($pendingSettle, 2, '.', ''),
            'settled_amount' => number_format($settledAmount, 2, '.', ''),
        ];
    }

    public static function trend($get)
    {
        $type = $get['type'] ?? 'week';
        $data = [];

        if ($type === 'week') {
            for ($i = 6; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-{$i} days"));
                $start = strtotime($date);
                $end = $start + 86400;
                
                $orderAmount = Db::name('article_order')
                    ->where('pay_status', 1)
                    ->where('pay_time', '>=', $start)
                    ->where('pay_time', '<', $end)
                    ->sum('order_amount');
                
                $platformProfit = Db::name('article_order')
                    ->where('pay_status', 1)
                    ->where('pay_time', '>=', $start)
                    ->where('pay_time', '<', $end)
                    ->sum('platform_profit');

                $data[] = [
                    'date' => date('m-d', $start),
                    'order_amount' => floatval($orderAmount),
                    'platform_profit' => floatval($platformProfit ?: 0),
                ];
            }
        } elseif ($type === 'month') {
            for ($i = 29; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-{$i} days"));
                $start = strtotime($date);
                $end = $start + 86400;
                
                $orderAmount = Db::name('article_order')
                    ->where('pay_status', 1)
                    ->where('pay_time', '>=', $start)
                    ->where('pay_time', '<', $end)
                    ->sum('order_amount');
                
                $platformProfit = Db::name('article_order')
                    ->where('pay_status', 1)
                    ->where('pay_time', '>=', $start)
                    ->where('pay_time', '<', $end)
                    ->sum('platform_profit');

                $data[] = [
                    'date' => date('m-d', $start),
                    'order_amount' => floatval($orderAmount),
                    'platform_profit' => floatval($platformProfit ?: 0),
                ];
            }
        }

        return ['lists' => $data];
    }

    public static function merchantProfit($get)
    {
        $where = [];
        $where[] = ['m.delete_time', '=', null];

        if (!empty($get['merchant_name'])) {
            $where[] = ['m.name', 'like', '%' . $get['merchant_name'] . '%'];
        }

        $count = Db::name('merchant')->alias('m')->where($where)->count();
        $lists = Db::name('merchant')
            ->alias('m')
            ->field('m.id, m.name, m.money as balance')
            ->where($where)
            ->page($get['page_no'] ?? 1, $get['page_size'] ?? 15)
            ->order('m.id', 'desc')
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['total_order_amount'] = Db::name('article_order')
                ->where('merchant_id', $item['id'])
                ->where('pay_status', 1)
                ->sum('order_amount');
            $item['total_order_count'] = Db::name('article_order')
                ->where('merchant_id', $item['id'])
                ->where('pay_status', 1)
                ->count();
            $item['settled_amount'] = Db::name('withdraw_apply')
                ->where('merchant_id', $item['id'])
                ->where('status', 3)
                ->sum('money');
            $item['total_order_amount'] = number_format($item['total_order_amount'], 2, '.', '');
            $item['settled_amount'] = number_format($item['settled_amount'], 2, '.', '');
            $item['balance'] = number_format($item['balance'], 2, '.', '');
        }

        return ['count' => $count, 'lists' => $lists];
    }

    public static function settleList($get)
    {
        $where = [];
        $where[] = ['w.delete_time', '=', null];

        if (!empty($get['merchant_name'])) {
            $where[] = ['m.name', 'like', '%' . $get['merchant_name'] . '%'];
        }
        if (isset($get['status']) && $get['status'] !== '') {
            $where[] = ['w.status', '=', $get['status']];
        }

        $count = Db::name('withdraw_apply')->alias('w')->where($where)->count();
        $lists = Db::name('withdraw_apply')
            ->alias('w')
            ->leftJoin('merchant m', 'w.merchant_id = m.id')
            ->field('w.*, m.name as merchant_name')
            ->where($where)
            ->page($get['page_no'] ?? 1, $get['page_size'] ?? 15)
            ->order('w.id', 'desc')
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
            $item['audit_time'] = $item['audit_time'] ? date('Y-m-d H:i:s', $item['audit_time']) : '';
            $item['status_text'] = self::getStatusText($item['status']);
        }

        return ['count' => $count, 'lists' => $lists];
    }

    public static function settle($params)
    {
        $id = $params['id'];

        $withdraw = Db::name('withdraw_apply')->find($id);
        if (!$withdraw) {
            throw new \Exception('提现申请不存在');
        }
        if ($withdraw['status'] != 2) {
            throw new \Exception('该申请未通过审核');
        }

        Db::name('withdraw_apply')->where('id', $id)->update([
            'status' => 3,
            'update_time' => time()
        ]);

        return true;
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
