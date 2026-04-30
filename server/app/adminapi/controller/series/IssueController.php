<?php
/**
 * 期次管理控制器
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
namespace app\adminapi\controller\series;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\series\IssueLists;
use app\adminapi\logic\series\IssueLogic;

class IssueController extends BaseAdminController
{
    public function lists()
    {
        return $this->dataLists(new IssueLists());
    }

    public function add()
    {
        $params = $this->request->post();
        $result = IssueLogic::add($params);
        if ($result === false) {
            return $this->fail(IssueLogic::getError());
        }
        return $this->success('添加成功');
    }

    public function edit()
    {
        $params = $this->request->post();
        $result = IssueLogic::edit($params);
        if ($result === false) {
            return $this->fail(IssueLogic::getError());
        }
        return $this->success('编辑成功');
    }

    public function detail()
    {
        $params = $this->request->get();
        $result = IssueLogic::detail($params);
        return $this->data($result);
    }

    public function delete()
    {
        $params = $this->request->post();
        $result = IssueLogic::delete($params);
        if ($result === false) {
            return $this->fail(IssueLogic::getError());
        }
        return $this->success('删除成功');
    }

    public function publish()
    {
        $params = $this->request->post();
        $result = IssueLogic::publish($params);
        if ($result === false) {
            return $this->fail(IssueLogic::getError());
        }
        return $this->success('发布成功');
    }
}
