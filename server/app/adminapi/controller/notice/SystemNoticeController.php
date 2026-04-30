<?php
namespace app\adminapi\controller\notice;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\notice\SystemNoticeLogic;

class SystemNoticeController extends BaseAdminController
{
    public function lists()
    {
        $page = $this->request->get('page_no', 1);
        $size = $this->request->get('page_size', 15);
        $offset = ($page - 1) * $size;
        $result = SystemNoticeLogic::lists($offset, $size);
        return $this->data($result);
    }

    public function add()
    {
        $params = $this->request->post();
        SystemNoticeLogic::add($params);
        return $this->success('发布成功');
    }

    public function edit()
    {
        $params = $this->request->post();
        SystemNoticeLogic::edit($params);
        return $this->success('编辑成功');
    }

    public function detail()
    {
        $id = $this->request->get('id/d');
        $result = SystemNoticeLogic::detail($id);
        return $this->data($result);
    }

    public function delete()
    {
        $params = $this->request->post();
        SystemNoticeLogic::delete($params);
        return $this->success('删除成功');
    }
}
