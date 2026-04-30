<?php
/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * 后台聊天违禁词管理控制器
 */
namespace app\adminapi\controller\chat;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\chat\ChatBannedWordLists;
use app\adminapi\logic\chat\ChatBannedWordLogic;
use app\adminapi\validate\chat\ChatBannedWordValidate;

class ChatBannedWordController extends BaseAdminController
{
    public function lists()
    {
        return $this->dataLists(new ChatBannedWordLists());
    }

    public function add()
    {
        $params = (new ChatBannedWordValidate())->post()->goCheck('add');
        ChatBannedWordLogic::add($params);
        return $this->success('添加成功');
    }

    public function edit()
    {
        $params = (new ChatBannedWordValidate())->post()->goCheck('edit');
        ChatBannedWordLogic::edit($params);
        return $this->success('编辑成功');
    }

    public function delete()
    {
        $params = (new ChatBannedWordValidate())->post()->goCheck('delete');
        ChatBannedWordLogic::delete($params);
        return $this->success('删除成功');
    }

    public function status()
    {
        $params = (new ChatBannedWordValidate())->post()->goCheck('status');
        ChatBannedWordLogic::status($params);
        return $this->success('操作成功');
    }
}
