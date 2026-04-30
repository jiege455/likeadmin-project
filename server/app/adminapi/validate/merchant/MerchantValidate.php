<?php
namespace app\adminapi\validate\merchant;

use app\common\validate\BaseValidate;

class MerchantValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|integer',
        'status' => 'require|in:0,1,2,3',
        'reason' => 'max:255',
    ];

    protected $message = [
        'id.require' => '商户ID不能为空',
        'id.integer' => '商户ID必须是整数',
        'status.require' => '状态不能为空',
        'status.in' => '状态值不正确',
        'reason.max' => '原因不能超过255个字符',
    ];

    public function sceneAudit()
    {
        return $this->only(['id', 'status', 'reason']);
    }

    public function sceneStatus()
    {
        return $this->only(['id', 'status']);
    }
}
