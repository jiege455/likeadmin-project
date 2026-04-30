<?php
/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * 违禁词管理逻辑
 */
namespace app\adminapi\logic\chat;

use app\common\model\chat\ChatBannedWord;
use app\common\logic\BaseLogic;

class ChatBannedWordLogic extends BaseLogic
{
    public static function add(array $params): bool
    {
        try {
            $exists = ChatBannedWord::where('word', $params['word'])->find();
            if ($exists) {
                self::setError('该违禁词已存在');
                return false;
            }
            
            ChatBannedWord::create([
                'word' => $params['word'],
                'type' => $params['type'] ?? 1,
                'replace_word' => $params['replace_word'] ?? '',
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
            $exists = ChatBannedWord::where('word', $params['word'])
                ->where('id', '<>', $params['id'])
                ->find();
            if ($exists) {
                self::setError('该违禁词已存在');
                return false;
            }
            
            ChatBannedWord::update([
                'id' => $params['id'],
                'word' => $params['word'],
                'type' => $params['type'] ?? 1,
                'replace_word' => $params['replace_word'] ?? '',
                'update_time' => time(),
            ]);
            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    public static function delete(array $params): bool
    {
        try {
            ChatBannedWord::destroy($params['id']);
            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    public static function status(array $params): bool
    {
        try {
            ChatBannedWord::update([
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
