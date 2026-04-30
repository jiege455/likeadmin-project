<?php
/**
 * 商家财务控制器
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
namespace app\api\controller\finance;

use app\api\controller\BaseApiController;
use think\facade\Db;

class MerchantFinanceController extends BaseApiController
{
    /**
     * @notes 获取商家财务详情
     * @return \think\response\Json
     */
    public function detail()
    {
        $merchant = Db::name('merchant')->where('user_id', $this->userId)->find();
        
        if (!$merchant) {
            return $this->fail('您还不是商户');
        }

        $todayStart = strtotime(date('Y-m-d'));
        $monthStart = strtotime(date('Y-m-01'));

        $totalIncome = Db::name('article_order')
            ->where('merchant_id', $merchant['id'])
            ->where('pay_status', 1)
            ->sum('order_amount');

        $todayIncome = Db::name('article_order')
            ->where('merchant_id', $merchant['id'])
            ->where('pay_status', 1)
            ->where('pay_time', '>=', $todayStart)
            ->sum('order_amount');

        $monthIncome = Db::name('article_order')
            ->where('merchant_id', $merchant['id'])
            ->where('pay_status', 1)
            ->where('pay_time', '>=', $monthStart)
            ->sum('order_amount');

        $withdrawn = Db::name('merchant_withdraw')
            ->where('merchant_id', $merchant['id'])
            ->where('status', 2)
            ->sum('amount');

        $withdrawing = Db::name('merchant_withdraw')
            ->where('merchant_id', $merchant['id'])
            ->where('status', 1)
            ->sum('amount');

        return $this->data([
            'balance' => number_format($merchant['money'], 2, '.', ''),
            'total_income' => number_format($totalIncome, 2, '.', ''),
            'today_income' => number_format($todayIncome, 2, '.', ''),
            'month_income' => number_format($monthIncome, 2, '.', ''),
            'withdrawn' => number_format($withdrawn, 2, '.', ''),
            'withdrawing' => number_format($withdrawing, 2, '.', ''),
        ]);
    }
}
