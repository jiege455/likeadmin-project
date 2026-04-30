<?php
namespace app\adminapi\controller\merchant;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\merchant\MerchantLogic;
use app\adminapi\validate\merchant\MerchantValidate;

class MerchantController extends BaseAdminController
{
    public function lists()
    {
        return $this->data(MerchantLogic::lists($this->request->get()));
    }

    public function detail()
    {
        $id = $this->request->get('id');
        return $this->data(MerchantLogic::detail($id));
    }

    public function audit()
    {
        $params = (new MerchantValidate())->post()->goCheck('audit');
        MerchantLogic::audit($params);
        return $this->success('操作成功');
    }

    public function setStatus()
    {
        $params = (new MerchantValidate())->post()->goCheck('status');
        MerchantLogic::setStatus($params);
        return $this->success('操作成功');
    }

    public function statistics()
    {
        $id = $this->request->get('id');
        return $this->data(MerchantLogic::statistics($id));
    }

    public function articles()
    {
        $id = $this->request->get('id');
        return $this->data(MerchantLogic::articles($id, $this->request->get()));
    }

    public function orders()
    {
        $id = $this->request->get('id');
        return $this->data(MerchantLogic::orders($id, $this->request->get()));
    }

    public function edit()
    {
        $params = $this->request->post();
        $result = MerchantLogic::edit($params);
        if ($result) {
            return $this->success('编辑成功');
        }
        return $this->fail(MerchantLogic::getError());
    }
}
