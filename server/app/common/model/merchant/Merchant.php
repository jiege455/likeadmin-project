<?php
namespace app\common\model\merchant;

use app\common\model\BaseModel;
use app\common\model\user\User;
use think\model\concern\SoftDelete;

/**
 * 商户模型
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
class Merchant extends BaseModel
{
    use SoftDelete;
    protected $name = 'merchant';
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
        $status = [
            0 => '待审核',
            1 => '正常',
            2 => '已拒绝',
            3 => '已禁用'
        ];
        return $status[$data['status']] ?? '未知';
    }
}
