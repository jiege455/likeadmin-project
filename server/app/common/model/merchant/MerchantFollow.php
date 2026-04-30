<?php
namespace app\common\model\merchant;

use app\common\model\BaseModel;

/**
 * 商家关注模型
 * Class MerchantFollow
 * @package app\common\model\merchant
 */
class MerchantFollow extends BaseModel
{
    protected $name = 'merchant_follow';

    /**
     * @notes 关联商家
     */
    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id', 'id');
    }

    /**
     * @notes 检查是否已关注
     * @param $userId
     * @param $merchantId
     * @return bool
     */
    public static function isFollow($userId, $merchantId)
    {
        return self::where([
            'user_id' => $userId,
            'merchant_id' => $merchantId
        ])->findOrEmpty()->isEmpty() ? false : true;
    }
}
