<?php
/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * 聊天禁言记录模型
 */
namespace app\common\model\chat;

use app\common\model\BaseModel;
use app\common\model\user\User;
use app\common\model\admin\Admin;

class ChatBan extends BaseModel
{
    protected $name = 'chat_ban';

    const USER_TYPE_USER = 1;
    const USER_TYPE_MERCHANT = 2;

    const BAN_TYPE_PRIVATE = 1;
    const BAN_TYPE_PUBLIC = 2;
    const BAN_TYPE_ALL = 3;

    const STATUS_CANCEL = 0;
    const STATUS_BANNED = 1;

    public function getCreateTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }

    public function getExpireTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : '永久';
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    public static function isBanned(int $userId, int $userType, int $banType = null): bool
    {
        $query = self::where('user_id', $userId)
            ->where('user_type', $userType)
            ->where('status', self::STATUS_BANNED);
        
        if ($banType !== null) {
            $query->where(function($q) use ($banType) {
                $q->where('ban_type', $banType)
                  ->whereOr('ban_type', self::BAN_TYPE_ALL);
            });
        }
        
        $ban = $query->find();
        
        if (!$ban) {
            return false;
        }
        
        if ($ban->expire_time && $ban->expire_time < time()) {
            $ban->status = self::STATUS_CANCEL;
            $ban->update_time = time();
            $ban->save();
            return false;
        }
        
        return true;
    }

    public static function getBanInfo(int $userId, int $userType, int $banType = null): ?array
    {
        $query = self::where('user_id', $userId)
            ->where('user_type', $userType)
            ->where('status', self::STATUS_BANNED);
        
        if ($banType !== null) {
            $query->where(function($q) use ($banType) {
                $q->where('ban_type', $banType)
                  ->whereOr('ban_type', self::BAN_TYPE_ALL);
            });
        }
        
        $ban = $query->order('id', 'desc')->find();
        
        if (!$ban) {
            return null;
        }
        
        if ($ban->expire_time && $ban->expire_time < time()) {
            $ban->status = self::STATUS_CANCEL;
            $ban->update_time = time();
            $ban->save();
            return null;
        }
        
        return [
            'id' => $ban->id,
            'reason' => $ban->reason,
            'ban_type' => $ban->ban_type,
            'expire_time' => $ban->expire_time ? date('Y-m-d H:i:s', $ban->expire_time) : '永久',
            'is_permanent' => $ban->expire_time ? false : true
        ];
    }

    public static function addBan(int $userId, int $userType, int $banType, string $reason, int $adminId, ?int $expireTime = null): bool
    {
        $ban = new self();
        $ban->user_id = $userId;
        $ban->user_type = $userType;
        $ban->ban_type = $banType;
        $ban->reason = $reason;
        $ban->admin_id = $adminId;
        $ban->expire_time = $expireTime;
        $ban->status = self::STATUS_BANNED;
        $ban->create_time = time();
        
        return $ban->save();
    }

    public static function cancelBan(int $id, int $adminId): bool
    {
        $ban = self::find($id);
        if (!$ban) {
            return false;
        }
        
        $ban->status = self::STATUS_CANCEL;
        $ban->update_time = time();
        
        return $ban->save();
    }

    public static function getUserTypeText(int $userType): string
    {
        return match($userType) {
            self::USER_TYPE_USER => '普通用户',
            self::USER_TYPE_MERCHANT => '商家',
            default => '未知'
        };
    }

    public static function getBanTypeText(int $banType): string
    {
        return match($banType) {
            self::BAN_TYPE_PRIVATE => '私聊禁言',
            self::BAN_TYPE_PUBLIC => '公共聊天禁言',
            self::BAN_TYPE_ALL => '全部禁言',
            default => '未知'
        };
    }
}
