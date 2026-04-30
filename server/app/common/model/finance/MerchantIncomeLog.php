<?php
namespace app\common\model\finance;

use app\common\model\BaseModel;
use app\common\model\merchant\Merchant;

/**
 * 商户收入记录
 */
class MerchantIncomeLog extends BaseModel
{
    protected $name = 'merchant_income_log';

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function getSourceTypeDescAttr($value, $data)
    {
        $types = [1 => '文章', 2 => '课程'];
        return $types[$data['source_type']] ?? '未知';
    }
}
