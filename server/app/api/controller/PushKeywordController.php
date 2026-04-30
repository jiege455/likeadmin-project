<?php
/**
 * 推送关键词控制器
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
namespace app\api\controller;

use app\api\controller\BaseApiController;
use app\api\validate\PushKeywordValidate;
use app\api\logic\push\PushKeywordLogic;
use app\api\lists\push\PushKeywordLists;

class PushKeywordController extends BaseApiController
{
    public array $notNeedLogin = [];

    public function lists()
    {
        $params = (new PushKeywordValidate())->get()->goCheck('lists');
        $result = PushKeywordLogic::lists($this->userId, $params['merchant_id']);
        return $this->data($result);
    }

    public function add()
    {
        $params = (new PushKeywordValidate())->post()->goCheck('add');
        $result = PushKeywordLogic::add(
            $this->userId,
            $params['merchant_id'],
            $params['keyword']
        );
        
        if ($result !== true) {
            return $this->fail($result);
        }
        
        return $this->success('添加成功');
    }

    public function edit()
    {
        $params = (new PushKeywordValidate())->post()->goCheck('edit');
        $result = PushKeywordLogic::edit(
            $this->userId,
            $params['id'],
            $params['keyword']
        );
        
        if ($result !== true) {
            return $this->fail($result);
        }
        
        return $this->success('修改成功');
    }

    public function delete()
    {
        $params = (new PushKeywordValidate())->post()->goCheck('delete');
        $result = PushKeywordLogic::delete($this->userId, $params['id']);
        
        if ($result !== true) {
            return $this->fail($result);
        }
        
        return $this->success('删除成功');
    }

    public function toggle()
    {
        $params = (new PushKeywordValidate())->post()->goCheck('toggle');
        $result = PushKeywordLogic::toggle($this->userId, $params['id']);
        
        if ($result !== true) {
            return $this->fail($result);
        }
        
        return $this->success('操作成功');
    }
}
