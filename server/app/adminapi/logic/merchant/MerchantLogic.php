<?php
namespace app\adminapi\logic\merchant;

use app\common\logic\BaseLogic;
use app\common\service\FileService;
use think\facade\Db;

class MerchantLogic extends BaseLogic
{
    public static function lists($get)
    {
        $where = [];
        $where[] = ['m.delete_time', '=', null];
        
        if (!empty($get['name'])) {
            $where[] = ['m.name', 'like', '%' . $get['name'] . '%'];
        }
        if (!empty($get['status'])) {
            $where[] = ['m.status', '=', $get['status']];
        }
        if (!empty($get['mobile'])) {
            $where[] = ['m.mobile', 'like', '%' . $get['mobile'] . '%'];
        }

        $count = Db::name('merchant')->alias('m')->where($where)->count();
        $lists = Db::name('merchant')
            ->alias('m')
            ->leftJoin('user u', 'm.user_id = u.id')
            ->field('m.*, u.nickname, u.avatar')
            ->where($where)
            ->page($get['page_no'] ?? 1, $get['page_size'] ?? 15)
            ->order('m.id', 'desc')
            ->select()
            ->toArray();

        // 批量查询统计数据（修复N+1查询）
        $merchantIds = array_column($lists, 'id');
        $articleCountMap = [];
        $orderCountMap = [];
        $totalIncomeMap = [];
        
        if (!empty($merchantIds)) {
            // 批量查询文章数量
            $articleCounts = Db::name('article')
                ->whereIn('merchant_id', $merchantIds)
                ->where('delete_time', null)
                ->group('merchant_id')
                ->column('COUNT(*) as count', 'merchant_id');
            $articleCountMap = $articleCounts;

            // 批量查询订单数量和收入
            $orderStats = Db::name('article_order')
                ->whereIn('merchant_id', $merchantIds)
                ->group('merchant_id')
                ->field('merchant_id, COUNT(*) as order_count, SUM(CASE WHEN pay_status = 1 THEN order_amount ELSE 0 END) as total_income')
                ->select()
                ->toArray();
            foreach ($orderStats as $stat) {
                $orderCountMap[$stat['merchant_id']] = $stat['order_count'];
                $totalIncomeMap[$stat['merchant_id']] = $stat['total_income'];
            }
        }

        foreach ($lists as &$item) {
            $item['logo'] = $item['image'] ? FileService::getFileUrl($item['image']) : '';
            $item['avatar'] = $item['avatar'] ? FileService::getFileUrl($item['avatar']) : '';
            $item['article_count'] = $articleCountMap[$item['id']] ?? 0;
            $item['order_count'] = $orderCountMap[$item['id']] ?? 0;
            $item['total_income'] = $totalIncomeMap[$item['id']] ?? 0;
            $item['status_text'] = self::getStatusText($item['status']);
            $item['create_time'] = date('Y-m-d H:i', $item['create_time']);
        }

        return ['count' => $count, 'lists' => $lists];
    }

    public static function detail($id)
    {
        $merchant = Db::name('merchant')
            ->alias('m')
            ->leftJoin('user u', 'm.user_id = u.id')
            ->field('m.*, u.nickname, u.avatar, u.mobile as user_mobile')
            ->where('m.id', $id)
            ->find();

        if (!$merchant) return null;

        $merchant['logo'] = ($merchant['logo'] ?? $merchant['image']) ? FileService::getFileUrl($merchant['logo'] ?? $merchant['image']) : '';
        $merchant['avatar'] = $merchant['avatar'] ? FileService::getFileUrl($merchant['avatar']) : '';
        $merchant['status_text'] = self::getStatusText($merchant['status']);
        $merchant['create_time'] = date('Y-m-d H:i:s', $merchant['create_time']);
        
        if (!empty($merchant['audit_time'])) {
            $merchant['audit_time'] = date('Y-m-d H:i:s', $merchant['audit_time']);
        }

        return $merchant;
    }

    public static function statistics($id)
    {
        $articleCount = Db::name('article')->where('merchant_id', $id)->where('delete_time', null)->count();
        $orderCount = Db::name('article_order')->where('merchant_id', $id)->where('pay_status', 1)->count();
        $totalIncome = Db::name('article_order')->where('merchant_id', $id)->where('pay_status', 1)->sum('order_amount');
        $withdrawAmount = Db::name('withdraw_apply')->where('merchant_id', $id)->where('status', 2)->sum('money');
        $fansCount = Db::name('merchant_follow')->where('merchant_id', $id)->count();

        $todayStart = strtotime(date('Y-m-d'));
        $todayIncome = Db::name('article_order')
            ->where('merchant_id', $id)
            ->where('pay_status', 1)
            ->where('pay_time', '>=', $todayStart)
            ->sum('order_amount');

        return [
            'article_count' => $articleCount,
            'order_count' => $orderCount,
            'total_income' => number_format($totalIncome, 2, '.', ''),
            'today_income' => number_format($todayIncome, 2, '.', ''),
            'withdraw_amount' => number_format($withdrawAmount, 2, '.', ''),
            'balance' => number_format(Db::name('merchant')->where('id', $id)->value('money') ?? 0, 2, '.', ''),
            'fans_count' => $fansCount,
        ];
    }

    public static function articles($id, $get)
    {
        $where = [];
        $where[] = ['merchant_id', '=', $id];
        $where[] = ['delete_time', '=', null];
        
        if (!empty($get['title'])) {
            $where[] = ['title', 'like', '%' . $get['title'] . '%'];
        }

        $count = Db::name('article')->where($where)->count();
        $lists = Db::name('article')
            ->where($where)
            ->page($get['page_no'] ?? 1, $get['page_size'] ?? 10)
            ->order('id', 'desc')
            ->select()
            ->toArray();

        // 批量查询订单统计（修复N+1查询）
        $articleIds = array_column($lists, 'id');
        $buyCountMap = [];
        $incomeMap = [];
        
        if (!empty($articleIds)) {
            $orderStats = Db::name('article_order')
                ->whereIn('article_id', $articleIds)
                ->where('pay_status', 1)
                ->group('article_id')
                ->field('article_id, COUNT(*) as buy_count, SUM(order_amount) as income')
                ->select()
                ->toArray();
            foreach ($orderStats as $stat) {
                $buyCountMap[$stat['article_id']] = $stat['buy_count'];
                $incomeMap[$stat['article_id']] = $stat['income'];
            }
        }

        foreach ($lists as &$item) {
            $item['create_time'] = date('Y-m-d H:i', $item['create_time']);
            $item['buy_count'] = $buyCountMap[$item['id']] ?? 0;
            $item['income'] = $incomeMap[$item['id']] ?? 0;
        }

        return ['count' => $count, 'lists' => $lists];
    }

    public static function orders($id, $get)
    {
        $where = [];
        $where[] = ['o.merchant_id', '=', $id];
        
        if (!empty($get['order_sn'])) {
            $where[] = ['o.order_sn', 'like', '%' . $get['order_sn'] . '%'];
        }
        if (isset($get['pay_status']) && $get['pay_status'] !== '') {
            $where[] = ['o.pay_status', '=', $get['pay_status']];
        }

        $count = Db::name('article_order')->alias('o')->where($where)->count();
        $lists = Db::name('article_order')
            ->alias('o')
            ->leftJoin('user u', 'o.user_id = u.id')
            ->leftJoin('article a', 'o.article_id = a.id')
            ->field('o.*, u.nickname, u.avatar, a.title as article_title')
            ->where($where)
            ->page($get['page_no'] ?? 1, $get['page_size'] ?? 10)
            ->order('o.id', 'desc')
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['avatar'] = $item['avatar'] ? FileService::getFileUrl($item['avatar']) : '';
            $item['pay_time'] = $item['pay_time'] ? date('Y-m-d H:i:s', $item['pay_time']) : '';
            $item['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
        }

        return ['count' => $count, 'lists' => $lists];
    }

    public static function audit($params)
    {
        Db::startTrans();
        try {
            $id = $params['id'];
            $status = $params['status'];
            $reason = $params['reason'] ?? '';

            $merchant = Db::name('merchant')->find($id);
            if (!$merchant) {
                throw new \Exception('商户不存在');
            }

            $data = [
                'status' => $status,
                'audit_time' => time(),
                'audit_reason' => $reason,
                'update_time' => time()
            ];

            Db::name('merchant')->where('id', $id)->update($data);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    public static function setStatus($params)
    {
        Db::startTrans();
        try {
            $id = $params['id'];
            $status = $params['status'];

            Db::name('merchant')->where('id', $id)->update([
                'status' => $status,
                'update_time' => time()
            ]);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    public static function edit($params)
    {
        Db::startTrans();
        try {
            $id = $params['id'];
            $merchant = Db::name('merchant')->find($id);
            if (!$merchant) {
                throw new \Exception('商户不存在');
            }

            $data = [
                'update_time' => time()
            ];

            if (isset($params['name'])) {
                $data['name'] = $params['name'];
            }
            if (isset($params['mobile'])) {
                $data['mobile'] = $params['mobile'];
            }
            if (isset($params['wechat'])) {
                $data['wechat'] = $params['wechat'];
            }
            if (isset($params['intro'])) {
                $data['intro'] = $params['intro'];
            }
            if (isset($params['image'])) {
                $data['image'] = $params['image'];
            }

            Db::name('merchant')->where('id', $id)->update($data);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    private static function getStatusText($status)
    {
        $texts = [
            0 => '待审核',
            1 => '正常',
            2 => '已拒绝',
            3 => '已禁用'
        ];
        return $texts[$status] ?? '未知';
    }
}
