<?php
namespace app\api\logic\push;

use app\common\logic\BaseLogic;
use app\common\model\notice\PushMessage;

/**
 * 推送消息逻辑
 * 开发者：杰哥网络科技 qq2711793818 杰哥
 */
class PushMessageLogic extends BaseLogic
{
    public static function lists($userId, $pageNo, $pageSize)
    {
        $lists = PushMessage::where('user_id', $userId)
            ->where('delete_time', null)
            ->field('id, merchant_id, article_id, keyword, title, content, is_read, create_time')
            ->order('id', 'desc')
            ->page($pageNo, $pageSize)
            ->select()
            ->toArray();

        return $lists;
    }

    public static function read($userId, $id)
    {
        $message = PushMessage::where('id', $id)
            ->where('delete_time', null)
            ->find();
        if (!$message || $message->user_id != $userId) {
            return '消息不存在';
        }

        $message->is_read = 1;
        $message->update_time = time();
        $message->save();

        return true;
    }

    public static function readAll($userId)
    {
        PushMessage::where('user_id', $userId)
            ->where('is_read', 0)
            ->where('delete_time', null)
            ->update([
                'is_read' => 1,
                'update_time' => time()
            ]);

        return true;
    }

    public static function unreadCount($userId)
    {
        return PushMessage::where([
            'user_id' => $userId,
            'is_read' => 0
        ])->where('delete_time', null)->count();
    }

    public static function delete($userId, $id)
    {
        $message = PushMessage::where('id', $id)
            ->where('delete_time', null)
            ->find();
        if (!$message || $message->user_id != $userId) {
            return '消息不存在';
        }

        $message->delete();
        return true;
    }
}
