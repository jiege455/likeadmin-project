<?php
namespace app\adminapi\controller\setting;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\setting\MerchantSettingLogic;

class MerchantController extends BaseAdminController
{
    public function getConfig()
    {
        return $this->data(MerchantSettingLogic::getConfig());
    }

    public function setConfig()
    {
        $params = $this->request->post();
        $result = MerchantSettingLogic::setConfig($params);
        if ($result === false) {
            return $this->fail(MerchantSettingLogic::getError());
        }
        return $this->success('保存成功');
    }
}
