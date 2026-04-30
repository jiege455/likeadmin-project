<?php
/**
 * 推送消息控制器
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
namespace app\api\controller;

use app\api\controller\BaseApiController;
use app\api\logic\push\PushMessageLogic;
use app\api\lists\push\PushMessageLists;

class PushMessageController extends BaseApiController
{
    public array $notNeedLogin = [];

    public function lists()
    {
        return $this->dataLists(new PushMessageLists());
    }

    public function read()
    {
        $id = $this->request->post('id');
        if (empty($id)) {
            return $this->fail('参数错误');
        }
        
        $result = PushMessageLogic::read($this->userId, $id);
        
        if ($result !== true) {
            return $this->fail($result);
        }
        
        return $this->success('操作成功');
    }

    public function readAll()
    {
        PushMessageLogic::readAll($this->userId);
        return $this->success('操作成功');
    }

    public function unreadCount()
    {
        $count = PushMessageLogic::unreadCount($this->userId);
        return $this->data(['count' => $count]);
    }

    public function delete()
    {
        $id = $this->request->post('id');
        if (empty($id)) {
            return $this->fail('参数错误');
        }
        
        $result = PushMessageLogic::delete($this->userId, $id);
        
        if ($result !== true) {
            return $this->fail($result);
        }
        
        return $this->success('删除成功');
    }
}
