<?php

namespace app\api\lists\notice;

use app\api\lists\BaseApiDataLists;
use app\common\model\notice\SystemNotice;
use app\common\model\notice\NoticeRead;
use app\common\lists\ListsSearchInterface;

class NoticeLists extends BaseApiDataLists implements ListsSearchInterface
{
    public function setSearch(): array
    {
        return [];
    }

    public function lists(): array
    {
        $lists = SystemNotice::where(['is_show' => 1])
            ->field('id, title, content, type, is_top, cover, views, create_time')
            ->order(['is_top' => 'desc', 'sort' => 'desc', 'id' => 'desc'])
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        if ($this->userId) {
            $readIds = NoticeRead::getReadNoticeIds($this->userId);
            foreach ($lists as &$item) {
                $item['is_read'] = in_array($item['id'], $readIds);
            }
        } else {
            foreach ($lists as &$item) {
                $item['is_read'] = false;
            }
        }

        return $lists;
    }

    public function count(): int
    {
        return SystemNotice::where(['is_show' => 1])->count();
    }
}
