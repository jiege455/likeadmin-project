<?php
/**
 * 文章订单列表
 * 开发者：杰哥网络科技 qq2711793818 杰哥
 * Class ArticleOrderLists
 * @package app\adminapi\lists\article
 */
namespace app\adminapi\lists\article;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsSearchInterface;
use app\common\lists\ListsSortInterface;
use app\common\model\article\ArticleOrder;

class ArticleOrderLists extends BaseAdminDataLists implements ListsSearchInterface, ListsSortInterface
{
    public function setSearch(): array
    {
        return [
            '=' => ['ao.pay_status', 'ao.refund_status'],
            '%like%' => ['ao.order_sn']
        ];
    }

    public function setSortFields(): array
    {
        return ['create_time' => 'ao.create_time', 'id' => 'ao.id'];
    }

    public function setDefaultOrder(): array
    {
        return ['ao.create_time' => 'desc', 'ao.id' => 'desc'];
    }

    public function lists(): array
    {
        $where = $this->searchWhere;
        
        if (!empty($this->params['user_info'])) {
            $where[] = ['u.nickname|u.mobile|u.sn', 'like', '%' . $this->params['user_info'] . '%'];
        }
        
        if (!empty($this->params['start_time'])) {
            $where[] = ['ao.create_time', '>=', strtotime($this->params['start_time'])];
        }
        if (!empty($this->params['end_time'])) {
            $where[] = ['ao.create_time', '<=', strtotime($this->params['end_time']) + 86400 - 1];
        }

        $lists = ArticleOrder::alias('ao')
            ->join('user u', 'ao.user_id = u.id')
            ->join('article a', 'ao.article_id = a.id')
            ->leftJoin('merchant m', 'ao.merchant_id = m.id')
            ->field('ao.*, u.nickname, u.sn as user_sn, u.avatar, u.mobile as user_mobile, a.title as article_title, m.name as merchant_name, m.mobile as merchant_mobile')
            ->where($where)
            ->limit($this->limitOffset, $this->limitLength)
            ->order($this->sortOrder)
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['pay_status_text'] = $item['pay_status'] == 1 ? '已支付' : '待支付';
            $item['refund_status_text'] = $item['refund_status'] == 1 ? '已退款' : '未退款';
            $item['avatar'] = \app\common\service\FileService::getFileUrl($item['avatar']);
            $item['pay_time'] = $item['pay_time'] ? date('Y-m-d H:i:s', is_numeric($item['pay_time']) ? $item['pay_time'] : strtotime($item['pay_time'])) : '-';
            $item['create_time'] = date('Y-m-d H:i:s', is_numeric($item['create_time']) ? $item['create_time'] : strtotime($item['create_time']));
        }

        return $lists;
    }

    public function count(): int
    {
        $where = $this->searchWhere;
        
        if (!empty($this->params['user_info'])) {
            $where[] = ['u.nickname|u.mobile|u.sn', 'like', '%' . $this->params['user_info'] . '%'];
        }
        
        if (!empty($this->params['start_time'])) {
            $where[] = ['ao.create_time', '>=', strtotime($this->params['start_time'])];
        }
        if (!empty($this->params['end_time'])) {
            $where[] = ['ao.create_time', '<=', strtotime($this->params['end_time']) + 86400 - 1];
        }

        return ArticleOrder::alias('ao')
            ->join('user u', 'ao.user_id = u.id')
            ->join('article a', 'ao.article_id = a.id')
            ->where($where)
            ->count();
    }
}
