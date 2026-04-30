<?php
/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * 聊天消息管理逻辑
 */
namespace app\adminapi\logic\chat;

use app\common\model\chat\ChatMessage;
use app\common\logic\BaseLogic;

class ChatMessageLogic extends BaseLogic
{
    public static function detail(array $params): array
    {
        $data = ChatMessage::findOrEmpty($params['id']);
        return $data->toArray();
    }

    public static function delete(array $params): bool
    {
        try {
            ChatMessage::update([
                'id' => $params['id'],
                'is_deleted' => 1,
                'update_time' => time(),
            ]);
            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    public static function clear(array $params): bool
    {
        try {
            $roomId = $params['room_id'] ?? '';
            $where = [['is_deleted', '=', 0]];
            if ($roomId) {
                $where[] = ['room_id', '=', $roomId];
            }
            ChatMessage::where($where)->update([
                'is_deleted' => 1,
                'update_time' => time(),
            ]);
            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }
}
