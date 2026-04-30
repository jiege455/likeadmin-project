<?php
namespace app\adminapi\controller\finance;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\finance\PlatformProfitLogic;

class ProfitController extends BaseAdminController
{
    public function statistics()
    {
        return $this->data(PlatformProfitLogic::statistics());
    }

    public function trend()
    {
        return $this->data(PlatformProfitLogic::trend($this->request->get()));
    }

    public function merchantProfit()
    {
        return $this->data(PlatformProfitLogic::merchantProfit($this->request->get()));
    }

    public function settleList()
    {
        return $this->data(PlatformProfitLogic::settleList($this->request->get()));
    }

    public function settle()
    {
        $params = $this->request->post();
        PlatformProfitLogic::settle($params);
        return $this->success('结算成功');
    }
}
