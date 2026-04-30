<?php
/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * 私聊会话模型
 */
namespace app\common\model\chat;

use app\common\model\BaseModel;
use app\common\model\user\User;
use app\common\model\merchant\Merchant;

class ChatConversation extends BaseModel
{
    protected $name = 'chat_conversation';

    public function getCreateTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }

    public function getLastMessageTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_id', 'id');
    }

    public function targetMerchant()
    {
        return $this->belongsTo(Merchant::class, 'target_id', 'id');
    }

    public static function generateConversationId(int $userId, int $targetId): string
    {
        $minId = min($userId, $targetId);
        $maxId = max($userId, $targetId);
        return "private_{$minId}_{$maxId}";
    }

    public static function getOrCreate(int $userId, int $targetId, int $targetType = 1): array
    {
        $conversationId = self::generateConversationId($userId, $targetId);
        
        $conversation = self::where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->find();
        
        $isNew = false;
        
        if (!$conversation) {
            $conversation = new self();
            $conversation->conversation_id = $conversationId;
            $conversation->user_id = $userId;
            $conversation->target_id = $targetId;
            $conversation->target_type = $targetType;
            $conversation->create_time = time();
            $conversation->save();
            $isNew = true;
        }
        
        $targetConversation = self::where('conversation_id', $conversationId)
            ->where('user_id', $targetId)
            ->find();
        
        if (!$targetConversation) {
            $targetConversation = new self();
            $targetConversation->conversation_id = $conversationId;
            $targetConversation->user_id = $targetId;
            $targetConversation->target_id = $userId;
            $targetConversation->target_type = 2;
            $targetConversation->create_time = time();
            $targetConversation->save();
        }
        
        return [
            'conversation' => $conversation,
            'is_new' => $isNew
        ];
    }

    public static function updateLastMessage(int $userId, int $targetId, string $message): void
    {
        $conversationId = self::generateConversationId($userId, $targetId);
        $now = time();
        
        self::where('conversation_id', $conversationId)
            ->where('user_id', $targetId)
            ->inc('unread_count')
            ->update([
                'last_message' => $message,
                'last_message_time' => $now,
                'update_time' => $now
            ]);
        
        self::where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->update([
                'last_message' => $message,
                'last_message_time' => $now,
                'update_time' => $now
            ]);
    }

    public static function clearUnread(int $userId, string $conversationId): void
    {
        self::where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->update([
                'unread_count' => 0,
                'update_time' => time()
            ]);
    }

    public static function getTargetInfo(int $targetId, int $targetType): array
    {
        if ($targetType == 1) {
            $merchant = Merchant::find($targetId);
            if ($merchant) {
                return [
                    'id' => $merchant->id,
                    'name' => $merchant->name ?? '商家',
                    'logo' => $merchant->logo ?? '',
                    'type' => 'merchant'
                ];
            }
        } else {
            $user = User::find($targetId);
            if ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->nickname ?? '用户',
                    'logo' => $user->avatar ?? '',
                    'type' => 'user'
                ];
            }
        }
        
        return [
            'id' => $targetId,
            'name' => '未知',
            'logo' => '',
            'type' => 'unknown'
        ];
    }
}
