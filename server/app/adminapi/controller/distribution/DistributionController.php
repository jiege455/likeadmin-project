<?php
namespace app\adminapi\controller\distribution;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\distribution\DistributionLists;

/**
 * 分销管理控制器
 */
class DistributionController extends BaseAdminController
{
    /**
     * @notes 分销记录
     */
    public function lists()
    {
        return $this->dataLists(new DistributionLists());
    }
}
