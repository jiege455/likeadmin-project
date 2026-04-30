<?php
namespace app\common\model\series;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

class SeriesOrder extends BaseModel
{
    use SoftDelete;

    protected $name = 'series_order';

    protected $deleteTime = 'delete_time';

    public function user()
    {
        return $this->belongsTo(\app\common\model\user\User::class, 'user_id');
    }

    public function series()
    {
        return $this->belongsTo(Series::class, 'series_id');
    }

    public function getPayStatusTextAttr($value, $data)
    {
        $status = [
            0 => '未支付',
            1 => '已支付'
        ];
        return $status[$data['pay_status']] ?? '未知';
    }
}
