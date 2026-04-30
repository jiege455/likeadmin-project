<?php
/**
 * 系列管理控制器
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
namespace app\adminapi\controller\series;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\series\SeriesLists;
use app\adminapi\logic\series\SeriesLogic;

class SeriesController extends BaseAdminController
{
    public function lists()
    {
        return $this->dataLists(new SeriesLists());
    }

    public function add()
    {
        $params = $this->request->post();
        $result = SeriesLogic::add($params);
        if ($result === false) {
            return $this->fail(SeriesLogic::getError());
        }
        return $this->success('添加成功');
    }

    public function edit()
    {
        $params = $this->request->post();
        $result = SeriesLogic::edit($params);
        if ($result === false) {
            return $this->fail(SeriesLogic::getError());
        }
        return $this->success('编辑成功');
    }

    public function detail()
    {
        $params = $this->request->get();
        $result = SeriesLogic::detail($params);
        return $this->data($result);
    }

    public function delete()
    {
        $params = $this->request->post();
        $result = SeriesLogic::delete($params);
        if ($result === false) {
            return $this->fail(SeriesLogic::getError());
        }
        return $this->success('删除成功');
    }

    public function status()
    {
        $params = $this->request->post();
        $result = SeriesLogic::status($params);
        if ($result === false) {
            return $this->fail(SeriesLogic::getError());
        }
        return $this->success('操作成功');
    }
}
