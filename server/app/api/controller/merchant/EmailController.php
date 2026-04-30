<?php

namespace app\api\controller\merchant;

use app\api\controller\BaseApiController;
use app\api\logic\merchant\EmailLogic;
use think\response\Json;

class EmailController extends BaseApiController
{
    public function info(): Json
    {
        $result = EmailLogic::info($this->userId);
        return $this->data($result);
    }

    public function bind(): Json
    {
        $params = $this->request->post();
        $result = EmailLogic::bind($this->userId, $params);
        if (true === $result) {
            return $this->success('绑定成功');
        }
        return $this->fail($result);
    }

    public function updateNotify(): Json
    {
        $params = $this->request->post();
        $result = EmailLogic::updateNotify($this->userId, $params);
        if (true === $result) {
            return $this->success('设置成功');
        }
        return $this->fail($result);
    }
}
