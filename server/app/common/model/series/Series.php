<?php
/**
 * 系列模型
 * 开发者公众号：杰哥网络科技
 * qq2711793818 杰哥
 */
namespace app\common\model\series;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

class Series extends BaseModel
{
    use SoftDelete;

    protected $name = 'series';

    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = null;

    public function issues()
    {
        return $this->hasMany(Issue::class, 'series_id', 'id');
    }

    public function merchant()
    {
        return $this->belongsTo('app\common\model\merchant\Merchant', 'merchant_id', 'id');
    }

    public function getLotteryTypeTextAttr($value, $data)
    {
        $types = [
            'fc3d' => '福彩3D',
            'pl3' => '排列三',
            'ssq' => '双色球',
            'dlt' => '大乐透'
        ];
        return $types[$data['lottery_type']] ?? $data['lottery_type'];
    }

    public function getSeriesStatusTextAttr($value, $data)
    {
        return $data['series_status'] == 1 ? '进行中' : '已结束';
    }

    public function getAutoPublishTextAttr($value, $data)
    {
        return $data['auto_publish'] == 1 ? '是' : '否';
    }

    public function scopeSeries($query)
    {
        return $query;
    }

    public function scopeMerchant($query, $merchantId)
    {
        return $query->where('merchant_id', $merchantId);
    }

    public function scopePublished($query)
    {
        return $query->where('series_status', 1);
    }
}
