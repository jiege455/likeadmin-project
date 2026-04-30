<?php
/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * 私聊会话列表
 */
namespace app\api\lists\chat;

use app\api\lists\BaseApiDataLists;
use app\common\model\chat\ChatConversation;
use app\common\service\FileService;
use think\facade\Config;

class ConversationLists extends BaseApiDataLists
{
    public function lists(): array
    {
        $conversations = ChatConversation::where('user_id', $this->userId)
            ->where('is_deleted', 0)
            ->where('last_message', '<>', '')
            ->order('last_message_time', 'desc')
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        $frontendUrl = Config::get('app.frontend_url', '');

        foreach ($conversations as &$conv) {
            $targetInfo = ChatConversation::getTargetInfo($conv['target_id'], $conv['target_type']);
            
            $conv['target_info'] = $targetInfo;
            
            if ($targetInfo['logo'] && !str_starts_with($targetInfo['logo'], 'http')) {
                $conv['target_info']['logo'] = FileService::getFileUrl($targetInfo['logo']);
            }
            
            if (empty($conv['target_info']['logo'])) {
                $conv['target_info']['logo'] = '/static/images/user/default_avatar.png';
            }
        }

        return $conversations;
    }

    public function count(): int
    {
        return ChatConversation::where('user_id', $this->userId)
            ->where('is_deleted', 0)
            ->where('last_message', '<>', '')
            ->count();
    }
}
