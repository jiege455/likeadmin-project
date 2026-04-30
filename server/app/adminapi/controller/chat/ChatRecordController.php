<?php
/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * 后台聊天记录管理控制器
 */
namespace app\adminapi\controller\chat;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\chat\ChatRecordLists;
use app\common\model\chat\ChatMessage;

class ChatRecordController extends BaseAdminController
{
    public function lists()
    {
        return $this->dataLists(new ChatRecordLists());
    }

    public function detail()
    {
        $id = $this->request->get('id', 0);
        
        if ($id <= 0) {
            return $this->fail('参数错误');
        }

        $message = ChatMessage::find($id);
        if (!$message) {
            return $this->fail('消息不存在');
        }

        return $this->success('获取成功', $message->toArray());
    }

    public function delete()
    {
        $id = $this->request->post('id', 0);
        
        if ($id <= 0) {
            return $this->fail('参数错误');
        }

        $message = ChatMessage::find($id);
        if (!$message) {
            return $this->fail('消息不存在');
        }

        $message->is_deleted = 1;
        $message->update_time = time();
        $message->save();

        return $this->success('删除成功');
    }

    public function conversations()
    {
        $roomId = $this->request->get('room_id', '');
        
        $query = \app\common\model\chat\ChatConversation::where('is_deleted', 0);
        
        if ($roomId) {
            $query->where('conversation_id', $roomId);
        }

        $list = $query->order('last_message_time', 'desc')
            ->limit(100)
            ->select()
            ->toArray();

        foreach ($list as &$item) {
            $item['target_info'] = \app\common\model\chat\ChatConversation::getTargetInfo(
                $item['target_id'], 
                $item['target_type']
            );
        }

        return $this->success('获取成功', $list);
    }
}
