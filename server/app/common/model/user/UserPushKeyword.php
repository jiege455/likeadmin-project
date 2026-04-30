<?php
namespace app\common\model\user;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

/**
 * 用户推送关键词模型
 * 开发者：杰哥网络科技 qq2711793818 杰哥
 */
class UserPushKeyword extends BaseModel
{
    use SoftDelete;

    protected $name = 'user_push_keyword';

    protected $deleteTime = 'delete_time';

    public function user()
    {
        return $this->belongsTo(\app\common\model\user\User::class, 'user_id');
    }

    public function merchant()
    {
        return $this->belongsTo(\app\common\model\merchant\Merchant::class, 'merchant_id');
    }

    public static function getUserKeywords($userId, $merchantId)
    {
        return self::where([
            'user_id' => $userId,
            'merchant_id' => $merchantId,
            'is_enable' => 1
        ])->column('keyword');
    }

    public static function getMerchantsByKeyword($keyword)
    {
        return self::where('keyword', 'like', '%' . $keyword . '%')
            ->where('is_enable', 1)
            ->field('user_id, merchant_id, keyword')
            ->select()
            ->toArray();
    }
}
