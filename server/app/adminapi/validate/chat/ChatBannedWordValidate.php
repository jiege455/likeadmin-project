<?php
/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * 违禁词验证器
 */
namespace app\adminapi\validate\chat;

use app\common\validate\BaseValidate;

class ChatBannedWordValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|integer',
        'word' => 'require|max:100',
        'type' => 'in:1,2',
        'replace_word' => 'max:100',
        'status' => 'in:0,1',
    ];

    protected $message = [
        'id.require' => 'ID不能为空',
        'id.integer' => 'ID必须是整数',
        'word.require' => '违禁词不能为空',
        'word.max' => '违禁词最多100个字符',
        'type.in' => '类型值不正确',
        'replace_word.max' => '替换词最多100个字符',
        'status.in' => '状态值不正确',
    ];

    protected $scene = [
        'add' => ['word', 'type', 'replace_word', 'status'],
        'edit' => ['id', 'word', 'type', 'replace_word'],
        'delete' => ['id'],
        'status' => ['id', 'status'],
    ];
}
