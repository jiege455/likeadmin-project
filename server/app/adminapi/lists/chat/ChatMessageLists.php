<?php
/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * 聊天消息列表
 */
namespace app\adminapi\lists\chat;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\model\chat\ChatMessage;
use app\common\lists\ListsSearchInterface;

class ChatMessageLists extends BaseAdminDataLists implements ListsSearchInterface
{
    public function setSearch(): array
    {
        return [
            '=' => ['room_id', 'user_id', 'msg_type'],
            '%like%' => ['nickname', 'content'],
        ];
    }

    public function lists(): array
    {
        $lists = ChatMessage::where($this->searchWhere)
            ->where('is_deleted', 0)
            ->field('id, room_id, user_id, nickname, avatar, content, msg_type, create_time')
            ->order(['id' => 'desc'])
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();
        
        foreach ($lists as &$item) {
            $item['msg_type_text'] = $this->getMsgTypeText($item['msg_type']);
        }
        
        return $lists;
    }

    public function count(): int
    {
        return ChatMessage::where($this->searchWhere)
            ->where('is_deleted', 0)
            ->count();
    }

    private function getMsgTypeText($type): string
    {
        $types = [
            1 => '文字',
            2 => '图片',
            3 => '系统消息',
        ];
        return $types[$type] ?? '未知';
    }
}
