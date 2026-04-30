<?php

namespace app\api\controller;

use think\facade\Db;

class CouponController extends BaseApiController
{
    public array $notNeedLogin = ['lists', 'merchantList'];

    /**
     * 领券中心列表
     * 显示所有商家创建的优惠券
     */
    public function lists()
    {
        $userId = $this->userId;
        
        $lists = Db::name('coupon')
            ->alias('c')
            ->leftJoin('merchant m', 'c.merchant_id = m.id')
            ->where('c.status', 1)
            ->where('c.delete_time', null)
            ->where('c.send_count', '<', Db::raw('c.total_count'))
            ->field('c.*, m.name as merchant_name')
            ->order('c.create_time', 'desc')
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            // 检查用户是否已领取
            $isGet = Db::name('user_coupon')
                ->where('user_id', $userId)
                ->where('coupon_id', $item['id'])
                ->find();
            $item['is_get'] = $isGet ? 1 : 0;
            
            $item['use_time_desc'] = $item['use_time_type'] == 1 
                ? date('Y-m-d', $item['use_time_start']) . ' ~ ' . date('Y-m-d', $item['use_time_end'])
                : '领取后' . $item['use_days'] . '天内有效';
            
            // 商家名称，0表示平台优惠券
            $item['merchant_name'] = $item['merchant_name'] ?: '平台';
        }

        return $this->data($lists);
    }

    /**
     * 商家优惠券列表
     * 显示所有优惠券，已领取的标记状态
     */
    public function merchantList()
    {
        $userId = $this->userId;
        $merchantId = $this->request->get('merchant_id', 0);
        $pageNo = $this->request->get('page_no', 1);
        $pageSize = $this->request->get('page_size', 10);

        // 获取用户已领取的优惠券ID列表
        $receivedCouponIds = Db::name('user_coupon')
            ->where('user_id', $userId)
            ->column('coupon_id');

        $query = Db::name('coupon')
            ->where('status', 1)
            ->where('delete_time', null);

        // 如果指定了商家ID，则筛选该商家的优惠券
        if ($merchantId > 0) {
            $query->where('merchant_id', $merchantId);
        }

        $total = $query->count();
        $lists = $query->order('create_time', 'desc')
            ->page($pageNo, $pageSize)
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            // 检查是否已领取
            $item['is_received'] = in_array($item['id'], $receivedCouponIds);
            // 检查是否已领完
            $item['is_sold_out'] = $item['send_count'] >= $item['total_count'];
            $item['amount'] = $item['money'];
            $item['min_amount'] = $item['condition_money'];
            $item['validity_text'] = $item['use_time_type'] == 1 
                ? date('Y-m-d', $item['use_time_start']) . ' ~ ' . date('Y-m-d', $item['use_time_end'])
                : '领取后' . $item['use_days'] . '天内有效';
            $item['end_time'] = $item['use_time_type'] == 1 
                ? date('Y-m-d', $item['use_time_end'])
                : '领取后' . $item['use_days'] . '天内有效';
            $item['desc'] = '适用于指定商品';
        }

        return $this->data([
            'lists' => $lists,
            'count' => $total,
            'page_no' => $pageNo,
            'page_size' => $pageSize
        ]);
    }

    /**
     * 我的优惠券
     */
    public function myList()
    {
        $userId = $this->userId;
        $status = $this->request->get('status', 0); // 0-未使用, 1-已使用, 2-已过期
        $pageNo = $this->request->get('page_no', 1);
        $pageSize = $this->request->get('page_size', 10);

        $query = Db::name('user_coupon')
            ->alias('uc')
            ->join('coupon c', 'uc.coupon_id = c.id')
            ->where('uc.user_id', $userId)
            ->where('uc.status', $status)
            ->field('uc.*, c.name, c.use_time_type, c.use_days');

        $total = $query->count();
        $lists = $query->order('uc.id', 'desc')
            ->page($pageNo, $pageSize)
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['amount'] = $item['money'];
            $item['min_amount'] = $item['condition_money'];
            $item['validity_text'] = $item['use_time_type'] == 1 
                ? date('Y-m-d', $item['use_time_start']) . ' ~ ' . date('Y-m-d', $item['use_time_end'])
                : '领取后' . $item['use_days'] . '天内有效';
            $item['desc'] = '适用于指定商品';
        }

        return $this->data([
            'lists' => $lists,
            'count' => $total,
            'page_no' => $pageNo,
            'page_size' => $pageSize
        ]);
    }

    /**
     * 可用优惠券列表（下单时使用）
     */
    public function available()
    {
        $userId = $this->userId;
        $amount = $this->request->get('amount', 0);
        $merchantId = $this->request->get('merchant_id', 0);
        $articleId = $this->request->get('article_id', 0);

        $lists = Db::name('user_coupon')
            ->alias('uc')
            ->join('coupon c', 'uc.coupon_id = c.id')
            ->where('uc.user_id', $userId)
            ->where('uc.status', 0)
            ->where('uc.use_time_start', '<=', time())
            ->where('uc.use_time_end', '>=', time())
            ->where('uc.condition_money', '<=', $amount)
            ->field('uc.*, c.name, c.use_time_type, c.use_days')
            ->order('uc.money', 'desc')
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['amount'] = $item['money'];
            $item['min_amount'] = $item['condition_money'];
            $item['validity_text'] = $item['use_time_type'] == 1 
                ? date('Y-m-d', $item['use_time_start']) . ' ~ ' . date('Y-m-d', $item['use_time_end'])
                : '领取后' . $item['use_days'] . '天内有效';
            $item['desc'] = '适用于指定商品';
        }

        return $this->data($lists);
    }

    /**
     * 领取优惠券
     */
    public function receive()
    {
        $couponId = $this->request->post('coupon_id');
        $userId = $this->userId;

        if (!$couponId) {
            return $this->fail('请选择要领取的优惠券');
        }

        $coupon = Db::name('coupon')->find($couponId);
        if (!$coupon || $coupon['status'] != 1 || $coupon['delete_time']) {
            return $this->fail('优惠券不存在或已失效');
        }

        if ($coupon['send_count'] >= $coupon['total_count']) {
            return $this->fail('优惠券已抢光');
        }

        $exist = Db::name('user_coupon')
            ->where('user_id', $userId)
            ->where('coupon_id', $couponId)
            ->find();
        if ($exist) {
            return $this->fail('您已领取过该优惠券');
        }

        // 计算有效期
        $startTime = time();
        $endTime = 0;
        if ($coupon['use_time_type'] == 1) {
            $startTime = $coupon['use_time_start'];
            $endTime = $coupon['use_time_end'];
        } else {
            $endTime = strtotime("+{$coupon['use_days']} days");
        }

        Db::startTrans();
        try {
            Db::name('user_coupon')->insert([
                'user_id' => $userId,
                'coupon_id' => $couponId,
                'money' => $coupon['money'],
                'condition_money' => $coupon['condition_money'],
                'use_time_start' => $startTime,
                'use_time_end' => $endTime,
                'status' => 0,
                'create_time' => time()
            ]);

            Db::name('coupon')->where('id', $couponId)->inc('send_count')->update();
            Db::commit();
            return $this->success('领取成功');
        } catch (\Exception $e) {
            Db::rollback();
            return $this->fail('领取失败');
        }
    }

    /**
     * 领取优惠券（旧接口兼容）
     */
    public function get()
    {
        return $this->receive();
    }
}
