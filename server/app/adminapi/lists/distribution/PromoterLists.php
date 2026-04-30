<?php
namespace app\adminapi\lists\distribution;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsSearchInterface;
use app\common\model\user\User;

/**
 * 推广员列表
 */
class PromoterLists extends BaseAdminDataLists implements ListsSearchInterface
{
    public function setSearch(): array
    {
        return [
            '%like%' => ['sn', 'nickname', 'mobile']
        ];
    }

    public function lists(): array
    {
        // 假设 total_commission > 0 的是推广员
        return User::where('total_commission', '>', 0)
            ->where($this->searchWhere)
            ->limit($this->limitOffset, $this->limitLength)
            ->order('total_commission', 'desc')
            ->select()
            ->toArray();
    }

    public function count(): int
    {
        return User::where('total_commission', '>', 0)
            ->where($this->searchWhere)
            ->count();
    }
}
