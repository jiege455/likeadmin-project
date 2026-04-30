<?php
namespace app\common\model\user;

use app\common\model\BaseModel;

class UserMerchant extends BaseModel
{
    protected $name = 'user_merchant';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function merchant()
    {
        return $this->belongsTo(\app\common\model\merchant\Merchant::class, 'merchant_id');
    }

    public function inviter()
    {
        return $this->belongsTo(User::class, 'inviter_id');
    }
}
