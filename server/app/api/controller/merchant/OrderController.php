<?php
namespace app\api\controller\merchant;

use app\api\controller\BaseApiController;
use app\common\service\FileService;
use app\common\enum\PayEnum;
use think\facade\Db;

class OrderController extends BaseApiController
{
    public function lists()
    {
        $merchants = Db::name('merchant')->where('user_id', $this->userId)->select()->toArray();
        if (empty($merchants)) {
            return $this->fail('您还不是商户，请先申请成为商户');
        }

        $merchantIds = array_column($merchants, 'id');
        
        $where = [];
        $where[] = ['o.merchant_id', 'in', $merchantIds];
        
        $payStatus = $this->request->get('pay_status');
        if ($payStatus !== '' && $payStatus !== null) {
            $where[] = ['o.pay_status', '=', $payStatus];
        }

        $refundStatus = $this->request->get('refund_status');
        if ($refundStatus !== '' && $refundStatus !== null) {
            $where[] = ['o.refund_status', '=', $refundStatus];
        }

        $lists = Db::name('article_order')
            ->alias('o')
            ->leftJoin('user u', 'o.user_id = u.id')
            ->leftJoin('article a', 'o.article_id = a.id')
            ->leftJoin('merchant m', 'o.merchant_id = m.id')
            ->leftJoin('user_coupon uc', 'o.coupon_id = uc.id')
            ->leftJoin('coupon c', 'uc.coupon_id = c.id')
            ->field('o.*, u.nickname, u.avatar, a.title as article_title, a.image as article_image, m.name as merchant_name, c.name as coupon_name')
            ->where($where)
            ->order('o.id', 'desc')
            ->page($this->request->get('page', 1), $this->request->get('limit', 20))
            ->select()
            ->toArray();

        $payWayMap = [
            PayEnum::BALANCE_PAY => '余额支付',
            PayEnum::WECHAT_PAY => '微信支付',
            PayEnum::ALI_PAY => '支付宝支付'
        ];

        foreach ($lists as &$item) {
            $item['avatar'] = $item['avatar'] ? FileService::getFileUrl($item['avatar']) : '';
            $item['article_image'] = $item['article_image'] ? FileService::getFileUrl($item['article_image']) : '';
            $item['pay_time_text'] = $item['pay_time'] ? date('Y-m-d H:i:s', $item['pay_time']) : '';
            $item['create_time_text'] = date('Y-m-d H:i:s', $item['create_time']);
            
            $item['original_amount'] = $item['order_amount'];
            if ($item['coupon_id'] > 0 && $item['coupon_money'] > 0) {
                $item['original_amount'] = number_format($item['order_amount'] + $item['coupon_money'], 2, '.', '');
            }
            
            $item['pay_way_text'] = $payWayMap[$item['pay_way']] ?? '未知';
            $item['pay_status_text'] = $item['pay_status'] == 1 ? '已支付' : '待支付';
            $item['refund_status_text'] = $item['refund_status'] == 1 ? '已退款' : '未退款';
            $item['refund_time_text'] = $item['refund_time'] ? date('Y-m-d H:i:s', $item['refund_time']) : '';
            
            $item['issue_no_text'] = $item['issue_no'] ? '第' . $item['issue_no'] . '期' : '';
            
            $item['profit_text'] = '';
            if ($item['pay_status'] == 1) {
                $item['profit_text'] = '商户利润: ¥' . number_format($item['merchant_profit'], 2);
            }
        }

        $total = Db::name('article_order')
            ->alias('o')
            ->where('o.merchant_id', 'in', $merchantIds)
            ->count();

        return $this->data([
            'lists' => $lists,
            'count' => $total,
            'merchant_ids' => $merchantIds
        ]);
    }

    public function statistics()
    {
        $merchants = Db::name('merchant')->where('user_id', $this->userId)->select()->toArray();
        if (empty($merchants)) {
            return $this->fail('您还不是商户，请先申请成为商户');
        }

        $merchantIds = array_column($merchants, 'id');

        $orderCount = Db::name('article_order')->where('merchant_id', 'in', $merchantIds)->where('pay_status', 1)->count();
        $totalIncome = Db::name('article_order')->where('merchant_id', 'in', $merchantIds)->where('pay_status', 1)->sum('order_amount');
        $totalProfit = Db::name('article_order')->where('merchant_id', 'in', $merchantIds)->where('pay_status', 1)->sum('merchant_profit');
        $todayIncome = Db::name('article_order')
            ->where('merchant_id', 'in', $merchantIds)
            ->where('pay_status', 1)
            ->where('pay_time', '>=', strtotime(date('Y-m-d')))
            ->sum('order_amount');

        $refundCount = Db::name('article_order')->where('merchant_id', 'in', $merchantIds)->where('pay_status', 1)->where('refund_status', 1)->count();
        $refundAmount = Db::name('article_order')->where('merchant_id', 'in', $merchantIds)->where('pay_status', 1)->where('refund_status', 1)->sum('order_amount');
        $refundProfit = Db::name('article_order')->where('merchant_id', 'in', $merchantIds)->where('pay_status', 1)->where('refund_status', 1)->sum('merchant_profit');

        return $this->data([
            'order_count' => $orderCount,
            'total_income' => number_format($totalIncome, 2, '.', ''),
            'total_profit' => number_format($totalProfit, 2, '.', ''),
            'today_income' => number_format($todayIncome, 2, '.', ''),
            'refund_count' => $refundCount,
            'refund_amount' => number_format($refundAmount, 2, '.', ''),
            'refund_profit' => number_format($refundProfit, 2, '.', ''),
            'actual_income' => number_format($totalIncome - $refundAmount, 2, '.', ''),
            'actual_profit' => number_format($totalProfit - $refundProfit, 2, '.', ''),
        ]);
    }
}
