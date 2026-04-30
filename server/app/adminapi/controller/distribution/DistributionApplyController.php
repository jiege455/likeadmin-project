<?php
namespace app\adminapi\controller\distribution;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\distribution\DistributionApplyLists;
use app\adminapi\logic\distribution\DistributionApplyLogic;

class DistributionApplyController extends BaseAdminController
{
    public function lists()
    {
        return $this->dataLists(new DistributionApplyLists());
    }

    public function audit()
    {
        $params = $this->request->post();
        $result = DistributionApplyLogic::audit($params);
        if ($result === true) {
            return $this->success('操作成功');
        }
        return $this->fail(DistributionApplyLogic::getError());
    }

    public function delete()
    {
        $params = $this->request->post();
        $result = DistributionApplyLogic::delete($params);
        if ($result === true) {
            return $this->success('删除成功');
        }
        return $this->fail(DistributionApplyLogic::getError());
    }
}
