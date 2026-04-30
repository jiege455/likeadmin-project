<?php
namespace app\api\controller\merchant;

use app\api\controller\BaseApiController;
use app\api\logic\merchant\WithdrawLogic;

class WithdrawController extends BaseApiController
{
    /**
     * @notes 申请提现
     * @return \think\response\Json
     * @author 杰哥
     * @date 2026-02-01
     */
    public function apply()
    {
        $params = (array)$this->request->post();
        $result = WithdrawLogic::apply($this->userId, $params);
        if ($result === false) {
            return $this->fail(WithdrawLogic::getError());
        }
        return $this->success('申请成功', $result);
    }

    /**
     * @notes 提现列表
     * @return \think\response\Json
     * @author 杰哥
     * @date 2026-02-01
     */
    public function lists()
    {
        $params = (array)$this->request->get();
        $result = WithdrawLogic::lists($this->userId, $params);
        return $this->data($result);
    }

    /**
     * @notes 提现详情
     * @return \think\response\Json
     * @author 杰哥
     * @date 2026-02-01
     */
    public function detail()
    {
        $id = $this->request->get('id');
        $result = WithdrawLogic::detail($this->userId, $id);
        return $this->data($result);
    }

    /**
     * @notes 提现配置
     * @return \think\response\Json
     * @author 杰哥
     * @date 2026-02-01
     */
    public function config()
    {
        $result = WithdrawLogic::config();
        return $this->data($result);
    }

    /**
     * @notes 商家信息
     * @return \think\response\Json
     * @author 杰哥
     * @date 2026-02-01
     */
    public function info()
    {
        $result = WithdrawLogic::info($this->userId);
        return $this->data($result);
    }
}
