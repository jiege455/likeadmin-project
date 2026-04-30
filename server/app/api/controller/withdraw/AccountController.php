<?php
namespace app\api\controller\withdraw;

use app\api\controller\BaseApiController;
use app\api\logic\withdraw\AccountLogic;

/**
 * 提现账户控制器
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
class AccountController extends BaseApiController
{
    public function lists()
    {
        $result = AccountLogic::lists($this->userId, $this->request->get());
        return $this->data($result);
    }

    public function add()
    {
        $params = $this->request->post();
        $result = AccountLogic::add($this->userId, $params);
        if ($result === false) {
            return $this->fail(AccountLogic::getError());
        }
        return $this->success('添加成功');
    }

    public function edit()
    {
        $params = $this->request->post();
        $result = AccountLogic::edit($this->userId, $params);
        if ($result === false) {
            return $this->fail(AccountLogic::getError());
        }
        return $this->success('修改成功');
    }

    public function delete()
    {
        $id = $this->request->post('id');
        $merchantId = intval($this->request->post('merchant_id', 0));
        $result = AccountLogic::delete($this->userId, $id, $merchantId);
        if ($result === false) {
            return $this->fail(AccountLogic::getError());
        }
        return $this->success('删除成功');
    }

    public function setDefault()
    {
        $id = $this->request->post('id');
        $merchantId = intval($this->request->post('merchant_id', 0));
        $result = AccountLogic::setDefault($this->userId, $id, $merchantId);
        if ($result === false) {
            return $this->fail(AccountLogic::getError());
        }
        return $this->success('设置成功');
    }

    public function detail()
    {
        $id = $this->request->get('id');
        $result = AccountLogic::detail($this->userId, $id);
        return $this->data($result);
    }

    public function methods()
    {
        $result = AccountLogic::getEnabledMethods();
        return $this->data($result);
    }
}
