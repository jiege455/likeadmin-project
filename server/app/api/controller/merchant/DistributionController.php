<?php
namespace app\api\controller\merchant;

use app\api\controller\BaseApiController;
use app\api\logic\merchant\DistributionLogic;

class DistributionController extends BaseApiController
{
    public function getSetting()
    {
        $result = DistributionLogic::getSetting($this->userId);
        return $this->data($result);
    }

    public function setSetting()
    {
        $params = $this->request->post();
        $result = DistributionLogic::setSetting($this->userId, $params);
        if ($result === false) {
            return $this->fail(DistributionLogic::getError());
        }
        return $this->success('保存成功');
    }
}
