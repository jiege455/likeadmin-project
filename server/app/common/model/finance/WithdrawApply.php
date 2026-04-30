<?php
namespace app\common\model\finance;

use app\common\model\BaseModel;
use app\common\model\merchant\Merchant;

class WithdrawApply extends BaseModel
{
    protected $name = 'withdraw_apply';

    const STATUS_WAIT = 0;
    const STATUS_REJECT = 1;
    const STATUS_PASS = 2;
    const STATUS_PAID = 3;

    const TYPE_WECHAT = 1;
    const TYPE_ALIPAY = 2;
    const TYPE_BANK = 3;

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function getStatusDescAttr($value, $data)
    {
        $desc = [
            self::STATUS_WAIT => '待审核',
            self::STATUS_REJECT => '已拒绝',
            self::STATUS_PASS => '已通过',
            self::STATUS_PAID => '已打款',
        ];
        return $desc[$data['status']] ?? '';
    }

    public function getTypeDescAttr($value, $data)
    {
        $desc = [
            self::TYPE_WECHAT => '微信零钱',
            self::TYPE_ALIPAY => '支付宝',
            self::TYPE_BANK => '银行卡',
        ];
        return $desc[$data['type']] ?? '';
    }
}
