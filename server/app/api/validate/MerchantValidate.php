<?php
/**
 * 商家验证器
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
namespace app\api\validate;

use think\Validate;

class MerchantValidate extends Validate
{
    protected $rule = [
        'id' => 'require|integer|gt:0',
        'merchant_id' => 'require|integer|gt:0',
        'page_no' => 'integer|gt:0',
        'page_size' => 'integer|between:1,100',
        'name' => 'require',
        'image' => 'require',
        'desc' => '',
        'remarks' => '',
    ];

    protected $message = [
        'id.require' => '商家ID不能为空',
        'id.integer' => '商家ID必须是整数',
        'id.gt' => '商家ID格式错误',
        'merchant_id.require' => '商家ID不能为空',
        'merchant_id.integer' => '商家ID必须是整数',
        'merchant_id.gt' => '商家ID格式错误',
        'page_no.integer' => '页码必须是整数',
        'page_no.gt' => '页码必须大于0',
        'page_size.integer' => '每页数量必须是整数',
        'page_size.between' => '每页数量必须在1-100之间',
        'name.require' => '商家名称不能为空',
        'image.require' => '商家头像不能为空',
    ];

    public function sceneFollow()
    {
        return $this->only(['merchant_id']);
    }

    public function sceneInfo()
    {
        return $this->only(['id']);
    }

    public function sceneLists()
    {
        return $this->only(['page_no', 'page_size']);
    }
}
