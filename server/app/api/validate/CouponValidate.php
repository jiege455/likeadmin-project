<?php
/**
 * 优惠券验证器
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
namespace app\api\validate;

use think\Validate;

class CouponValidate extends Validate
{
    protected $rule = [
        'id' => 'require|integer|gt:0',
        'coupon_id' => 'require|integer|gt:0',
        'merchant_id' => 'integer|gt:0',
        'page_no' => 'integer|gt:0',
        'page_size' => 'integer|between:1,100',
    ];

    protected $message = [
        'id.require' => '优惠券ID不能为空',
        'id.integer' => '优惠券ID必须是整数',
        'id.gt' => '优惠券ID格式错误',
        'coupon_id.require' => '优惠券ID不能为空',
        'coupon_id.integer' => '优惠券ID必须是整数',
        'coupon_id.gt' => '优惠券ID格式错误',
        'merchant_id.integer' => '商家ID必须是整数',
        'merchant_id.gt' => '商家ID格式错误',
        'page_no.integer' => '页码必须是整数',
        'page_no.gt' => '页码必须大于0',
        'page_size.integer' => '每页数量必须是整数',
        'page_size.between' => '每页数量必须在1-100之间',
    ];

    public function sceneLists()
    {
        return $this->only(['page_no', 'page_size']);
    }

    public function sceneMerchantList()
    {
        return $this->only(['merchant_id', 'page_no', 'page_size']);
    }

    public function sceneMyList()
    {
        return $this->only(['page_no', 'page_size']);
    }

    public function sceneAvailable()
    {
        return $this->only(['page_no', 'page_size']);
    }

    public function sceneReceive()
    {
        return $this->only(['coupon_id']);
    }

    public function sceneGet()
    {
        return $this->only(['id']);
    }
}
