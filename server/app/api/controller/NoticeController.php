<?php

namespace app\api\controller;

use app\api\lists\notice\NoticeLists;
use app\api\logic\NoticeLogic;

class NoticeController extends BaseApiController
{
    public array $notNeedLogin = ['lists', 'detail', 'popup'];

    public function lists()
    {
        return $this->dataLists(new NoticeLists());
    }

    public function detail()
    {
        $id = $this->request->get('id/d');
        $userId = $this->userId ?? null;
        $result = NoticeLogic::detail($id, $userId);
        return $this->data($result);
    }

    public function unreadCount()
    {
        if (!$this->userId) {
            return $this->data(['count' => 0]);
        }
        $count = NoticeLogic::getUnreadCount($this->userId);
        return $this->data(['count' => $count]);
    }

    public function popup()
    {
        $userId = $this->userId ?? null;
        $result = NoticeLogic::getPopupNotice($userId);
        return $this->data($result);
    }

    public function markRead()
    {
        $id = $this->request->post('id/d');
        if (!$this->userId) {
            return $this->fail('请先登录');
        }
        NoticeLogic::detail($id, $this->userId);
        return $this->success('标记成功');
    }

    public function markAllRead()
    {
        if (!$this->userId) {
            return $this->fail('请先登录');
        }
        NoticeLogic::markAllRead($this->userId);
        return $this->success('全部标记成功');
    }
}
