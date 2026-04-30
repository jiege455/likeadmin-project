<?php
/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * 后台聊天消息管理控制器
 */
namespace app\adminapi\controller\chat;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\chat\ChatMessageLists;
use app\adminapi\logic\chat\ChatMessageLogic;
use app\adminapi\validate\chat\ChatMessageValidate;

class ChatMessageController extends BaseAdminController
{
    public function lists()
    {
        return $this->dataLists(new ChatMessageLists());
    }

    public function detail()
    {
        $params = (new ChatMessageValidate())->goCheck('detail');
        $result = ChatMessageLogic::detail($params);
        return $this->data($result);
    }

    public function delete()
    {
        $params = (new ChatMessageValidate())->post()->goCheck('delete');
        ChatMessageLogic::delete($params);
        return $this->success('删除成功');
    }

    public function clear()
    {
        $params = $this->request->post();
        ChatMessageLogic::clear($params);
        return $this->success('清空成功');
    }
}
