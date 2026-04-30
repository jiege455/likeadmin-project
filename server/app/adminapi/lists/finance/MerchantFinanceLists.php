<?php
namespace app\adminapi\lists\finance;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsSearchInterface;
use app\common\model\finance\MerchantIncomeLog;

/**
 * 商户资金记录列表
 */
class MerchantFinanceLists extends BaseAdminDataLists implements ListsSearchInterface
{
    public function setSearch(): array
    {
        return [
            '=' => ['merchant_id', 'source_type']
        ];
    }

    public function lists(): array
    {
        return MerchantIncomeLog::with(['merchant'])
            ->where($this->searchWhere)
            ->limit($this->limitOffset, $this->limitLength)
            ->order('id', 'desc')
            ->select()
            ->toArray();
    }

    public function count(): int
    {
        return MerchantIncomeLog::where($this->searchWhere)->count();
    }
}
