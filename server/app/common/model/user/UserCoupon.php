<?php
namespace app\common\model\user;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

class UserCoupon extends BaseModel
{
    use SoftDelete;

    protected $name = 'user_coupon';

    protected $deleteTime = 'delete_time';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getStatusTextAttr($value, $data)
    {
        $status = [
            0 => '未使用',
            1 => '已使用',
            2 => '已过期'
        ];
        return $status[$data['status']] ?? '未知';
    }
}
