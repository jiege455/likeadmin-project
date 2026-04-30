<?php
namespace app\adminapi\controller\finance;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\finance\MerchantFinanceLists;

/**
 * 商户资金控制器
 */
class MerchantFinanceController extends BaseAdminController
{
    /**
     * @notes 资金明细
     */
    public function lists()
    {
        return $this->dataLists(new MerchantFinanceLists());
    }
}
