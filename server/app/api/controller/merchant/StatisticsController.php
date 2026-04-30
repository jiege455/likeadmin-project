<?php
namespace app\api\controller\merchant;

use app\api\controller\BaseApiController;
use think\facade\Db;

/**
 * 商家统计控制器
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 */
class StatisticsController extends BaseApiController
{
    public function index()
    {
        $merchant = Db::name('merchant')->where('user_id', $this->userId)->find();
        if (!$merchant) {
            return $this->fail('您还不是商户');
        }

        $todayStart = strtotime(date('Y-m-d'));
        $merchantId = $merchant['id'];

        // 使用单个查询获取所有统计数据，减少数据库连接次数
        $stats = Db::name('article')
            ->where('merchant_id', $merchantId)
            ->where('delete_time', null)
            ->field([
                'COUNT(*) as article_count',
                'SUM(click_actual) as total_click_actual',
                'SUM(click_virtual) as total_click_virtual'
            ])
            ->find();

        $articleCount = $stats['article_count'] ?? 0;
        $todayViews = intval($stats['total_click_actual'] ?? 0) + intval($stats['total_click_virtual'] ?? 0);

        // 订单统计
        $orderStats = Db::name('article_order')
            ->where('merchant_id', $merchantId)
            ->where('pay_status', 1)
            ->field([
                'COUNT(*) as order_count',
                'SUM(order_amount) as total_income',
                'SUM(CASE WHEN pay_time >= ' . $todayStart . ' THEN order_amount ELSE 0 END) as today_income'
            ])
            ->find();

        $orderCount = $orderStats['order_count'] ?? 0;
        $totalIncome = $orderStats['total_income'] ?? 0;
        $todayIncome = $orderStats['today_income'] ?? 0;

        $fansCount = Db::name('merchant_follow')->where('merchant_id', $merchantId)->count();

        // 优化：使用单个查询获取本周收入数据
        $weekStart = strtotime('-6 days', $todayStart);
        $weekData = Db::name('article_order')
            ->where('merchant_id', $merchantId)
            ->where('pay_status', 1)
            ->where('pay_time', '>=', $weekStart)
            ->field([
                'DATE(FROM_UNIXTIME(pay_time)) as date',
                'SUM(order_amount) as income'
            ])
            ->group('DATE(FROM_UNIXTIME(pay_time))')
            ->column('income', 'date');

        $weekIncome = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days", $todayStart));
            $weekIncome[] = [
                'date' => date('m-d', strtotime($date)),
                'income' => floatval($weekData[$date] ?? 0)
            ];
        }

        return $this->data([
            'article_count' => $articleCount,
            'order_count' => $orderCount,
            'fans_count' => $fansCount,
            'balance' => number_format($merchant['money'], 2, '.', ''),
            'total_income' => number_format($totalIncome, 2, '.', ''),
            'today_income' => number_format($todayIncome, 2, '.', ''),
            'today_views' => $todayViews,
            'week_income' => $weekIncome,
        ]);
    }

    public function articles()
    {
        $merchant = Db::name('merchant')->where('user_id', $this->userId)->find();
        if (!$merchant) {
            return $this->fail('您还不是商户');
        }

        $articles = Db::name('article')
            ->where('merchant_id', $merchant['id'])
            ->where('delete_time', null)
            ->field('id, title, image, price, click_actual, click_virtual, create_time')
            ->order('id', 'desc')
            ->limit(10)
            ->select()
            ->toArray();

        if (empty($articles)) {
            return $this->data(['lists' => []]);
        }

        // 优化：使用单个查询获取所有文章的订单统计
        $articleIds = array_column($articles, 'id');
        $orderStats = Db::name('article_order')
            ->where('article_id', 'in', $articleIds)
            ->where('pay_status', 1)
            ->field([
                'article_id',
                'COUNT(*) as buy_count',
                'SUM(order_amount) as income'
            ])
            ->group('article_id')
            ->column(null, 'article_id');

        foreach ($articles as &$item) {
            $item['views'] = intval($item['click_actual']) + intval($item['click_virtual']);
            $stats = $orderStats[$item['id']] ?? ['buy_count' => 0, 'income' => 0];
            $item['buy_count'] = $stats['buy_count'];
            $item['income'] = $stats['income'];
            $item['create_time'] = date('Y-m-d', $item['create_time']);
        }

        return $this->data(['lists' => $articles]);
    }
}
