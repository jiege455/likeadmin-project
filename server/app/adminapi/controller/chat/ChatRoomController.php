<?php
/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * 后台聊天室管理控制器
 */
namespace app\adminapi\controller\chat;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\chat\ChatRoomLists;
use app\adminapi\logic\chat\ChatRoomLogic;
use app\adminapi\validate\chat\ChatRoomValidate;

class ChatRoomController extends BaseAdminController
{
    public function lists()
    {
        return $this->dataLists(new ChatRoomLists());
    }

    public function add()
    {
        $params = (new ChatRoomValidate())->post()->goCheck('add');
        ChatRoomLogic::add($params);
        return $this->success('添加成功');
    }

    public function edit()
    {
        $params = (new ChatRoomValidate())->post()->goCheck('edit');
        ChatRoomLogic::edit($params);
        return $this->success('编辑成功');
    }

    public function detail()
    {
        $params = (new ChatRoomValidate())->goCheck('detail');
        $result = ChatRoomLogic::detail($params);
        return $this->data($result);
    }

    public function delete()
    {
        $params = (new ChatRoomValidate())->post()->goCheck('delete');
        ChatRoomLogic::delete($params);
        return $this->success('删除成功');
    }

    public function status()
    {
        $params = (new ChatRoomValidate())->post()->goCheck('status');
        ChatRoomLogic::status($params);
        return $this->success('操作成功');
    }
}
