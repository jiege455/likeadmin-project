<?php
namespace app\common\model\finance;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

class WithdrawAccount extends BaseModel
{
    use SoftDelete;

    protected $name = 'withdraw_account';

    protected $deleteTime = 'delete_time';

    public function user()
    {
        return $this->belongsTo(\app\common\model\user\User::class, 'user_id');
    }

    public function merchant()
    {
        return $this->belongsTo(\app\common\model\merchant\Merchant::class, 'merchant_id');
    }

    public function getTypeTextAttr($value, $data)
    {
        $types = [
            1 => '微信',
            2 => '支付宝',
            3 => '银行卡'
        ];
        return $types[$data['type']] ?? '未知';
    }

    public function getStatusTextAttr($value, $data)
    {
        return $data['status'] == 1 ? '启用' : '禁用';
    }
}
