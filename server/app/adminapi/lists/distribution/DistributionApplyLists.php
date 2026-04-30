<?php
namespace app\adminapi\lists\distribution;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsSearchInterface;
use app\common\model\distribution\DistributionApply;

class DistributionApplyLists extends BaseAdminDataLists implements ListsSearchInterface
{
    /**
     * @notes 设置搜索条件
     * @return array
     */
    public function setSearch(): array
    {
        return [
            '=' => ['status'],
            'like' => ['name', 'mobile']
        ];
    }

    /**
     * @notes 获取列表
     * @return array
     */
    public function lists(): array
    {
        return DistributionApply::with(['user'])
            ->where($this->searchWhere)
            ->order('id', 'desc')
            ->page($this->pageNo, $this->pageSize)
            ->select()
            ->toArray();
    }

    /**
     * @notes 获取数量
     * @return int
     */
    public function count(): int
    {
        return DistributionApply::where($this->searchWhere)->count();
    }
}
