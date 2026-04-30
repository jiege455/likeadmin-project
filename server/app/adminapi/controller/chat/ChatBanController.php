<?php
/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * 后台禁言管理控制器
 */
namespace app\adminapi\controller\chat;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\chat\ChatBanLists;
use app\common\model\chat\ChatBan;
use app\common\model\user\User;
use app\common\model\merchant\Merchant;

class ChatBanController extends BaseAdminController
{
    public function lists()
    {
        return $this->dataLists(new ChatBanLists());
    }

    public function add()
    {
        $userId = $this->request->post('user_id', 0);
        $userType = $this->request->post('user_type', 1);
        $banType = $this->request->post('ban_type', 1);
        $reason = $this->request->post('reason', '');
        $expireTime = $this->request->post('expire_time', '');
        $duration = $this->request->post('duration', 0);

        if ($userId <= 0) {
            return $this->fail('请选择要禁言的用户');
        }

        if (empty($reason)) {
            return $this->fail('请填写禁言原因');
        }

        if ($userType == ChatBan::USER_TYPE_USER) {
            $user = User::find($userId);
            if (!$user) {
                return $this->fail('用户不存在');
            }
        } else {
            $merchant = Merchant::find($userId);
            if (!$merchant) {
                return $this->fail('商家不存在');
            }
        }

        $expireTimestamp = null;
        if ($duration > 0) {
            $expireTimestamp = time() + ($duration * 3600);
        } elseif (!empty($expireTime)) {
            $expireTimestamp = strtotime($expireTime);
            if ($expireTimestamp === false) {
                return $this->fail('到期时间格式错误');
            }
        }

        $result = ChatBan::addBan(
            $userId,
            $userType,
            $banType,
            $reason,
            $this->adminId,
            $expireTimestamp
        );

        if ($result) {
            return $this->success('禁言成功');
        }

        return $this->fail('禁言失败');
    }

    public function cancel()
    {
        $id = $this->request->post('id', 0);
        
        if ($id <= 0) {
            return $this->fail('参数错误');
        }

        $result = ChatBan::cancelBan($id, $this->adminId);

        if ($result) {
            return $this->success('解除禁言成功');
        }

        return $this->fail('解除禁言失败');
    }

    public function check()
    {
        $userId = $this->request->get('user_id', 0);
        $userType = $this->request->get('user_type', 1);

        if ($userId <= 0) {
            return $this->fail('参数错误');
        }

        $banInfo = ChatBan::getBanInfo($userId, $userType);

        return $this->success('获取成功', [
            'is_banned' => $banInfo !== null,
            'ban_info' => $banInfo
        ]);
    }

    public function detail()
    {
        $id = $this->request->get('id', 0);
        
        if ($id <= 0) {
            return $this->fail('参数错误');
        }

        $ban = ChatBan::find($id);
        if (!$ban) {
            return $this->fail('记录不存在');
        }

        $data = $ban->toArray();
        $data['user_type_text'] = ChatBan::getUserTypeText($ban->user_type);
        $data['ban_type_text'] = ChatBan::getBanTypeText($ban->ban_type);

        if ($ban->user_type == ChatBan::USER_TYPE_USER) {
            $user = User::find($ban->user_id);
            $data['user_info'] = $user ? [
                'id' => $user->id,
                'nickname' => $user->nickname,
                'avatar' => $user->avatar
            ] : null;
        } else {
            $merchant = Merchant::find($ban->user_id);
            $data['user_info'] = $merchant ? [
                'id' => $merchant->id,
                'name' => $merchant->name,
                'logo' => $merchant->logo
            ] : null;
        }

        return $this->success('获取成功', $data);
    }
}
