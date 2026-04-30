<?php

namespace app\common\model\distribution;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

/**
 * 分销员申请模型
 * Class DistributionApply
 * @package app\common\model\distribution
 */
class DistributionApply extends BaseModel
{
    use SoftDelete;

    protected $deleteTime = 'delete_time';

    // 状态枚举
    const STATUS_WAIT = 0; // 待审核
    const STATUS_PASS = 1; // 审核通过
    const STATUS_FAIL = 2; // 审核拒绝

    /**
     * @notes 关联用户
     * @return \think\model\relation\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\app\common\model\user\User::class, 'user_id');
    }

    /**
     * @notes 状态获取器
     * @param $value
     * @return string
     */
    public function getStatusDescAttr($value, $data)
    {
        $desc = [
            self::STATUS_WAIT => '待审核',
            self::STATUS_PASS => '审核通过',
            self::STATUS_FAIL => '审核拒绝',
        ];
        return $desc[$data['status']] ?? '';
    }
}
