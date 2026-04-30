<?php
namespace app\adminapi\lists\merchant;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsSearchInterface;
use app\common\model\merchant\MerchantApply;

/**
 * 商家入驻申请列表
 * Class ApplyLists
 * @package app\adminapi\lists\merchant
 */
class ApplyLists extends BaseAdminDataLists implements ListsSearchInterface
{
    public function setSearch(): array
    {
        return [
            '%like%' => ['name', 'mobile'],
            '=' => ['status']
        ];
    }

    public function lists(): array
    {
        return MerchantApply::with(['user'])
            ->where($this->searchWhere)
            ->limit($this->limitOffset, $this->limitLength)
            ->order('id', 'desc')
            ->select()
            ->toArray();
    }

    public function count(): int
    {
        return MerchantApply::where($this->searchWhere)->count();
    }
}
