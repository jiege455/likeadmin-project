<?php
/**
 * 订单控制器
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */

namespace app\api\controller;

use app\common\service\FileService;
use app\common\enum\PayEnum;
use think\facade\Db;

class OrderController extends BaseApiController
{
    public array $notNeedLogin = ['traces'];

    // 支付超时时间（15分钟）
    const PAY_TIMEOUT = 900;

    public function lists()
    {
        $type = $this->request->get('type', 'all');
        $pageNo = $this->request->get('page_no', 1);
        $pageSize = $this->request->get('page_size', 10);
        $userId = $this->userId;

        $where = [
            ['o.user_id', '=', $userId]
        ];

        switch ($type) {
            case 'pay':
                $where[] = ['o.pay_status', '=', 0];
                break;
            case 'delivery':
            case 'receive':
            case 'finish':
                $where[] = ['o.pay_status', '=', 1];
                break;
            default:
                break;
        }

        $lists = Db::name('article_order')
            ->alias('o')
            ->leftJoin('article a', 'o.article_id = a.id')
            ->leftJoin('merchant m', 'o.merchant_id = m.id')
            ->leftJoin('user_coupon uc', 'o.coupon_id = uc.id')
            ->leftJoin('coupon c', 'uc.coupon_id = c.id')
            ->field('o.*, a.title as article_title, a.image as article_image, m.name as merchant_name, c.name as coupon_name')
            ->where($where)
            ->order('o.id', 'desc')
            ->page($pageNo, $pageSize)
            ->select()
            ->toArray();

        $count = Db::name('article_order')
            ->alias('o')
            ->where('o.user_id', '=', $userId)
            ->count();

        $payWayMap = [
            PayEnum::BALANCE_PAY => '余额支付',
            PayEnum::WECHAT_PAY => '微信支付',
            PayEnum::ALI_PAY => '支付宝支付',
            PayEnum::RAINBOW_PAY => '在线支付'
        ];

        foreach ($lists as &$item) {
            $articleImage = $item['article_image'] ? FileService::getFileUrl($item['article_image']) : '';
            
            $item['sn'] = $item['order_sn'];
            $item['total_num'] = 1;
            $item['order_status_desc'] = $item['pay_status'] == 1 ? '已完成' : '待付款';
            
            $item['original_amount'] = $item['order_amount'];
            if ($item['coupon_id'] > 0 && $item['coupon_money'] > 0) {
                $item['original_amount'] = number_format($item['order_amount'] + $item['coupon_money'], 2, '.', '');
            }
            
            $item['pay_way_text'] = $payWayMap[$item['pay_way']] ?? '';
            $item['pay_status_text'] = $item['pay_status'] == 1 ? '已支付' : '待支付';
            $item['issue_no_text'] = $item['issue_no'] ? '第' . $item['issue_no'] . '期' : '';
            
            $specValue = '';
            if ($item['merchant_name']) {
                $specValue = '商家：' . $item['merchant_name'];
            }
            if (!empty($item['issue_no'])) {
                $specValue .= ($specValue ? ' | ' : '') . '期号：' . $item['issue_no'];
            }
            
            $item['order_goods'] = [
                [
                    'image' => $articleImage,
                    'goods_name' => $item['article_title'] ?: '文章购买',
                    'goods_price' => $item['order_amount'],
                    'original_price' => $item['original_amount'],
                    'goods_num' => 1,
                    'spec_value_str' => $specValue,
                    'coupon_name' => $item['coupon_name'],
                    'coupon_money' => $item['coupon_money']
                ]
            ];
            
            // 检查订单是否超过15分钟未支付
            $isPayTimeout = $item['pay_status'] == 0 && (time() - $item['create_time'] > self::PAY_TIMEOUT);

            $item['cancel_btn'] = $item['pay_status'] == 0 && !$isPayTimeout;
            $item['pay_btn'] = $item['pay_status'] == 0 && !$isPayTimeout;
            $item['pay_timeout'] = $isPayTimeout;
            $item['pay_timeout_text'] = $isPayTimeout ? '已超时' : '';
            $item['confirm_btn'] = false;
            $item['delete_btn'] = $item['pay_status'] == 1 || $isPayTimeout;
            
            $item['pay_time_text'] = $item['pay_time'] ? date('Y-m-d H:i:s', $item['pay_time']) : '';
            $item['create_time_text'] = date('Y-m-d H:i:s', $item['create_time']);
        }

        return $this->data([
            'lists' => $lists,
            'count' => $count,
            'page_no' => $pageNo,
            'page_size' => $pageSize
        ]);
    }

    public function detail()
    {
        $id = intval($this->request->get('id', 0));
        if (empty($id)) {
            return $this->fail('参数错误');
        }

        $order = Db::name('article_order')
            ->alias('o')
            ->leftJoin('article a', 'o.article_id = a.id')
            ->leftJoin('merchant m', 'o.merchant_id = m.id')
            ->leftJoin('user_coupon uc', 'o.coupon_id = uc.id')
            ->leftJoin('coupon c', 'uc.coupon_id = c.id')
            ->field('o.*, a.title as article_title, a.image as article_image, a.desc as article_desc, m.name as merchant_name, c.name as coupon_name')
            ->where('o.id', $id)
            ->where('o.user_id', $this->userId)
            ->find();

        if (!$order) {
            return $this->fail('订单不存在');
        }

        $payWayMap = [
            PayEnum::BALANCE_PAY => '余额支付',
            PayEnum::WECHAT_PAY => '微信支付',
            PayEnum::ALI_PAY => '支付宝支付',
            PayEnum::RAINBOW_PAY => '在线支付'
        ];

        $order['article_image'] = $order['article_image'] ? FileService::getFileUrl($order['article_image']) : '';
        $order['pay_time_text'] = $order['pay_time'] ? date('Y-m-d H:i:s', $order['pay_time']) : '';
        $order['create_time_text'] = date('Y-m-d H:i:s', $order['create_time']);
        $order['status_text'] = $order['pay_status'] == 1 ? '已支付' : '待支付';
        $order['pay_way_text'] = $payWayMap[$order['pay_way']] ?? '';
        $order['issue_no_text'] = $order['issue_no'] ? '第' . $order['issue_no'] . '期' : '';
        
        $order['original_amount'] = $order['order_amount'];
        if ($order['coupon_id'] > 0 && $order['coupon_money'] > 0) {
            $order['original_amount'] = number_format($order['order_amount'] + $order['coupon_money'], 2, '.', '');
        }

        return $this->data($order);
    }

    public function cancel()
    {
        $id = intval($this->request->post('id', 0));
        if (empty($id)) {
            return $this->fail('参数错误');
        }

        $order = Db::name('article_order')
            ->where('id', $id)
            ->where('user_id', $this->userId)
            ->find();

        if (!$order) {
            return $this->fail('订单不存在');
        }

        if ($order['pay_status'] == 1) {
            return $this->fail('已支付订单无法取消');
        }

        Db::name('article_order')->where('id', $id)->delete();

        return $this->success('取消成功');
    }

    public function del()
    {
        $id = intval($this->request->post('id', 0));
        if (empty($id)) {
            return $this->fail('参数错误');
        }

        $order = Db::name('article_order')
            ->where('id', $id)
            ->where('user_id', $this->userId)
            ->find();

        if (!$order) {
            return $this->fail('订单不存在');
        }

        Db::name('article_order')->where('id', $id)->delete();

        return $this->success('删除成功');
    }

    public function confirm()
    {
        return $this->success('操作成功');
    }

    public function traces()
    {
        return $this->data([]);
    }

    public function statistics()
    {
        $userId = $this->userId;

        $totalOrders = Db::name('article_order')
            ->where('user_id', $userId)
            ->count();

        $totalAmount = Db::name('article_order')
            ->where('user_id', $userId)
            ->where('pay_status', 1)
            ->sum('order_amount');
        $totalAmount = $totalAmount ?: '0.00';

        $pendingOrders = Db::name('article_order')
            ->where('user_id', $userId)
            ->where('pay_status', 0)
            ->count();

        return $this->data([
            'total_orders' => $totalOrders,
            'total_amount' => $totalAmount,
            'pending_orders' => $pendingOrders
        ]);
    }
}
