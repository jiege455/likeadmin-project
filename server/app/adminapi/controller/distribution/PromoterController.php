<?php
namespace app\adminapi\controller\distribution;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\distribution\PromoterLists;

class PromoterController extends BaseAdminController
{
    public function lists()
    {
        return $this->dataLists(new PromoterLists());
    }
}
