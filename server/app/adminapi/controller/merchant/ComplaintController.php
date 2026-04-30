<?php
namespace app\adminapi\controller\merchant;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\merchant\ComplaintLists;
use app\adminapi\logic\merchant\ComplaintLogic;

class ComplaintController extends BaseAdminController
{
    /**
     * @notes 获取投诉列表
     * @return \think\response\Json
     */
    public function lists()
    {
        return $this->dataLists(new ComplaintLists());
    }

    /**
     * @notes 处理投诉
     * @return \think\response\Json
     */
    public function handle()
    {
        $params = $this->request->post();
        $result = ComplaintLogic::handle($params);
        if (false === $result) {
            return $this->fail(ComplaintLogic::getError());
        }
        return $this->success('操作成功');
    }

    /**
     * @notes 删除投诉
     * @return \think\response\Json
     */
    public function del()
    {
        $id = $this->request->post('id');
        $result = ComplaintLogic::del($id);
        if (false === $result) {
            return $this->fail(ComplaintLogic::getError());
        }
        return $this->success('操作成功');
    }
}
