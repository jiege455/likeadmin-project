<?php
namespace app\common\model\series;

use app\common\model\BaseModel;

class OpenResultLog extends BaseModel
{
    protected $name = 'open_result_log';

    public function getSyncStatusTextAttr($value, $data)
    {
        return $data['sync_status'] == 1 ? '成功' : '失败';
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

    public function matchedSeries()
    {
        return $this->belongsTo(Series::class, 'matched_series_id');
    }

    public function matchedArticle()
    {
        return $this->belongsTo(\app\common\model\article\Article::class, 'matched_article_id');
    }
}
