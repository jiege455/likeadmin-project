<?php
namespace app\common\model\merchant;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

class MerchantComplaint extends BaseModel
{
    use SoftDelete;

    protected $name = 'merchant_complaint';
    protected $deleteTime = 'delete_time';

    const TYPE_MERCHANT = 1;
    const TYPE_ARTICLE = 2;

    public static function getTypeDesc($type = null)
    {
        $desc = [
            self::TYPE_MERCHANT => '商家',
            self::TYPE_ARTICLE => '文章',
        ];
        if ($type !== null) {
            return $desc[$type] ?? '未知';
        }
        return $desc;
    }

    public function user()
    {
        return $this->belongsTo(\app\common\model\user\User::class, 'user_id');
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }
}
