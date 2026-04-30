<?php
namespace app\adminapi\controller\merchant;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\merchant\ApplyLists;
use app\adminapi\logic\merchant\ApplyLogic;

/**
 * 商家入驻申请控制器
 */
class ApplyController extends BaseAdminController
{
    /**
     * @notes 申请列表
     */
    public function lists()
    {
        return $this->dataLists(new ApplyLists());
    }

    /**
     * @notes 审核申请
     */
    public function audit()
    {
        $params = $this->request->post();
        // 简单验证
        if (!isset($params['id']) || !isset($params['status'])) {
            return $this->fail('参数错误');
        }
        $result = ApplyLogic::audit($params);
        if ($result) {
            return $this->success('操作成功');
        }
        return $this->fail(ApplyLogic::getError());
    }

    /**
     * @notes 删除申请
     */
    public function delete()
    {
        $params = (array)$this->request->post();
        $result = ApplyLogic::delete($params);
        if ($result) {
            return $this->success('删除成功');
        }
        return $this->fail(ApplyLogic::getError());
    }
}
