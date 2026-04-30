<?php
/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * 聊天室管理逻辑
 */
namespace app\adminapi\logic\chat;

use app\common\model\chat\ChatRoom;
use app\common\logic\BaseLogic;
use think\facade\Db;

class ChatRoomLogic extends BaseLogic
{
    public static function add(array $params): bool
    {
        try {
            ChatRoom::create([
                'name' => $params['name'],
                'room_id' => $params['room_id'],
                'description' => $params['description'] ?? '',
                'max_users' => $params['max_users'] ?? 1000,
                'is_public' => $params['is_public'] ?? 1,
                'status' => $params['status'] ?? 1,
                'create_time' => time(),
            ]);
            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    public static function edit(array $params): bool
    {
        try {
            ChatRoom::update([
                'id' => $params['id'],
                'name' => $params['name'],
                'description' => $params['description'] ?? '',
                'max_users' => $params['max_users'] ?? 1000,
                'is_public' => $params['is_public'] ?? 1,
                'update_time' => time(),
            ]);
            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    public static function detail(array $params): array
    {
        $data = ChatRoom::findOrEmpty($params['id']);
        return $data->toArray();
    }

    public static function delete(array $params): bool
    {
        try {
            if ($params['id'] == 1) {
                self::setError('公共频道不能删除');
                return false;
            }
            ChatRoom::destroy($params['id']);
            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    public static function status(array $params): bool
    {
        try {
            ChatRoom::update([
                'id' => $params['id'],
                'status' => $params['status'],
                'update_time' => time(),
            ]);
            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }
}
