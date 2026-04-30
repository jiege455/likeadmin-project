<?php
namespace app\api\controller\series;

use app\api\controller\BaseApiController;
use app\api\logic\series\IssueLogic;

class IssueController extends BaseApiController
{
    public function read()
    {
        $id = $this->request->get('id');
        $result = IssueLogic::read($id, $this->userId);
        return $this->data($result);
    }
}
