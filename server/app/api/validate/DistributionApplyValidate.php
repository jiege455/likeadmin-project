<?php
namespace app\api\validate;

use app\common\validate\BaseValidate;

class DistributionApplyValidate extends BaseValidate
{
    protected $rule = [
        'name' => 'require|max:32',
        'mobile' => 'require|mobile',
        'reason' => 'max:255'
    ];

    protected $message = [
        'name.require' => '请输入真实姓名',
        'name.max' => '姓名不能超过32个字符',
        'mobile.require' => '请输入手机号码',
        'mobile.mobile' => '手机号码格式不正确',
        'reason.max' => '申请理由不能超过255个字符'
    ];
}
