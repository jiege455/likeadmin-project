<?php
/**
 * 文章标签验证器
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
namespace app\api\validate;

use think\Validate;

class ArticleTagValidate extends Validate
{
    protected $rule = [
        'id' => 'require|integer|gt:0',
        'tag_id' => 'require|integer|gt:0',
        'name' => 'require|max:20',
        'page_no' => 'integer|gt:0',
        'page_size' => 'integer|between:1,100',
    ];

    protected $message = [
        'id.require' => '标签ID不能为空',
        'id.integer' => '标签ID必须是整数',
        'id.gt' => '标签ID格式错误',
        'tag_id.require' => '标签ID不能为空',
        'tag_id.integer' => '标签ID必须是整数',
        'tag_id.gt' => '标签ID格式错误',
        'name.require' => '标签名称不能为空',
        'name.max' => '标签名称最多20个字符',
        'page_no.integer' => '页码必须是整数',
        'page_no.gt' => '页码必须大于0',
        'page_size.integer' => '每页数量必须是整数',
        'page_size.between' => '每页数量必须在1-100之间',
    ];

    public function sceneCreate()
    {
        return $this->only(['name']);
    }

    public function sceneArticles()
    {
        return $this->only(['tag_id', 'page_no', 'page_size']);
    }
}
