<?php
/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * 聊天消息模型
 */
namespace app\common\model\chat;

use app\common\model\BaseModel;

class ChatMessage extends BaseModel
{
    protected $name = 'chat_message';

    public function getCreateTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }

    public function user()
    {
        return $this->belongsTo(\app\common\model\user\User::class, 'user_id', 'id');
    }
}
