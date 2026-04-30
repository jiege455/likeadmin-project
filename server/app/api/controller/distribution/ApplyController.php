<?php
namespace app\api\controller\distribution;

use app\api\controller\BaseApiController;
use app\api\logic\distribution\DistributionLogic;

class ApplyController extends BaseApiController
{
    public function status()
    {
        $result = DistributionLogic::getStatus($this->userId);
        return $this->data($result);
    }

    public function submit()
    {
        $params = $this->request->post();
        $result = DistributionLogic::apply($this->userId, $params);
        if ($result === false) {
            return $this->fail(DistributionLogic::getError());
        }
        return $this->success('申请成功，请等待审核');
    }

    public function info()
    {
        $result = DistributionLogic::getInfo($this->userId);
        return $this->data($result);
    }
}
