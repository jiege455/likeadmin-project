<?php
namespace app\adminapi\controller\user;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\user\InviteLists;

/**
 * 邀请管理控制器
 */
class InviteController extends BaseAdminController
{
    /**
     * @notes 邀请记录
     */
    public function lists()
    {
        return $this->dataLists(new InviteLists());
    }
}
