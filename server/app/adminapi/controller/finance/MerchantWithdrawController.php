<?php
namespace app\adminapi\controller\finance;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\finance\MerchantWithdrawLogic;
use app\adminapi\validate\finance\MerchantWithdrawValidate;

class MerchantWithdrawController extends BaseAdminController
{
    public function lists()
    {
        $params = (array)$this->request->get();
        $result = MerchantWithdrawLogic::lists($params);
        return $this->data($result);
    }

    public function detail()
    {
        $id = $this->request->get('id');
        $result = MerchantWithdrawLogic::detail($id);
        return $this->data($result);
    }

    public function audit()
    {
        $params = (array)$this->request->post();
        if (empty($params['id']) || !isset($params['status'])) {
            return $this->fail('参数错误');
        }
        $result = MerchantWithdrawLogic::audit($params);
        if ($result === false) {
            return $this->fail(MerchantWithdrawLogic::getError());
        }
        return $this->success('操作成功');
    }

    public function statistics()
    {
        return $this->data(MerchantWithdrawLogic::statistics());
    }
}
