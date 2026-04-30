<?php

namespace app\api\controller;

use app\api\logic\DistributionLogic;
use app\api\validate\DistributionApplyValidate;
use think\facade\Db;

class DistributionController extends BaseApiController
{
    /**
     * 检查分销员身份
     */
    private function checkDistributor()
    {
        $user = Db::name('user')->where('id', $this->userId)->find();
        if (empty($user['is_distributor'])) {
            return false;
        }
        return true;
    }

    /**
     * 分销中心首页数据
     */
    public function index()
    {
        if (!$this->checkDistributor()) {
             return $this->fail('请先申请成为分销员', [], 20001);
        }

        $userId = $this->userId;
        $user = Db::name('user')->where('id', $userId)->find();

        // 统计邀请人数
        $todayInviteCount = Db::name('user')
            ->where('inviter_id', $userId)
            ->whereDay('create_time')
            ->count();
            
        $yesterdayInviteCount = Db::name('user')
            ->where('inviter_id', $userId)
            ->whereDay('create_time', 'yesterday')
            ->count();

        $totalInviteCount = Db::name('user')
            ->where('inviter_id', $userId)
            ->count();

        $inviteCode = !empty($user['sn']) ? $user['sn'] : $user['id'];
        $domain = $this->request->domain();
        // 构造H5分享链接
        $shareUrl = $domain . '/mobile/#/pages/index/index?invite_code=' . $inviteCode;

        return $this->data([
            'commission' => $user['earnings'] ?? '0.00', // 可提现佣金
            'total_commission' => $user['earnings'] ?? '0.00', // 累计佣金
            'today_commission' => '0.00', // 今日佣金
            'month_commission' => '0.00', // 本月佣金
            'order_count' => 0, // 推广订单数
            'today_invite_count' => $todayInviteCount, // 今日邀请
            'yesterday_invite_count' => $yesterdayInviteCount, // 昨日邀请
            'total_invite_count' => $totalInviteCount, // 累计邀请
            'invite_code' => $inviteCode, // 邀请码
            'share_url' => $shareUrl, // 完整的分享链接
        ]);
    }

    /**
     * 获取推广海报信息
     */
    public function poster()
    {
        if (!$this->checkDistributor()) {
             return $this->fail('请先申请成为分销员', [], 20001);
        }

        $userId = $this->userId;
        $user = Db::name('user')->where('id', $userId)->find();
        $inviteCode = !empty($user['sn']) ? $user['sn'] : $user['id'];
        $domain = $this->request->domain();
        
        // 完整的H5链接
        $inviteLink = $domain . '/mobile/#/pages/index/index?invite_code=' . $inviteCode;
        
        return $this->data([
            'invite_link' => $inviteLink,
            'invite_code' => $inviteCode,
            'poster_bg' => '', // 可以在后台配置默认背景图
        ]);
    }

    /**
     * 分销记录
     */
    public function lists()
    {
        if (!$this->checkDistributor()) {
             return $this->fail('请先申请成为分销员', [], 20001);
        }

        $userId = $this->userId;
        $pageNo = $this->request->get('page_no/d', 1);
        $pageSize = $this->request->get('page_size/d', 10);

        $list = Db::name('user')
            ->where('inviter_id', $userId)
            ->field('id, nickname, avatar, create_time')
            ->order('create_time', 'desc')
            ->page($pageNo, $pageSize)
            ->select()
            ->each(function ($item) {
                $item['avatar'] = empty($item['avatar']) ? '' : get_file_url($item['avatar']);
                $item['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
                return $item;
            })
            ->toArray();

        $count = Db::name('user')
            ->where('inviter_id', $userId)
            ->count();

        return $this->data([
            'lists' => $list,
            'count' => $count,
            'page_no' => $pageNo,
            'page_size' => $pageSize
        ]);
    }

    /**
     * 申请成为分销员
     */
    public function apply()
    {
        $params = (new DistributionApplyValidate())->post()->goCheck();
        $result = DistributionLogic::apply($this->userId, $params);
        if ($result === false) {
            return $this->fail(DistributionLogic::getError());
        }
        return $this->success('申请提交成功');
    }

    /**
     * 获取申请详情/状态
     */
    public function applyDetail()
    {
        $result = DistributionLogic::getApplyStatus($this->userId);
        return $this->data($result);
    }

    /**
     * 获取佣金日志
     */
    public function commissionLog()
    {
        if (!$this->checkDistributor()) {
             return $this->fail('请先申请成为分销员', [], 20001);
        }

        $userId = $this->userId;
        $pageNo = $this->request->get('page_no/d', 1);
        $pageSize = $this->request->get('page_size/d', 10);

        $list = Db::name('user_account_log')
            ->where('user_id', $userId)
            ->where('change_type', 202)
            ->field('id, change_amount as money, create_time, remark as type_desc')
            ->order('create_time', 'desc')
            ->page($pageNo, $pageSize)
            ->select()
            ->each(function ($item) {
                $item['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
                $item['money'] = abs($item['money']);
                return $item;
            })
            ->toArray();

        $count = Db::name('user_account_log')
            ->where('user_id', $userId)
            ->where('change_type', 202)
            ->count();

        return $this->data([
            'lists' => $list,
            'count' => $count,
            'page_no' => $pageNo,
            'page_size' => $pageSize
        ]);
    }
}
