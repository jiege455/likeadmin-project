<?php
/**
 * 期次模型
 * 开发者公众号：杰哥网络科技
 * qq2711793818 杰哥
 */
namespace app\common\model\series;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

class Issue extends BaseModel
{
    use SoftDelete;

    protected $name = 'issue';

    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = null;

    public function series()
    {
        return $this->belongsTo(Series::class, 'series_id', 'id');
    }

    public function merchant()
    {
        return $this->belongsTo('app\common\model\merchant\Merchant', 'merchant_id', 'id');
    }

    public function getIssueStatusTextAttr($value, $data)
    {
        $status = [
            0 => '草稿',
            1 => '已发布',
            2 => '已开奖'
        ];
        return $status[$data['issue_status']] ?? '未知';
    }

    public function getIsOpenedTextAttr($value, $data)
    {
        return $data['is_opened'] == 1 ? '已开奖' : '未开奖';
    }

    public function scopeSeries($query, $seriesId)
    {
        return $query->where('series_id', $seriesId);
    }

    public function scopePublished($query)
    {
        return $query->where('issue_status', '>', 0);
    }

    public function scopeOpened($query)
    {
        return $query->where('is_opened', 1);
    }

    public function scopeMerchant($query, $merchantId)
    {
        return $query->where('merchant_id', $merchantId);
    }
}
