<?php
/**
 * 提现账户验证器
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
namespace app\api\validate;

use think\Validate;

class WithdrawAccountValidate extends Validate
{
    protected $rule = [
        'id' => 'require|integer|gt:0',
        'type' => 'require|in:1,2,3',
        'account' => 'require|max:100',
        'real_name' => 'require|max:50',
        'bank_name' => 'max:50',
        'bank_branch' => 'max:100',
    ];

    protected $message = [
        'id.require' => '账户ID不能为空',
        'id.integer' => '账户ID必须是整数',
        'id.gt' => '账户ID格式错误',
        'type.require' => '账户类型不能为空',
        'type.in' => '账户类型错误',
        'account.require' => '账号不能为空',
        'account.max' => '账号最多100个字符',
        'real_name.require' => '真实姓名不能为空',
        'real_name.max' => '真实姓名最多50个字符',
        'bank_name.max' => '银行名称最多50个字符',
        'bank_branch.max' => '开户行最多100个字符',
    ];

    public function sceneAdd()
    {
        return $this->only(['type', 'account', 'real_name', 'bank_name', 'bank_branch']);
    }

    public function sceneEdit()
    {
        return $this->only(['id', 'type', 'account', 'real_name', 'bank_name', 'bank_branch']);
    }

    public function sceneDelete()
    {
        return $this->only(['id']);
    }

    public function sceneSetDefault()
    {
        return $this->only(['id']);
    }

    public function sceneDetail()
    {
        return $this->only(['id']);
    }
}
