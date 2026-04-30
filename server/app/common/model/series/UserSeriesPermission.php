<?php
namespace app\common\model\series;

use app\common\model\BaseModel;

class UserSeriesPermission extends BaseModel
{
    protected $name = 'user_series_permission';

    public function user()
    {
        return $this->belongsTo(\app\common\model\user\User::class, 'user_id');
    }

    public function series()
    {
        return $this->belongsTo(Series::class, 'series_id');
    }

    public function order()
    {
        return $this->belongsTo(SeriesOrder::class, 'order_id');
    }

    public function isExpired()
    {
        if ($this->expire_time === null) {
            return false;
        }
        return $this->expire_time < time();
    }
}
