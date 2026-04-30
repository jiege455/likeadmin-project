<?php

namespace app\adminapi\controller\setting;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\setting\EmailLogic;
use think\response\Json;

class EmailController extends BaseAdminController
{
    public function config(): Json
    {
        $result = EmailLogic::getConfig();
        return $this->data($result);
    }

    public function setConfig(): Json
    {
        $params = $this->request->post();
        $result = EmailLogic::setConfig($params);
        if (true === $result) {
            return $this->success('保存成功');
        }
        return $this->fail($result);
    }

    public function test(): Json
    {
        $params = $this->request->post();
        $result = EmailLogic::sendTest($params);
        if (true === $result) {
            return $this->success('测试邮件发送成功');
        }
        return $this->fail($result);
    }

    public function getSwitchConfig(): Json
    {
        $result = EmailLogic::getSwitchConfig();
        return $this->data($result);
    }

    public function setSwitchConfig(): Json
    {
        $params = $this->request->post();
        $result = EmailLogic::setSwitchConfig($params);
        if (true === $result) {
            return $this->success('保存成功');
        }
        return $this->fail($result);
    }
}
