<?php
namespace app\api\controller\merchant;

use app\api\controller\BaseApiController;
use app\api\lists\merchant\FollowLists;
use app\api\logic\merchant\FollowLogic;

/**
 * 商家关注控制器
 * 开发者：杰哥网络科技 qq2711793818 杰哥
 */
class FollowController extends BaseApiController
{
    public array $notNeedLogin = ['lists'];

    public function lists()
    {
        return $this->dataLists(new FollowLists());
    }

    public function toggle()
    {
        $params = $this->request->post();
        if (empty($params['merchant_id'])) {
            return $this->fail('参数错误');
        }
        $result = FollowLogic::toggle($this->userId, $params['merchant_id']);
        if ($result === true) {
            return $this->success('操作成功');
        }
        return $this->fail($result);
    }

    public function setCurrent()
    {
        $params = $this->request->post();
        if (empty($params['merchant_id'])) {
            return $this->fail('参数错误');
        }
        $result = FollowLogic::setCurrentMerchant($this->userId, $params['merchant_id']);
        if ($result === true) {
            return $this->success('切换成功');
        }
        return $this->fail($result);
    }

    public function current()
    {
        $merchant = FollowLogic::getCurrentMerchant($this->userId);
        return $this->data($merchant);
    }

    public function followed()
    {
        $list = FollowLogic::getFollowedMerchants($this->userId);
        return $this->data($list);
    }

    public function togglePush()
    {
        $params = $this->request->post();
        if (empty($params['merchant_id'])) {
            return $this->fail('参数错误');
        }
        $result = FollowLogic::togglePush($this->userId, $params['merchant_id']);
        if ($result === true) {
            return $this->success('操作成功');
        }
        return $this->fail($result);
    }
}
