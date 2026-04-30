<?php
/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * 聊天室列表
 */
namespace app\adminapi\lists\chat;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\model\chat\ChatRoom;
use app\common\lists\ListsSearchInterface;

class ChatRoomLists extends BaseAdminDataLists implements ListsSearchInterface
{
    public function setSearch(): array
    {
        return [
            '%like%' => ['name', 'room_id'],
            '=' => ['status', 'is_public'],
        ];
    }

    public function lists(): array
    {
        $lists = ChatRoom::where($this->searchWhere)
            ->field('id, name, room_id, description, max_users, is_public, status, create_time')
            ->order(['id' => 'desc'])
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();
        
        return $lists;
    }

    public function count(): int
    {
        return ChatRoom::where($this->searchWhere)->count();
    }
}
