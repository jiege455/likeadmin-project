<?php
/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * 聊天设置逻辑
 */
namespace app\adminapi\logic\chat;

use app\common\model\chat\ChatSetting;
use app\common\logic\BaseLogic;
use think\facade\Db;

class ChatSettingLogic extends BaseLogic
{
    public static function getConfig(): array
    {
        $setting = ChatSetting::find(1);
        
        if (!$setting) {
            // 如果不存在，返回默认值
            return [
                'chat_enabled' => 1,
                'chat_notice' => '欢迎来到聊天室，请文明聊天！',
                'max_message_length' => 500,
                'message_interval' => 1,
                'enable_banned_word' => 1,
                'enable_ip_blacklist' => 0,
                'show_online_count' => 1,
            ];
        }
        
        return [
            'chat_enabled' => (int)$setting->chat_enabled,
            'chat_notice' => $setting->chat_notice ?? '欢迎来到聊天室，请文明聊天！',
            'max_message_length' => (int)$setting->max_message_length,
            'message_interval' => (int)$setting->message_interval,
            'enable_banned_word' => (int)$setting->enable_banned_word,
            'enable_ip_blacklist' => (int)$setting->enable_ip_blacklist,
            'show_online_count' => (int)$setting->show_online_count,
        ];
    }

    public static function setConfig(array $params): bool
    {
        try {
            $setting = ChatSetting::find(1);
            
            if ($setting) {
                // 更新现有配置
                $setting->chat_enabled = $params['chat_enabled'] ?? 1;
                $setting->chat_notice = $params['chat_notice'] ?? '';
                $setting->max_message_length = $params['max_message_length'] ?? 500;
                $setting->message_interval = $params['message_interval'] ?? 1;
                $setting->enable_banned_word = $params['enable_banned_word'] ?? 1;
                $setting->enable_ip_blacklist = $params['enable_ip_blacklist'] ?? 0;
                $setting->show_online_count = $params['show_online_count'] ?? 1;
                $setting->update_time = time();
                $setting->save();
            } else {
                // 创建新配置
                ChatSetting::create([
                    'chat_enabled' => $params['chat_enabled'] ?? 1,
                    'chat_notice' => $params['chat_notice'] ?? '',
                    'max_message_length' => $params['max_message_length'] ?? 500,
                    'message_interval' => $params['message_interval'] ?? 1,
                    'enable_banned_word' => $params['enable_banned_word'] ?? 1,
                    'enable_ip_blacklist' => $params['enable_ip_blacklist'] ?? 0,
                    'show_online_count' => $params['show_online_count'] ?? 1,
                    'create_time' => time(),
                    'update_time' => time()
                ]);
            }
            
            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }
}
