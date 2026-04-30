<?php
/**
 * 期次验证器
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
namespace app\api\validate\series;

use app\common\validate\BaseValidate;

class IssueValidate extends BaseValidate
{
    protected $rule = [
        'series_id' => 'require|integer|gt:0',
        'issue_no' => 'require|max:20',
        'title' => 'require|max:255',
        'content' => 'require',
        'open_code' => 'max:100',
        'is_opened' => 'in:0,1',
        'issue_status' => 'in:0,1,2'
    ];

    protected $message = [
        'series_id.require' => '请选择系列',
        'series_id.integer' => '系列ID格式不正确',
        'series_id.gt' => '系列ID必须大于0',
        'issue_no.require' => '请输入期号',
        'issue_no.max' => '期号不能超过20个字符',
        'title.require' => '请输入标题',
        'title.max' => '标题不能超过255个字符',
        'content.require' => '请输入内容',
        'open_code.max' => '开奖号码不能超过100个字符',
        'is_opened.in' => '是否开奖参数不正确',
        'issue_status.in' => '期次状态参数不正确'
    ];

    protected $scene = [
        'add' => ['series_id', 'issue_no', 'title', 'content'],
        'edit' => ['issue_no', 'title', 'content'],
        'publish' => ['issue_status']
    ];
}
