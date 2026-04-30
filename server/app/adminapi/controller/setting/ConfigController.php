<?php
namespace app\adminapi\controller\setting;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\setting\ConfigLogic;

/**
 * 系统配置控制器
 */
class ConfigController extends BaseAdminController
{
    /**
     * @notes 获取抽成配置
     */
    public function getCommissionConfig()
    {
        $result = ConfigLogic::getCommissionConfig();
        return $this->data($result);
    }

    /**
     * @notes 设置抽成配置
     */
    public function setCommissionConfig()
    {
        $params = $this->request->post();
        ConfigLogic::setCommissionConfig($params);
        return $this->success('操作成功');
    }

    /**
     * @notes 获取邮箱配置
     */
    public function getSmtpConfig()
    {
        $result = ConfigLogic::getSmtpConfig();
        return $this->data($result);
    }

    /**
     * @notes 设置邮箱配置
     */
    public function setSmtpConfig()
    {
        $params = $this->request->post();
        ConfigLogic::setSmtpConfig($params);
        return $this->success('操作成功');
    }
}
