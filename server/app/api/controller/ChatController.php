<?php
/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * 聊天API控制器
 */
namespace app\api\controller;

use app\api\lists\chat\ChatMessageLists;
use app\common\model\chat\ChatRoom;
use app\common\model\chat\ChatMessage;
use app\common\model\chat\ChatSetting;
use app\common\utils\XssFilter;
use think\facade\Config;

class ChatController extends BaseApiController
{
    public array $notNeedLogin = ['rooms', 'config', 'setting'];

    public function lists()
    {
        return $this->dataLists(new ChatMessageLists());
    }

    public function rooms()
    {
        $rooms = ChatRoom::where('status', 1)
            ->where('is_public', 1)
            ->field('id, name, room_id, description, max_users')
            ->order('id', 'asc')
            ->select()
            ->toArray();
        
        return $this->success('获取成功', $rooms);
    }

    public function config()
    {
        $settings = $this->getSettings();
        
        $config = [
            'ws_url' => Config::get('project.websocket_url', 'ws://127.0.0.1:8282'),
            'heartbeat' => 30,
            'reconnect' => true,
            'reconnect_interval' => 3,
            'chat_enabled' => (int)($settings['chat_enabled'] ?? 1),
            'chat_notice' => $settings['chat_notice'] ?? '',
            'max_message_length' => (int)($settings['max_message_length'] ?? 500),
            'message_interval' => (int)($settings['message_interval'] ?? 1),
        ];
        
        return $this->success('获取成功', $config);
    }

    public function setting()
    {
        $settings = $this->getSettings();
        
        $data = [
            'chat_enabled' => (int)($settings['chat_enabled'] ?? 1),
            'chat_notice' => $settings['chat_notice'] ?? '',
            'max_message_length' => (int)($settings['max_message_length'] ?? 500),
            'message_interval' => (int)($settings['message_interval'] ?? 1),
            'show_online_count' => (int)($settings['show_online_count'] ?? 1),
        ];
        
        return $this->success('获取成功', $data);
    }

    public function send()
    {
        $settings = $this->getSettings();
        
        if (($settings['chat_enabled'] ?? 1) != 1) {
            return $this->fail('聊天功能已关闭');
        }
        
        $roomId = XssFilter::clean(trim($this->request->post('room_id', 'public')));
        $content = XssFilter::clean(trim($this->request->post('content', '')));
        $msgType = intval($this->request->post('msg_type', 1));
        
        if (empty($content)) {
            return $this->fail('消息内容不能为空');
        }
        
        $maxLength = (int)($settings['max_message_length'] ?? 500);
        if (mb_strlen($content) > $maxLength) {
            return $this->fail("消息内容不能超过{$maxLength}字");
        }
        
        if (($settings['enable_banned_word'] ?? 0) == 1) {
            $content = $this->filterBannedWords($content);
            if ($content === false) {
                return $this->fail('消息包含违禁词，发送失败');
            }
        }
        
        $nickname = XssFilter::clean($this->userInfo['nickname'] ?? ('用户' . $this->userId));
        $avatar = XssFilter::clean($this->userInfo['avatar'] ?? '');
        
        $message = new ChatMessage();
        $message->room_id = $roomId;
        $message->user_id = $this->userId;
        $message->nickname = $nickname;
        $message->avatar = $avatar;
        $message->content = $content;
        $message->msg_type = $msgType;
        $message->create_time = time();
        $message->save();
        
        return $this->success('发送成功', [
            'id' => $message->id,
            'content' => $content,
            'create_time' => date('Y-m-d H:i:s')
        ]);
    }

    private function getSettings(): array
    {
        static $settings = null;
        if ($settings === null) {
            $setting = ChatSetting::find(1);
            if ($setting) {
                $settings = [
                    'chat_enabled' => $setting->chat_enabled,
                    'chat_notice' => $setting->chat_notice,
                    'max_message_length' => $setting->max_message_length,
                    'message_interval' => $setting->message_interval,
                    'enable_banned_word' => $setting->enable_banned_word,
                    'enable_ip_blacklist' => $setting->enable_ip_blacklist,
                    'show_online_count' => $setting->show_online_count,
                ];
            } else {
                $settings = [
                    'chat_enabled' => 1,
                    'chat_notice' => '',
                    'max_message_length' => 500,
                    'message_interval' => 1,
                    'enable_banned_word' => 1,
                    'enable_ip_blacklist' => 0,
                    'show_online_count' => 1,
                ];
            }
        }
        return $settings;
    }

    private function filterBannedWords(string $content): string|false
    {
        $bannedWords = ChatSetting::getBannedWords();
        foreach ($bannedWords as $item) {
            if (stripos($content, $item['word']) !== false) {
                if (!empty($item['replace_word'])) {
                    $content = str_ireplace($item['word'], $item['replace_word'], $content);
                } else {
                    return false;
                }
            }
        }
        return $content;
    }
}
