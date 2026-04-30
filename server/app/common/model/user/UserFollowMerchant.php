<?php
namespace app\common\model\user;

use app\common\model\BaseModel;

class UserFollowMerchant extends BaseModel
{
    protected $name = 'user_follow_merchant';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function merchant()
    {
        return $this->belongsTo(\app\common\model\merchant\Merchant::class, 'merchant_id');
    }

    public static function isFollow($userId, $merchantId)
    {
        return self::where([
            'user_id' => $userId,
            'merchant_id' => $merchantId
        ])->findOrEmpty()->isEmpty() ? false : true;
    }
}
