<?php

namespace app\api\controller;

use app\api\controller\BaseApiController;
use app\api\logic\EmailLogic;
use think\response\Json;

class EmailController extends BaseApiController
{
    public array $notNeedLogin = ['sendCode', 'verify'];

    public function sendCode(): Json
    {
        $params = $this->request->post();
        $result = EmailLogic::sendCode($params);
        if (true === $result) {
            return $this->success('发送成功');
        }
        return $this->fail($result);
    }

    public function verify(): Json
    {
        $params = $this->request->post();
        $result = EmailLogic::verify($params);
        if (true === $result) {
            return $this->success('验证成功');
        }
        return $this->fail($result);
    }
}
