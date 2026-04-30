<?php
/**
 * 推送关键词验证器
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
namespace app\api\validate;

use app\common\validate\BaseValidate;

class PushKeywordValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require|integer|gt:0',
        'merchant_id' => 'require|integer|gt:0',
        'keyword' => 'require|max:20',
        'is_enable' => 'in:0,1',
    ];

    protected $message = [
        'id.require' => '关键词ID不能为空',
        'id.integer' => '关键词ID必须是整数',
        'id.gt' => '关键词ID格式错误',
        'merchant_id.require' => '商家ID不能为空',
        'merchant_id.integer' => '商家ID必须是整数',
        'merchant_id.gt' => '商家ID格式错误',
        'keyword.require' => '关键词不能为空',
        'keyword.max' => '关键词最多20个字符',
        'is_enable.in' => '启用状态参数错误',
    ];

    public function sceneAdd()
    {
        return $this->only(['merchant_id', 'keyword']);
    }

    public function sceneEdit()
    {
        return $this->only(['id', 'keyword']);
    }

    public function sceneDelete()
    {
        return $this->only(['id']);
    }

    public function sceneToggle()
    {
        return $this->only(['id']);
    }

    public function sceneLists()
    {
        return $this->only(['merchant_id']);
    }
}
