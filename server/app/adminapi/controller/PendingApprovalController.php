<?php
/**
 * 待处理审批管理控制器
 * 开发者：杰哥网络科技 qq2711793818 杰哥
 */

namespace app\adminapi\controller;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\PendingApprovalLists;
use app\adminapi\logic\PendingApprovalLogic;

class PendingApprovalController extends BaseAdminController
{
    /**
     * @notes 获取待处理审批列表
     * @return \think\response\Json
     */
    public function lists()
    {
        return $this->dataLists(new PendingApprovalLists());
    }

    /**
     * @notes 获取待处理审批统计
     * @return \think\response\Json
     */
    public function statistics()
    {
        $result = PendingApprovalLogic::statistics();
        return $this->data($result);
    }

    /**
     * @notes 快捷审批
     * @return \think\response\Json
     */
    public function audit()
    {
        $params = $this->request->post();
        $result = PendingApprovalLogic::audit($params);
        if ($result === false) {
            return $this->fail(PendingApprovalLogic::getError());
        }
        return $this->success('审批成功');
    }

    /**
     * @notes 获取审批详情
     * @return \think\response\Json
     */
    public function detail()
    {
        $params = $this->request->get();
        $result = PendingApprovalLogic::detail($params);
        if ($result === false) {
            return $this->fail(PendingApprovalLogic::getError());
        }
        return $this->data($result);
    }
}
