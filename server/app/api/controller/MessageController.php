<?php
namespace app\api\controller;

use app\api\lists\notice\NoticeRecordLists;
use app\common\model\notice\NoticeRecord;

class MessageController extends BaseApiController
{
    // 获取业务通知列表
    public function lists()
    {
        return $this->dataLists(new NoticeRecordLists());
    }

    // 标记已读
    public function read()
    {
        $id = $this->request->post('id');
        NoticeRecord::where('id', $id)->where('user_id', $this->userId)->update(['read' => 1]);
        return $this->success();
    }
}
