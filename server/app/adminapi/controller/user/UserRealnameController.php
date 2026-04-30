<?php
namespace app\adminapi\controller\user;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\user\UserRealnameLogic;

class UserRealnameController extends BaseAdminController
{
    /**
     * @notes 实名记录列表
     */
    public function lists()
    {
        $params = (array)$this->request->get();
        $result = UserRealnameLogic::lists($params);
        return $this->data($result);
    }

    /**
     * @notes 审核
     */
    public function audit()
    {
        $params = (array)$this->request->post();
        $result = UserRealnameLogic::audit($params);
        if ($result === false) {
            return $this->fail(UserRealnameLogic::getError());
        }
        return $this->success('审核成功');
    }

    /**
     * @notes 获取配置
     */
    public function getConfig()
    {
        $result = UserRealnameLogic::getConfig();
        return $this->data($result);
    }

    /**
     * @notes 保存配置
     */
    public function setConfig()
    {
        $params = (array)$this->request->post();
        $result = UserRealnameLogic::setConfig($params);
        return $this->success('保存成功');
    }
}
