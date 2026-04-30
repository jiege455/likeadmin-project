<?php
/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * 私聊消息列表
 */
namespace app\api\lists\chat;

use app\api\lists\BaseApiDataLists;
use app\common\model\chat\ChatMessage;
use app\common\model\chat\ChatConversation;
use app\common\service\FileService;
use think\facade\Config;

class PrivateMessageLists extends BaseApiDataLists
{
    public function lists(): array
    {
        $conversationId = $this->request->get('conversation_id', '');
        
        if (empty($conversationId)) {
            return [];
        }

        $conversation = ChatConversation::where('conversation_id', $conversationId)
            ->where('user_id', $this->userId)
            ->find();

        if (!$conversation) {
            return [];
        }

        $messages = ChatMessage::where('room_id', $conversationId)
            ->where('is_deleted', 0)
            ->field('id, user_id, nickname, avatar, content, msg_type, create_time')
            ->order('id', 'desc')
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        $frontendUrl = Config::get('app.frontend_url', '');

        foreach ($messages as &$msg) {
            if ($msg['avatar'] && !str_starts_with($msg['avatar'], 'http')) {
                $msg['avatar'] = FileService::getFileUrl($msg['avatar']);
            }
            if (empty($msg['avatar'])) {
                $msg['avatar'] = '/static/images/user/default_avatar.png';
            }
        }

        return array_reverse($messages);
    }

    public function count(): int
    {
        $conversationId = $this->request->get('conversation_id', '');
        
        if (empty($conversationId)) {
            return 0;
        }

        $conversation = ChatConversation::where('conversation_id', $conversationId)
            ->where('user_id', $this->userId)
            ->find();

        if (!$conversation) {
            return 0;
        }

        return ChatMessage::where('room_id', $conversationId)
            ->where('is_deleted', 0)
            ->count();
    }
}
