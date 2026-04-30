<?php
namespace app\adminapi\validate\marketing;

use app\common\validate\BaseValidate;

class CouponValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require',
        'name' => 'require|max:100',
        'money' => 'require|float|gt:0',
        'condition_money' => 'require|float|egt:0',
        'total_count' => 'require|integer|gt:0',
        'use_time_type' => 'require|in:1,2',
        'status' => 'require|in:0,1'
    ];

    protected $message = [
        'id.require' => '请选择优惠券',
        'name.require' => '请输入优惠券名称',
        'name.max' => '优惠券名称不能超过100个字符',
        'money.require' => '请输入优惠金额',
        'money.float' => '优惠金额格式错误',
        'money.gt' => '优惠金额必须大于0',
        'condition_money.require' => '请输入使用门槛',
        'condition_money.float' => '使用门槛格式错误',
        'condition_money.egt' => '使用门槛必须大于等于0',
        'total_count.require' => '请输入发放总量',
        'total_count.integer' => '发放总量必须是整数',
        'total_count.gt' => '发放总量必须大于0',
        'use_time_type.require' => '请选择使用时间类型',
        'use_time_type.in' => '使用时间类型值错误',
        'status.require' => '请选择状态',
        'status.in' => '状态值错误',
    ];

    protected $scene = [
        'add' => ['name', 'money', 'condition_money', 'total_count', 'use_time_type', 'status'],
        'edit' => ['id', 'name', 'money', 'condition_money', 'total_count', 'use_time_type', 'status'],
        'del' => ['id']
    ];
}
