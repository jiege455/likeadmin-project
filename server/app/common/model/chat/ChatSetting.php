<?php
/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * 聊天设置模型
 */
namespace app\common\model\chat;

use app\common\model\BaseModel;

class ChatSetting extends BaseModel
{
    protected $name = 'chat_setting';

    public static function getBannedWords(): array
    {
        static $words = null;
        if ($words === null) {
            $words = (new \app\common\model\chat\ChatBannedWord())
                ->where('status', 1)
                ->field('word, replace_word')
                ->select()
                ->toArray();
        }
        return $words;
    }
}
