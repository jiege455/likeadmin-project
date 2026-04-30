<?php
/**
 * 待处理审批列表类
 * 开发者：杰哥网络科技 qq2711793818 杰哥
 */

namespace app\adminapi\lists;

use app\common\lists\BaseDataLists;
use think\facade\Db;

class PendingApprovalLists extends BaseDataLists
{

    /**
     * @notes 获取列表
     * @return array
     */
    public function lists(): array
    {
        $type = $this->params['type'] ?? '';
        $orderBy = $this->params['order_by'] ?? 'create_time';
        $orderDir = $this->params['order_dir'] ?? 'desc';
        
        if (!in_array(strtolower($orderDir), ['desc', 'asc'])) {
            $orderDir = 'desc';
        }

        $lists = [];

        if (empty($type) || $type == 'all') {
            $lists = $this->getAllPendingList($this->limitOffset, $this->limitLength, $orderBy, $orderDir);
        } else {
            $lists = $this->getTypePendingList($type, $this->limitOffset, $this->limitLength, $orderBy, $orderDir);
        }

        return $lists;
    }

    /**
     * @notes 获取总数
     * @return int
     */
    public function count(): int
    {
        $type = $this->params['type'] ?? '';

        if (empty($type) || $type == 'all') {
            $count = 0;
            $count += Db::name('distribution_apply')->where('status', 0)->whereNull('delete_time')->count();
            $count += Db::name('merchant_apply')->where('status', 0)->whereNull('delete_time')->count();
            $count += Db::name('article')->where('audit_status', 0)->whereNull('delete_time')->count();
            $count += Db::name('withdraw_apply')->where('status', 0)->whereNull('delete_time')->count();
            return $count;
        }

        return $this->getTypeCount($type);
    }

    /**
     * @notes 获取所有待处理列表
     */
    private function getAllPendingList($offset, $limit, $orderBy, $orderDir): array
    {
        $allItems = [];

        $distributionApplies = Db::name('distribution_apply')
            ->alias('da')
            ->leftJoin('user u', 'da.user_id = u.id')
            ->field('da.id, da.user_id, da.name, da.mobile, da.reason, da.create_time, da.status, u.nickname, u.avatar')
            ->where('da.status', 0)
            ->whereNull('da.delete_time')
            ->select()
            ->toArray();

        foreach ($distributionApplies as $item) {
            $allItems[] = [
                'id' => $item['id'],
                'type' => 'distribution_apply',
                'type_name' => '分销员申请',
                'applicant_name' => $item['name'] ?: $item['nickname'],
                'applicant_avatar' => $item['avatar'],
                'applicant_mobile' => $item['mobile'],
                'summary' => $item['reason'] ?: '申请成为分销员',
                'create_time' => $item['create_time'],
                'create_time_text' => date('Y-m-d H:i:s', $item['create_time']),
                'status' => $item['status'],
                'detail_url' => '/distribution/apply'
            ];
        }

        $merchantApplies = Db::name('merchant_apply')
            ->alias('ma')
            ->leftJoin('user u', 'ma.user_id = u.id')
            ->field('ma.id, ma.user_id, ma.name, ma.mobile, ma.desc, ma.create_time, ma.status, u.nickname, u.avatar')
            ->where('ma.status', 0)
            ->whereNull('ma.delete_time')
            ->select()
            ->toArray();

        foreach ($merchantApplies as $item) {
            $allItems[] = [
                'id' => $item['id'],
                'type' => 'merchant_apply',
                'type_name' => '商家入驻申请',
                'applicant_name' => $item['name'] ?: $item['nickname'],
                'applicant_avatar' => $item['avatar'],
                'applicant_mobile' => $item['mobile'],
                'summary' => $item['desc'] ?: '申请入驻成为商家',
                'create_time' => $item['create_time'],
                'create_time_text' => date('Y-m-d H:i:s', $item['create_time']),
                'status' => $item['status'],
                'detail_url' => '/merchant/apply'
            ];
        }

        $articles = Db::name('article')
            ->alias('a')
            ->leftJoin('merchant m', 'a.merchant_id = m.id')
            ->field('a.id, a.title, a.create_time, a.audit_status, m.name as merchant_name')
            ->where('a.audit_status', 0)
            ->whereNull('a.delete_time')
            ->select()
            ->toArray();

        foreach ($articles as $item) {
            $allItems[] = [
                'id' => $item['id'],
                'type' => 'article_audit',
                'type_name' => '文章审核',
                'applicant_name' => $item['merchant_name'] ?: '未知商家',
                'applicant_avatar' => '',
                'applicant_mobile' => '',
                'summary' => $item['title'],
                'create_time' => $item['create_time'],
                'create_time_text' => date('Y-m-d H:i:s', $item['create_time']),
                'status' => $item['audit_status'],
                'detail_url' => '/article/audit'
            ];
        }

        $withdraws = Db::name('withdraw_apply')
            ->alias('wa')
            ->leftJoin('user u', 'wa.user_id = u.id')
            ->leftJoin('merchant m', 'wa.merchant_id = m.id')
            ->field('wa.id, wa.user_id, wa.merchant_id, wa.money, wa.source, wa.create_time, wa.status, u.nickname, u.avatar, m.name as merchant_name')
            ->where('wa.status', 0)
            ->whereNull('wa.delete_time')
            ->select()
            ->toArray();

        foreach ($withdraws as $item) {
            $allItems[] = [
                'id' => $item['id'],
                'type' => 'withdraw_apply',
                'type_name' => '提现申请',
                'applicant_name' => $item['source'] == 1 ? $item['merchant_name'] : $item['nickname'],
                'applicant_avatar' => $item['avatar'],
                'applicant_mobile' => '',
                'summary' => '提现金额：¥' . number_format($item['money'], 2),
                'create_time' => $item['create_time'],
                'create_time_text' => date('Y-m-d H:i:s', $item['create_time']),
                'status' => $item['status'],
                'detail_url' => '/finance/merchant_withdraw',
                'extra' => [
                    'money' => $item['money'],
                    'source' => $item['source']
                ]
            ];
        }

        usort($allItems, function ($a, $b) use ($orderBy, $orderDir) {
            $valueA = $a[$orderBy] ?? 0;
            $valueB = $b[$orderBy] ?? 0;
            if ($orderDir === 'asc') {
                return $valueA <=> $valueB;
            }
            return $valueB <=> $valueA;
        });

        return array_slice($allItems, $offset, $limit);
    }

    /**
     * @notes 获取指定类型待处理列表
     */
    private function getTypePendingList($type, $offset, $limit, $orderBy, $orderDir): array
    {
        switch ($type) {
            case 'distribution_apply':
                return $this->getDistributionApplyList($offset, $limit);
            case 'merchant_apply':
                return $this->getMerchantApplyList($offset, $limit);
            case 'article_audit':
                return $this->getArticleAuditList($offset, $limit);
            case 'withdraw_apply':
                return $this->getWithdrawApplyList($offset, $limit);
            default:
                return [];
        }
    }

    private function getTypeCount($type): int
    {
        switch ($type) {
            case 'distribution_apply':
                return Db::name('distribution_apply')->where('status', 0)->whereNull('delete_time')->count();
            case 'merchant_apply':
                return Db::name('merchant_apply')->where('status', 0)->whereNull('delete_time')->count();
            case 'article_audit':
                return Db::name('article')->where('audit_status', 0)->whereNull('delete_time')->count();
            case 'withdraw_apply':
                return Db::name('withdraw_apply')->where('status', 0)->whereNull('delete_time')->count();
            default:
                return 0;
        }
    }

    private function getDistributionApplyList($offset, $limit): array
    {
        $items = Db::name('distribution_apply')
            ->alias('da')
            ->leftJoin('user u', 'da.user_id = u.id')
            ->field('da.id, da.user_id, da.name, da.mobile, da.reason, da.create_time, da.status, u.nickname, u.avatar')
            ->where('da.status', 0)
            ->whereNull('da.delete_time')
            ->order('da.create_time', 'desc')
            ->limit($offset, $limit)
            ->select()
            ->toArray();

        $result = [];
        foreach ($items as $item) {
            $result[] = [
                'id' => $item['id'],
                'type' => 'distribution_apply',
                'type_name' => '分销员申请',
                'applicant_name' => $item['name'] ?: $item['nickname'],
                'applicant_avatar' => $item['avatar'],
                'applicant_mobile' => $item['mobile'],
                'summary' => $item['reason'] ?: '申请成为分销员',
                'create_time' => $item['create_time'],
                'create_time_text' => date('Y-m-d H:i:s', $item['create_time']),
                'status' => $item['status'],
                'detail_url' => '/distribution/apply'
            ];
        }

        return $result;
    }

    private function getMerchantApplyList($offset, $limit): array
    {
        $items = Db::name('merchant_apply')
            ->alias('ma')
            ->leftJoin('user u', 'ma.user_id = u.id')
            ->field('ma.id, ma.user_id, ma.name, ma.mobile, ma.desc, ma.create_time, ma.status, u.nickname, u.avatar')
            ->where('ma.status', 0)
            ->whereNull('ma.delete_time')
            ->order('ma.create_time', 'desc')
            ->limit($offset, $limit)
            ->select()
            ->toArray();

        $result = [];
        foreach ($items as $item) {
            $result[] = [
                'id' => $item['id'],
                'type' => 'merchant_apply',
                'type_name' => '商家入驻申请',
                'applicant_name' => $item['name'] ?: $item['nickname'],
                'applicant_avatar' => $item['avatar'],
                'applicant_mobile' => $item['mobile'],
                'summary' => $item['desc'] ?: '申请入驻成为商家',
                'create_time' => $item['create_time'],
                'create_time_text' => date('Y-m-d H:i:s', $item['create_time']),
                'status' => $item['status'],
                'detail_url' => '/merchant/apply'
            ];
        }

        return $result;
    }

    private function getArticleAuditList($offset, $limit): array
    {
        $items = Db::name('article')
            ->alias('a')
            ->leftJoin('merchant m', 'a.merchant_id = m.id')
            ->field('a.id, a.title, a.create_time, a.audit_status, m.name as merchant_name')
            ->where('a.audit_status', 0)
            ->whereNull('a.delete_time')
            ->order('a.create_time', 'desc')
            ->limit($offset, $limit)
            ->select()
            ->toArray();

        $result = [];
        foreach ($items as $item) {
            $result[] = [
                'id' => $item['id'],
                'type' => 'article_audit',
                'type_name' => '文章审核',
                'applicant_name' => $item['merchant_name'] ?: '未知商家',
                'applicant_avatar' => '',
                'applicant_mobile' => '',
                'summary' => $item['title'],
                'create_time' => $item['create_time'],
                'create_time_text' => date('Y-m-d H:i:s', $item['create_time']),
                'status' => $item['audit_status'],
                'detail_url' => '/article/audit'
            ];
        }

        return $result;
    }

    private function getWithdrawApplyList($offset, $limit): array
    {
        $items = Db::name('withdraw_apply')
            ->alias('wa')
            ->leftJoin('user u', 'wa.user_id = u.id')
            ->leftJoin('merchant m', 'wa.merchant_id = m.id')
            ->field('wa.id, wa.user_id, wa.merchant_id, wa.money, wa.source, wa.create_time, wa.status, u.nickname, u.avatar, m.name as merchant_name')
            ->where('wa.status', 0)
            ->whereNull('wa.delete_time')
            ->order('wa.create_time', 'desc')
            ->limit($offset, $limit)
            ->select()
            ->toArray();

        $result = [];
        foreach ($items as $item) {
            $result[] = [
                'id' => $item['id'],
                'type' => 'withdraw_apply',
                'type_name' => '提现申请',
                'applicant_name' => $item['source'] == 1 ? $item['merchant_name'] : $item['nickname'],
                'applicant_avatar' => $item['avatar'],
                'applicant_mobile' => '',
                'summary' => '提现金额：¥' . number_format($item['money'], 2),
                'create_time' => $item['create_time'],
                'create_time_text' => date('Y-m-d H:i:s', $item['create_time']),
                'status' => $item['status'],
                'detail_url' => '/finance/merchant_withdraw',
                'extra' => [
                    'money' => $item['money'],
                    'source' => $item['source']
                ]
            ];
        }

        return $result;
    }
}
