<?php
namespace app\common\model\notice;

use app\common\model\BaseModel;

class NoticeRead extends BaseModel
{
    protected $name = 'notice_read';

    public static function hasRead($noticeId, $userId)
    {
        return self::where('notice_id', $noticeId)
            ->where('user_id', $userId)
            ->count() > 0;
    }

    public static function markRead($noticeId, $userId)
    {
        try {
            self::create([
                'notice_id' => $noticeId,
                'user_id' => $userId,
                'create_time' => time()
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getReadNoticeIds($userId)
    {
        return self::where('user_id', $userId)->column('notice_id');
    }
}
