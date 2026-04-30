<?php
namespace app\api\controller\merchant;

use app\api\controller\BaseApiController;
use app\common\service\FileService;
use think\facade\Db;

/**
 * 商家粉丝/客户控制器
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 */
class FanController extends BaseApiController
{
    public function fans()
    {
        $merchant = Db::name('merchant')->where('user_id', $this->userId)->find();
        if (!$merchant) {
            return $this->fail('您还不是商户');
        }

        $where = [];
        $where[] = ['f.merchant_id', '=', $merchant['id']];

        $keyword = $this->request->get('keyword');
        if ($keyword) {
            $where[] = ['u.nickname|u.mobile', 'like', '%' . $keyword . '%'];
        }

        $count = Db::name('merchant_follow')->alias('f')->where($where)->count();
        $lists = Db::name('merchant_follow')
            ->alias('f')
            ->leftJoin('user u', 'f.user_id = u.id')
            ->field('f.id, f.user_id, f.create_time, u.nickname, u.avatar, u.mobile')
            ->where($where)
            ->order('f.id', 'desc')
            ->page($this->request->get('page', 1), $this->request->get('limit', 20))
            ->select()
            ->toArray();

        if (!empty($lists)) {
            // 优化：使用单个查询获取所有用户的订单统计
            $userIds = array_column($lists, 'user_id');
            $orderStats = Db::name('article_order')
                ->where('user_id', 'in', $userIds)
                ->where('merchant_id', $merchant['id'])
                ->where('pay_status', 1)
                ->field([
                    'user_id',
                    'COUNT(*) as order_count',
                    'SUM(order_amount) as order_amount'
                ])
                ->group('user_id')
                ->column(null, 'user_id');

            foreach ($lists as &$item) {
                $item['avatar'] = $item['avatar'] ? FileService::getFileUrl($item['avatar']) : '';
                $item['create_time'] = date('Y-m-d H:i', $item['create_time']);
                $stats = $orderStats[$item['user_id']] ?? ['order_count' => 0, 'order_amount' => 0];
                $item['order_count'] = $stats['order_count'];
                $item['order_amount'] = number_format($stats['order_amount'], 2, '.', '');
            }
        }

        return $this->data(['count' => $count, 'lists' => $lists]);
    }

    public function customers()
    {
        $merchant = Db::name('merchant')->where('user_id', $this->userId)->find();
        if (!$merchant) {
            return $this->fail('您还不是商户');
        }

        $where = [];
        $where[] = ['o.merchant_id', '=', $merchant['id']];
        $where[] = ['o.pay_status', '=', 1];

        $keyword = $this->request->get('keyword');
        if ($keyword) {
            $where[] = ['u.nickname|u.mobile', 'like', '%' . $keyword . '%'];
        }

        $userIds = Db::name('article_order')
            ->where('merchant_id', $merchant['id'])
            ->where('pay_status', 1)
            ->column('distinct user_id');

        $count = count($userIds);
        
        $lists = Db::name('article_order')
            ->alias('o')
            ->leftJoin('user u', 'o.user_id = u.id')
            ->field('o.user_id, u.nickname, u.avatar, u.mobile, COUNT(o.id) as order_count, SUM(o.order_amount) as total_amount, MAX(o.pay_time) as last_pay_time')
            ->where($where)
            ->group('o.user_id')
            ->order('total_amount', 'desc')
            ->page($this->request->get('page', 1), $this->request->get('limit', 20))
            ->select()
            ->toArray();

        if (!empty($lists)) {
            // 优化：使用单个查询获取所有用户的关注状态
            $customerIds = array_column($lists, 'user_id');
            $followedIds = Db::name('merchant_follow')
                ->where('user_id', 'in', $customerIds)
                ->where('merchant_id', $merchant['id'])
                ->column('user_id');
            $followedIds = array_flip($followedIds);

            foreach ($lists as &$item) {
                $item['avatar'] = $item['avatar'] ? FileService::getFileUrl($item['avatar']) : '';
                $item['total_amount'] = number_format($item['total_amount'], 2, '.', '');
                $item['last_pay_time'] = $item['last_pay_time'] ? date('Y-m-d H:i', $item['last_pay_time']) : '';
                $item['is_fan'] = isset($followedIds[$item['user_id']]) ? 1 : 0;
            }
        }

        return $this->data(['count' => $count, 'lists' => $lists]);
    }

    public function statistics()
    {
        $merchant = Db::name('merchant')->where('user_id', $this->userId)->find();
        if (!$merchant) {
            return $this->fail('您还不是商户');
        }

        $todayStart = strtotime(date('Y-m-d'));

        // 优化：使用单个查询获取所有统计数据
        $stats = Db::name('merchant_follow')
            ->where('merchant_id', $merchant['id'])
            ->field([
                'COUNT(*) as fans_count',
                'SUM(CASE WHEN create_time >= ' . $todayStart . ' THEN 1 ELSE 0 END) as new_fans_today'
            ])
            ->find();

        $customerStats = Db::name('article_order')
            ->where('merchant_id', $merchant['id'])
            ->where('pay_status', 1)
            ->field([
                'COUNT(DISTINCT user_id) as customer_count',
                'COUNT(DISTINCT CASE WHEN pay_time >= ' . $todayStart . ' THEN user_id END) as new_customers_today'
            ])
            ->find();

        return $this->data([
            'fans_count' => $stats['fans_count'] ?? 0,
            'customer_count' => $customerStats['customer_count'] ?? 0,
            'new_fans_today' => $stats['new_fans_today'] ?? 0,
            'new_customers_today' => $customerStats['new_customers_today'] ?? 0,
        ]);
    }
}
