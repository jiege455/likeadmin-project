<?php
namespace app\adminapi\controller\setting;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\setting\WithdrawSettingLogic;

class WithdrawController extends BaseAdminController
{
    public function getConfig()
    {
        return $this->data(WithdrawSettingLogic::getConfig());
    }

    public function setConfig()
    {
        $params = $this->request->post();
        WithdrawSettingLogic::setConfig($params);
        return $this->success('保存成功');
    }
}
