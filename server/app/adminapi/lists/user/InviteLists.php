<?php
namespace app\adminapi\lists\user;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsSearchInterface;
use app\common\model\user\User;

/**
 * 邀请记录列表
 */
class InviteLists extends BaseAdminDataLists implements ListsSearchInterface
{
    public function setSearch(): array
    {
        return [
            '%like%' => ['sn', 'nickname', 'mobile']
        ];
    }

    public function lists(): array
    {
        return User::with(['inviter'])
            ->where('inviter_id', '>', 0)
            ->where($this->searchWhere)
            ->limit($this->limitOffset, $this->limitLength)
            ->order('id', 'desc')
            ->select()
            ->toArray();
    }

    public function count(): int
    {
        return User::where('inviter_id', '>', 0)
            ->where($this->searchWhere)
            ->count();
    }
}
