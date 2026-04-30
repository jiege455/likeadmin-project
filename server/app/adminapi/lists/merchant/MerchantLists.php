<?php
namespace app\adminapi\lists\merchant;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsSearchInterface;
use app\common\model\merchant\Merchant;

/**
 * 商户列表
 * Class MerchantLists
 * @package app\adminapi\lists\merchant
 */
class MerchantLists extends BaseAdminDataLists implements ListsSearchInterface
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
        return Merchant::with(['user'])
            ->where($this->searchWhere)
            ->limit($this->limitOffset, $this->limitLength)
            ->order('id', 'desc')
            ->select()
            ->toArray();
    }

    public function count(): int
    {
        return Merchant::where($this->searchWhere)->count();
    }
}
