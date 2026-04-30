<?php
namespace app\common\model\distribution;

use app\common\model\BaseModel;
use app\common\model\user\User;

/**
 * 分销记录模型
 */
class DistributionLog extends BaseModel
{
    protected $name = 'distribution_log';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sourceUser()
    {
        return $this->belongsTo(User::class, 'source_user_id');
    }

    public function getStatusDescAttr($value, $data)
    {
        return $data['status'] == 1 ? '已结算' : '待结算';
    }
}
