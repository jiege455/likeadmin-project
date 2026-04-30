<?php
namespace app\adminapi\controller\merchant;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\merchant\WithdrawLogic;

class WithdrawController extends BaseAdminController
{
    public function lists()
    {
        return $this->data(WithdrawLogic::lists($this->request->get()));
    }

    public function detail()
    {
        $id = $this->request->get('id');
        return $this->data(WithdrawLogic::detail($id));
    }

    public function statistics()
    {
        return $this->data(WithdrawLogic::statistics());
    }

    public function audit()
    {
        $params = $this->request->post();
        $result = WithdrawLogic::audit($params);
        if ($result === false) {
            return $this->fail(WithdrawLogic::getError());
        }
        return $this->success('操作成功');
    }
}
