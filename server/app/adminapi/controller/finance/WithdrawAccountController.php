<?php
namespace app\adminapi\controller\finance;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\finance\WithdrawAccountLogic;

class WithdrawAccountController extends BaseAdminController
{
    public function lists()
    {
        return $this->data(WithdrawAccountLogic::lists($this->request->get()));
    }

    public function detail()
    {
        $id = $this->request->get('id');
        return $this->data(WithdrawAccountLogic::detail($id));
    }

    public function setStatus()
    {
        $params = $this->request->post();
        $result = WithdrawAccountLogic::setStatus($params);
        if ($result === false) {
            return $this->fail(WithdrawAccountLogic::getError());
        }
        return $this->success('操作成功');
    }
}
