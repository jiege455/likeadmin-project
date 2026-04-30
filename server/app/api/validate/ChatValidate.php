<?php
/**
 * 聊天验证器
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
namespace app\api\validate;

use think\Validate;

class ChatValidate extends Validate
{
    protected $rule = [
        'room_id' => 'require|integer|gt:0',
        'content' => 'require',
        'msg_type' => 'in:text,image,voice,video',
        'to_user_id' => 'integer|gt:0',
        'page_no' => 'integer|gt:0',
        'page_size' => 'integer|between:1,100',
    ];

    protected $message = [
        'room_id.require' => '房间ID不能为空',
        'room_id.integer' => '房间ID必须是整数',
        'room_id.gt' => '房间ID格式错误',
        'content.require' => '消息内容不能为空',
        'msg_type.in' => '消息类型错误',
        'to_user_id.integer' => '接收用户ID必须是整数',
        'to_user_id.gt' => '接收用户ID格式错误',
        'page_no.integer' => '页码必须是整数',
        'page_no.gt' => '页码必须大于0',
        'page_size.integer' => '每页数量必须是整数',
        'page_size.between' => '每页数量必须在1-100之间',
    ];

    public function sceneLists()
    {
        return $this->only(['room_id', 'page_no', 'page_size']);
    }

    public function sceneSend()
    {
        return $this->only(['room_id', 'content', 'msg_type', 'to_user_id']);
    }
}
