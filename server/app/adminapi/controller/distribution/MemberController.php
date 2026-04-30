<?php
namespace app\adminapi\controller\distribution;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\distribution\DistributionLogic;

class MemberController extends BaseAdminController
{
    public function lists()
    {
        return $this->data(DistributionLogic::memberLists($this->request->get()));
    }

    public function statistics()
    {
        return $this->data(DistributionLogic::statistics());
    }
}
