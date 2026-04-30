<?php
/**
 * 商家文章验证器
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
namespace app\api\validate\merchant;

use think\Validate;

class ArticleValidate extends Validate
{
    protected $rule = [
        'id' => 'require|integer|gt:0',
        'title' => 'require|max:100',
        'desc' => 'max:500',
        'content' => 'require',
        'image' => 'max:255',
        'cate_id' => 'integer|gt:0',
        'is_pay' => 'in:0,1',
        'price' => 'float|egt:0',
        'series_id' => 'integer|gt:0',
        'issue_id' => 'integer|gt:0',
        'page_no' => 'integer|gt:0',
        'page_size' => 'integer|between:1,100',
    ];

    protected $message = [
        'id.require' => '文章ID不能为空',
        'id.integer' => '文章ID必须是整数',
        'id.gt' => '文章ID格式错误',
        'title.require' => '文章标题不能为空',
        'title.max' => '文章标题最多100个字符',
        'desc.max' => '文章摘要最多500个字符',
        'content.require' => '文章内容不能为空',
        'image.max' => '封面图地址最多255个字符',
        'cate_id.integer' => '分类ID必须是整数',
        'cate_id.gt' => '分类ID格式错误',
        'is_pay.in' => '付费状态错误',
        'price.float' => '价格格式错误',
        'price.egt' => '价格不能为负数',
        'series_id.integer' => '系列ID必须是整数',
        'series_id.gt' => '系列ID格式错误',
        'issue_id.integer' => '期次ID必须是整数',
        'issue_id.gt' => '期次ID格式错误',
        'page_no.integer' => '页码必须是整数',
        'page_no.gt' => '页码必须大于0',
        'page_size.integer' => '每页数量必须是整数',
        'page_size.between' => '每页数量必须在1-100之间',
    ];

    public function sceneLists()
    {
        return $this->only(['page_no', 'page_size']);
    }

    public function sceneDetail()
    {
        return $this->only(['id']);
    }

    public function sceneSave()
    {
        return $this->only(['id', 'title', 'desc', 'content', 'image', 'cate_id', 'is_pay', 'price', 'series_id', 'issue_id']);
    }

    public function sceneDelete()
    {
        return $this->only(['id']);
    }
}
