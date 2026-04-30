<?php
/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * 聊天室模型
 */
namespace app\common\model\chat;

use app\common\model\BaseModel;

class ChatRoom extends BaseModel
{
    protected $name = 'chat_room';

    public function getCreateTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }
}
