<?php
/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * 聊天消息验证器
 */
namespace app\adminapi\validate\chat;

use app\common\validate\BaseValidate;

class ChatMessageValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|integer',
        'room_id' => 'max:50',
    ];

    protected $message = [
        'id.require' => 'ID不能为空',
        'id.integer' => 'ID必须是整数',
        'room_id.max' => '聊天室ID最多50个字符',
    ];

    protected $scene = [
        'detail' => ['id'],
        'delete' => ['id'],
    ];
}
