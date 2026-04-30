<?php
/**
 * 订单验证器
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
namespace app\api\validate;

use think\Validate;

class OrderValidate extends Validate
{
    protected $rule = [
        'id' => 'require|integer|gt:0',
        'type' => 'in:0,1,2,3',
        'page_no' => 'integer|gt:0',
        'page_size' => 'integer|between:1,100',
    ];

    protected $message = [
        'id.require' => '订单ID不能为空',
        'id.integer' => '订单ID必须是整数',
        'id.gt' => '订单ID格式错误',
        'type.in' => '订单类型错误',
        'page_no.integer' => '页码必须是整数',
        'page_no.gt' => '页码必须大于0',
        'page_size.integer' => '每页数量必须是整数',
        'page_size.between' => '每页数量必须在1-100之间',
    ];

    public function sceneLists()
    {
        return $this->only(['type', 'page_no', 'page_size']);
    }

    public function sceneDetail()
    {
        return $this->only(['id']);
    }

    public function sceneCancel()
    {
        return $this->only(['id']);
    }

    public function sceneDel()
    {
        return $this->only(['id']);
    }

    public function sceneConfirm()
    {
        return $this->only(['id']);
    }
}
