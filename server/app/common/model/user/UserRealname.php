<?php
namespace app\common\model\user;

use app\common\model\BaseModel;
use app\common\model\user\User;
use think\model\concern\SoftDelete;

class UserRealname extends BaseModel
{
    use SoftDelete;

    protected $name = 'user_realname';
    protected $deleteTime = 'delete_time';

    // 状态
    const STATUS_WAIT = 0; // 待审核
    const STATUS_SUCCESS = 1; // 认证通过
    const STATUS_FAIL = 2; // 认证失败

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getStatusDescAttr($value, $data)
    {
        $desc = [
            self::STATUS_WAIT => '待审核',
            self::STATUS_SUCCESS => '认证通过',
            self::STATUS_FAIL => '认证失败',
        ];
        return $desc[$data['status']] ?? '';
    }
}
