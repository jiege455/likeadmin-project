<?php
/**
 * 商户提现审核验证器
 * 开发者公众号：杰哥网络科技
 * qq2711793818 杰哥
 */
namespace app\adminapi\validate\finance;

use app\common\validate\BaseValidate;

class MerchantWithdrawValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|integer',
        'status' => 'require|in:1,2,3',
        'remark' => 'max:255',
    ];

    protected $message = [
        'id.require' => '请选择提现记录',
        'id.integer' => 'ID必须是整数',
        'status.require' => '请选择审核状态',
        'status.in' => '审核状态值错误',
        'remark.max' => '备注不能超过255个字符',
    ];

    protected $scene = [
        'audit' => ['id', 'status', 'remark'],
        'detail' => ['id'],
    ];
}
