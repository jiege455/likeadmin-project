<?php
namespace app\adminapi\controller\marketing;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\marketing\CouponLogic;
use app\adminapi\validate\marketing\CouponValidate;

class CouponController extends BaseAdminController
{
    public function lists()
    {
        return $this->data(CouponLogic::lists($this->request->get()));
    }

    public function add()
    {
        $params = (new CouponValidate())->post()->goCheck('add');
        CouponLogic::add($params);
        return $this->success('添加成功');
    }

    public function edit()
    {
        $params = (new CouponValidate())->post()->goCheck('edit');
        CouponLogic::edit($params);
        return $this->success('编辑成功');
    }

    public function del()
    {
        $params = (new CouponValidate())->post()->goCheck('del');
        CouponLogic::del($params['id']);
        return $this->success('删除成功');
    }
    
    public function detail()
    {
        $id = $this->request->get('id');
        return $this->data(CouponLogic::detail($id));
    }
}
