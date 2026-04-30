<?php
/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * 聊天室验证器
 */
namespace app\adminapi\validate\chat;

use app\common\validate\BaseValidate;

class ChatRoomValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|integer',
        'name' => 'require|max:100',
        'room_id' => 'require|max:50',
        'description' => 'max:255',
        'max_users' => 'integer|gt:0',
        'is_public' => 'in:0,1',
        'status' => 'in:0,1',
    ];

    protected $message = [
        'id.require' => 'ID不能为空',
        'id.integer' => 'ID必须是整数',
        'name.require' => '聊天室名称不能为空',
        'name.max' => '聊天室名称最多100个字符',
        'room_id.require' => '聊天室ID不能为空',
        'room_id.max' => '聊天室ID最多50个字符',
        'description.max' => '描述最多255个字符',
        'max_users.integer' => '最大用户数必须是整数',
        'max_users.gt' => '最大用户数必须大于0',
        'is_public.in' => '公开状态值不正确',
        'status.in' => '状态值不正确',
    ];

    protected $scene = [
        'add' => ['name', 'room_id', 'description', 'max_users', 'is_public', 'status'],
        'edit' => ['id', 'name', 'description', 'max_users', 'is_public'],
        'detail' => ['id'],
        'delete' => ['id'],
        'status' => ['id', 'status'],
    ];
}
