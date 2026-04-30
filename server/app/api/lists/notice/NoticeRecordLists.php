<?php
namespace app\api\lists\notice;

use app\api\lists\BaseApiDataLists;
use app\common\model\notice\NoticeRecord;
use app\common\lists\ListsSearchInterface;

class NoticeRecordLists extends BaseApiDataLists implements ListsSearchInterface
{
    public function setSearch(): array
    {
        return [];
    }

    public function lists(): array
    {
        $lists = NoticeRecord::where('user_id', $this->userId)
            ->where('notice_type', 1) // 1=业务通知
            ->field('id, title, content, create_time, read')
            ->order('id', 'desc')
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        return $lists;
    }

    public function count(): int
    {
        return NoticeRecord::where('user_id', $this->userId)
            ->where('notice_type', 1)
            ->count();
    }
}
