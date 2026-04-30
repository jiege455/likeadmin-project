<?php
namespace app\api\controller\distribution;

use app\api\controller\BaseApiController;
use app\api\logic\distribution\DistributionLogic;

class IndexController extends BaseApiController
{
    public function info()
    {
        $result = DistributionLogic::getInfo($this->userId);
        return $this->data($result);
    }

    public function orders()
    {
        $result = DistributionLogic::orders($this->userId, $this->request->get());
        return $this->data($result);
    }
}
