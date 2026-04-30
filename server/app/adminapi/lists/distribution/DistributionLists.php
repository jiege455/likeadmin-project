<?php
namespace app\adminapi\lists\distribution;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsSearchInterface;
use app\common\model\distribution\DistributionLog;

/**
 * 分销记录列表
 */
class DistributionLists extends BaseAdminDataLists implements ListsSearchInterface
{
    public function setSearch(): array
    {
        return [
            '=' => ['user_id', 'status']
        ];
    }

    public function lists(): array
    {
        return DistributionLog::with(['user', 'sourceUser'])
            ->where($this->searchWhere)
            ->limit($this->limitOffset, $this->limitLength)
            ->order('id', 'desc')
            ->select()
            ->toArray();
    }

    public function count(): int
    {
        return DistributionLog::where($this->searchWhere)->count();
    }
}
