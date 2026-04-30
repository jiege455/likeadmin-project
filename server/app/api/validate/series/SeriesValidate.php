<?php
/**
 * 系列验证器
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
namespace app\api\validate\series;

use app\common\validate\BaseValidate;

class SeriesValidate extends BaseValidate
{
    protected $rule = [
        'name' => 'require',
        'lottery_type' => 'require|in:fc3d,pl3,ssq,dlt',
        'series_price' => 'require|float|egt:0',
        'total_issues' => 'integer|egt:0',
        'series_desc' => '',
        'auto_publish' => 'in:0,1',
        'publish_interval' => 'integer|egt:0',
        'series_status' => 'in:0,1'
    ];

    protected $message = [
        'name.require' => '请输入系列名称',
        'lottery_type.require' => '请选择彩票类型',
        'lottery_type.in' => '彩票类型不正确',
        'series_price.require' => '请输入系列价格',
        'series_price.float' => '系列价格格式不正确',
        'series_price.egt' => '系列价格不能为负数',
        'total_issues.integer' => '总期数必须是整数',
        'total_issues.egt' => '总期数不能为负数',
        'auto_publish.in' => '自动发布参数不正确',
        'publish_interval.integer' => '发布间隔必须是整数',
        'publish_interval.egt' => '发布间隔不能为负数',
        'series_status.in' => '系列状态参数不正确'
    ];

    protected $scene = [
        'add' => ['name', 'lottery_type', 'series_price'],
        'edit' => ['name', 'lottery_type', 'series_price']
    ];
}
