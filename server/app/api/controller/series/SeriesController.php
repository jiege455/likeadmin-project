<?php
namespace app\api\controller\series;

use app\api\controller\BaseApiController;
use app\api\logic\series\SeriesLogic;

class SeriesController extends BaseApiController
{
    public function lists()
    {
        $result = SeriesLogic::lists($this->request->get());
        return $this->data($result);
    }

    public function detail()
    {
        $id = $this->request->get('id');
        $result = SeriesLogic::detail($id, $this->userId);
        return $this->data($result);
    }
}
