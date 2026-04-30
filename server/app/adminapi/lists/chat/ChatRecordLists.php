<?php
/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * 聊天记录列表（合并消息管理功能）
 */
namespace app\adminapi\lists\chat;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\model\chat\ChatMessage;
use app\common\lists\ListsSearchInterface;

class ChatRecordLists extends BaseAdminDataLists implements ListsSearchInterface
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
        $query = ChatMessage::where($this->searchWhere)
            ->where('is_deleted', 0);
        
        $roomType = $this->params['room_type'] ?? '';
        if ($roomType === 'public') {
            $query->where('room_id', 'public');
        } elseif ($roomType === 'private') {
            $query->where('room_id', '<>', 'public');
            $query->where('room_id', 'like', 'private_%');
        }
        
        $lists = $query->field('id, room_id, user_id, nickname, avatar, content, msg_type, create_time')
            ->order(['id' => 'desc'])
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();
        
        foreach ($lists as &$item) {
            $item['msg_type_text'] = $this->getMsgTypeText($item['msg_type']);
            $item['room_type'] = $this->getRoomType($item['room_id']);
        }
        
        return $lists;
    }

    public function count(): int
    {
        $query = ChatMessage::where($this->searchWhere)
            ->where('is_deleted', 0);
        
        $roomType = $this->params['room_type'] ?? '';
        if ($roomType === 'public') {
            $query->where('room_id', 'public');
        } elseif ($roomType === 'private') {
            $query->where('room_id', '<>', 'public');
            $query->where('room_id', 'like', 'private_%');
        }
        
        return $query->count();
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

    private function getRoomType($roomId): string
    {
        if ($roomId === 'public') {
            return '公共聊天室';
        }
        if (str_starts_with($roomId, 'private_')) {
            return '私聊';
        }
        return '其他';
    }
}