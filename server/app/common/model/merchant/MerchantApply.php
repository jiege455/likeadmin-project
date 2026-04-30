<?php
namespace app\common\model\merchant;

use app\common\model\BaseModel;
use app\common\model\user\User;
use think\model\concern\SoftDelete;

/**
 * 商家入驻申请模型
 * Class MerchantApply
 * @package app\common\model\merchant
 */
class MerchantApply extends BaseModel
{
    use SoftDelete;
    protected $name = 'merchant_apply';
    protected $deleteTime = 'delete_time';

    /**
     * @notes 关联用户
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @notes 状态获取器
     */
    public function getStatusDescAttr($value, $data)
    {
        $status = [0 => '待审核', 1 => '已通过', 2 => '已拒绝'];
        return $status[$data['status']] ?? '未知';
    }

    /**
     * @notes 创建时间
     */
    public function getCreateTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }

    /**
     * @notes 更新时间
     */
    public function getUpdateTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }
}
