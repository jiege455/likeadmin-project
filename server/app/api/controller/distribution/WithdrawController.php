<?php
namespace app\api\controller\distribution;

use app\api\controller\BaseApiController;
use app\api\logic\distribution\DistributionLogic;

class WithdrawController extends BaseApiController
{
    public function apply()
    {
        $params = $this->request->post();
        $result = DistributionLogic::withdraw($this->userId, $params);
        if ($result === false) {
            return $this->fail(DistributionLogic::getError());
        }
        return $this->success('申请成功');
    }

    public function lists()
    {
        $result = DistributionLogic::withdrawList($this->userId, $this->request->get());
        return $this->data($result);
    }

    public function config()
    {
        $result = DistributionLogic::withdrawConfig();
        return $this->data($result);
    }
}
