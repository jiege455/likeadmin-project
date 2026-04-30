<?php
/**
 * 文章订单控制器
 * 开发者：杰哥网络科技 qq2711793818 杰哥
 */
namespace app\adminapi\controller\article;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\article\ArticleOrderLists;
use think\facade\Db;

class ArticleOrderController extends BaseAdminController
{
    public function lists()
    {
        return $this->dataLists(new ArticleOrderLists());
    }

    public function detail()
    {
        $id = $this->request->get('id');
        if (empty($id)) {
            return $this->fail('参数错误');
        }

        $order = Db::name('article_order')
            ->alias('o')
            ->leftJoin('user u', 'o.user_id = u.id')
            ->leftJoin('article a', 'o.article_id = a.id')
            ->leftJoin('merchant m', 'o.merchant_id = m.id')
            ->field('o.*, u.nickname, u.avatar, u.mobile as user_mobile, a.title as article_title, m.name as merchant_name, m.mobile as merchant_mobile')
            ->where('o.id', $id)
            ->find();

        if (!$order) {
            return $this->fail('订单不存在');
        }

        $order['pay_status_text'] = $order['pay_status'] == 1 ? '已支付' : '待支付';
        $order['refund_status_text'] = $order['refund_status'] == 1 ? '已退款' : '未退款';
        $order['pay_time'] = $order['pay_time'] ? date('Y-m-d H:i:s', is_numeric($order['pay_time']) ? $order['pay_time'] : strtotime($order['pay_time'])) : '-';
        $order['create_time'] = date('Y-m-d H:i:s', is_numeric($order['create_time']) ? $order['create_time'] : strtotime($order['create_time']));
        $order['refund_time'] = $order['refund_time'] ? date('Y-m-d H:i:s', is_numeric($order['refund_time']) ? $order['refund_time'] : strtotime($order['refund_time'])) : '-';

        return $this->data($order);
    }

    public function statistics()
    {
        $totalOrders = Db::name('article_order')->count();
        $paidOrders = Db::name('article_order')->where('pay_status', 1)->count();
        $totalAmount = Db::name('article_order')->where('pay_status', 1)->sum('order_amount');
        $todayAmount = Db::name('article_order')
            ->where('pay_status', 1)
            ->where('pay_time', '>=', strtotime(date('Y-m-d')))
            ->sum('order_amount');
        $refundAmount = Db::name('article_order')
            ->where('refund_status', 1)
            ->sum('order_amount');

        return $this->data([
            'total_orders' => $totalOrders,
            'paid_orders' => $paidOrders,
            'total_amount' => number_format($totalAmount, 2, '.', ''),
            'today_amount' => number_format($todayAmount, 2, '.', ''),
            'refund_amount' => number_format($refundAmount, 2, '.', '')
        ]);
    }

    public function refund()
    {
        $id = $this->request->post('id');
        $reason = $this->request->post('reason', '');

        if (empty($id)) {
            return $this->fail('参数错误');
        }

        $order = Db::name('article_order')->find($id);
        if (!$order) {
            return $this->fail('订单不存在');
        }

        if ($order['pay_status'] != 1) {
            return $this->fail('订单未支付，无法退款');
        }

        if ($order['refund_status'] == 1) {
            return $this->fail('订单已退款，请勿重复操作');
        }

        if (empty($reason)) {
            return $this->fail('请输入退款原因');
        }

        Db::startTrans();
        try {
            Db::name('article_order')->where('id', $id)->update([
                'refund_status' => 1,
                'refund_time' => time(),
                'refund_reason' => $reason,
                'update_time' => time()
            ]);

            if ($order['merchant_id'] > 0 && $order['merchant_profit'] > 0) {
                Db::name('merchant')->where('id', $order['merchant_id'])->inc('money', $order['merchant_profit'])->update();
                
                Db::name('merchant_income_log')->insert([
                    'merchant_id' => $order['merchant_id'],
                    'source_type' => 1,
                    'source_id' => $order['article_id'],
                    'amount' => $order['merchant_profit'],
                    'platform_ratio' => $order['platform_ratio'] ?? 0,
                    'remark' => '订单退款返还：' . $reason . '，订单号：' . $order['order_sn'],
                    'create_time' => time()
                ]);
            }

            Db::commit();
            return $this->success('退款成功');
        } catch (\Exception $e) {
            Db::rollback();
            return $this->fail('退款失败：' . $e->getMessage());
        }
    }
}
