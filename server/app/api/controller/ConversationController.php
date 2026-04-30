<?php
/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * 私聊会话控制器
 */
namespace app\api\controller;

use app\api\lists\chat\ConversationLists;
use app\api\lists\chat\PrivateMessageLists;
use app\common\model\chat\ChatConversation;
use app\common\model\chat\ChatBan;
use app\common\model\chat\ChatMessage;
use app\common\model\merchant\Merchant;

class ConversationController extends BaseApiController
{
    public array $notNeedLogin = [];

    public function list()
    {
        return $this->dataLists(new ConversationLists());
    }

    public function create()
    {
        $targetId = $this->request->post('target_id', 0);
        $targetType = $this->request->post('target_type', 1);

        if ($targetId <= 0) {
            return $this->fail('参数错误：target_id无效');
        }

        if ($targetId == $this->userId) {
            return $this->fail('不能和自己私聊');
        }

        if ($targetType == 1) {
            $merchant = Merchant::find($targetId);
            if (!$merchant) {
                return $this->fail('商家不存在');
            }
        }

        $banInfo = ChatBan::getBanInfo($this->userId, ChatBan::USER_TYPE_USER, ChatBan::BAN_TYPE_PRIVATE);
        if ($banInfo) {
            return $this->fail('您已被禁言，原因：' . $banInfo['reason'] . '，解禁时间：' . $banInfo['expire_time']);
        }

        $result = ChatConversation::getOrCreate($this->userId, $targetId, $targetType);
        
        $conversation = $result['conversation'];
        $targetInfo = ChatConversation::getTargetInfo($targetId, $targetType);

        return $this->success('获取成功', [
            'conversation_id' => $conversation->conversation_id,
            'target_info' => $targetInfo,
            'is_new' => $result['is_new']
        ]);
    }

    public function detail()
    {
        $conversationId = $this->request->get('conversation_id', '');
        
        if (empty($conversationId)) {
            return $this->fail('参数错误：conversation_id不能为空');
        }

        $conversation = ChatConversation::where('conversation_id', $conversationId)
            ->where('user_id', $this->userId)
            ->find();

        if (!$conversation) {
            return $this->fail('会话不存在');
        }

        $targetInfo = ChatConversation::getTargetInfo($conversation->target_id, $conversation->target_type);

        return $this->success('获取成功', [
            'id' => $conversation->id,
            'conversation_id' => $conversation->conversation_id,
            'target_info' => $targetInfo,
            'last_message' => $conversation->last_message,
            'last_message_time' => $conversation->last_message_time,
            'unread_count' => $conversation->unread_count
        ]);
    }

    public function read()
    {
        $conversationId = $this->request->post('conversation_id', '');
        
        if (empty($conversationId)) {
            return $this->fail('参数错误：conversation_id不能为空');
        }

        $conversation = ChatConversation::where('conversation_id', $conversationId)
            ->where('user_id', $this->userId)
            ->find();

        if (!$conversation) {
            return $this->fail('会话不存在');
        }

        ChatConversation::clearUnread($this->userId, $conversationId);

        return $this->success('操作成功');
    }

    public function delete()
    {
        $conversationId = $this->request->post('conversation_id', '');
        
        if (empty($conversationId)) {
            return $this->fail('参数错误：conversation_id不能为空');
        }

        $conversation = ChatConversation::where('conversation_id', $conversationId)
            ->where('user_id', $this->userId)
            ->find();

        if (!$conversation) {
            return $this->fail('会话不存在');
        }

        $conversation->is_deleted = 1;
        $conversation->update_time = time();
        $conversation->save();

        return $this->success('删除成功');
    }

    public function messages()
    {
        return $this->dataLists(new PrivateMessageLists());
    }

    public function unreadTotal()
    {
        $total = ChatConversation::where('user_id', $this->userId)
            ->where('is_deleted', 0)
            ->sum('unread_count');

        return $this->success('获取成功', [
            'total' => (int)$total
        ]);
    }
}
