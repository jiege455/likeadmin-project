<?php

namespace app\api\logic;

use app\common\model\notice\SystemNotice;
use app\common\model\notice\NoticeRead;
use app\common\logic\BaseLogic;

class NoticeLogic extends BaseLogic
{
    public static function detail($id, $userId = null)
    {
        $notice = SystemNotice::where(['id' => $id, 'is_show' => 1])
            ->findOrEmpty();
        
        if ($notice->isEmpty()) {
            return [];
        }

        SystemNotice::where('id', $id)->inc('views')->update();

        if ($userId) {
            NoticeRead::markRead($id, $userId);
        }

        $result = $notice->toArray();
        $result['is_read'] = $userId ? NoticeRead::hasRead($id, $userId) : false;
        
        return $result;
    }

    public static function getUnreadCount($userId)
    {
        $totalNotice = SystemNotice::where('is_show', 1)->count();
        $readCount = NoticeRead::where('user_id', $userId)->count();
        return max(0, $totalNotice - $readCount);
    }

    public static function getPopupNotice($userId = null)
    {
        $notice = SystemNotice::where(['is_show' => 1, 'type' => SystemNotice::TYPE_IMPORTANT])
            ->order(['is_top' => 'desc', 'sort' => 'desc', 'id' => 'desc'])
            ->find();

        if (!$notice) {
            return null;
        }

        $popupType = $notice->popup_type;
        
        if ($userId && $popupType == 1) {
            $cacheKey = 'notice_popup_' . $userId . '_' . $notice->id;
            $lastPopupTime = cache($cacheKey);
            if ($lastPopupTime && date('Ymd') == date('Ymd', $lastPopupTime)) {
                return null;
            }
            cache($cacheKey, time(), 86400);
        }

        return $notice->toArray();
    }

    public static function markAllRead($userId)
    {
        $notices = SystemNotice::where('is_show', 1)->column('id');
        foreach ($notices as $noticeId) {
            NoticeRead::markRead($noticeId, $userId);
        }
        return true;
    }
}
