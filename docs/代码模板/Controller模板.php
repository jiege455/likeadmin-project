<?php

namespace app\adminapi\controller\{{module}};

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\{{module}}\{{name}}Lists;
use app\adminapi\logic\{{module}}\{{name}}Logic;
use app\adminapi\validate\{{module}}\{{name}}Validate;

/**
 * {{title}}控制器
 * @author 杰哥
 * @date {{date}}
 */
class {{name}}Controller extends BaseAdminController
{
    /**
     * @notes 获取{{title}}列表
     * @return \think\response\Json
     * @author 杰哥
     * @date {{date}}
     */
    public function lists()
    {
        return $this->dataLists(new {{name}}Lists());
    }

    /**
     * @notes 获取{{title}}详情
     * @return \think\response\Json
     * @author 杰哥
     * @date {{date}}
     */
    public function detail()
    {
        $params = (new {{name}}Validate())->goCheck('detail');
        $result = {{name}}Logic::detail($params['id']);
        return $this->success('获取成功', $result);
    }

    /**
     * @notes 添加{{title}}
     * @return \think\response\Json
     * @author 杰哥
     * @date {{date}}
     */
    public function add()
    {
        $params = (new {{name}}Validate())->post()->goCheck('add');
        $result = {{name}}Logic::add($params);
        if ($result === false) {
            return $this->fail({{name}}Logic::getError());
        }
        return $this->success('添加成功', [], 1, 1);
    }

    /**
     * @notes 编辑{{title}}
     * @return \think\response\Json
     * @author 杰哥
     * @date {{date}}
     */
    public function edit()
    {
        $params = (new {{name}}Validate())->post()->goCheck('edit');
        $result = {{name}}Logic::edit($params);
        if ($result === false) {
            return $this->fail({{name}}Logic::getError());
        }
        return $this->success('编辑成功', [], 1, 1);
    }

    /**
     * @notes 删除{{title}}
     * @return \think\response\Json
     * @author 杰哥
     * @date {{date}}
     */
    public function delete()
    {
        $params = (new {{name}}Validate())->post()->goCheck('delete');
        {{name}}Logic::delete($params['id']);
        return $this->success('删除成功', [], 1, 1);
    }

    /**
     * @notes 切换{{title}}状态
     * @return \think\response\Json
     * @author 杰哥
     * @date {{date}}
     */
    public function status()
    {
        $params = (new {{name}}Validate())->post()->goCheck('status');
        {{name}}Logic::status($params['id']);
        return $this->success('操作成功', [], 1, 1);
    }
}
