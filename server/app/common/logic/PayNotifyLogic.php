<?php
// +----------------------------------------------------------------------
// | likeadmin快速开发前后端分离管理后台（PHP版）
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | gitee下载：https://gitee.com/likeshop_gitee/likeadmin
// | github下载：https://github.com/likeshop-github/likeadmin
// | 访问官网：https://www.likeadmin.cn
// | likeadmin团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeadminTeam
// +----------------------------------------------------------------------

namespace app\common\logic;

use app\common\enum\PayEnum;
use app\common\enum\user\AccountLogEnum;
use app\common\model\recharge\RechargeOrder;
use app\common\model\user\User;
use think\facade\Db;
use think\facade\Log;
use app\common\service\EmailService;

/**
 * 支付成功后处理订单状态
 * Class PayNotifyLogic
 * @package app\api\logic
 */
class PayNotifyLogic extends BaseLogic
{

    public static function handle($action, $orderSn, $extra = [])
    {
        Db::startTrans();
        try {
            self::$action($orderSn, $extra);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            Log::write(implode('-', [
                __CLASS__,
                __FUNCTION__,
                $e->getFile(),
                $e->getLine(),
                $e->getMessage()
            ]));
            self::setError($e->getMessage());
            return $e->getMessage();
        }
    }


    /**
     * @notes 充值回调
     * @param $orderSn
     * @param array $extra
     * @author 段誉
     * @date 2023/2/27 15:28
     */
    public static function recharge($orderSn, array $extra = [])
    {
        $order = RechargeOrder::where('sn', $orderSn)->findOrEmpty();
        if ($order->isEmpty()) {
            throw new \Exception('订单不存在');
        }
        
        // 检查订单是否已支付，防止重复处理
        if ($order->pay_status == PayEnum::ISPAID) {
            return true;
        }
        
        // 使用行锁防止并发
        $user = User::where('id', $order->user_id)->lock(true)->findOrEmpty();
        if ($user->isEmpty()) {
            throw new \Exception('用户不存在');
        }
        
        $user->total_recharge_amount = bcadd(strval($user->total_recharge_amount), strval($order->order_amount), 2);
        $user->user_money = bcadd(strval($user->user_money), strval($order->order_amount), 2);
        $user->save();

        // 记录账户流水
        AccountLogLogic::add(
            $order->user_id,
            AccountLogEnum::UM_INC_RECHARGE,
            AccountLogEnum::INC,
            $order->order_amount,
            $order->sn,
            '用户充值'
        );

        // 更新充值订单状态
        $order->transaction_id = $extra['transaction_id'] ?? '';
        $order->pay_status = PayEnum::ISPAID;
        $order->pay_time = time();
        $order->save();

        // 发送邮件通知管理员
        try {
            EmailNotifyLogic::sendToAdmin(
                '用户充值到账提醒',
                "用户充值成功！\n订单号：{$order->sn}\n金额：{$order->order_amount}元\n用户ID：{$order->user_id}"
            );
        } catch (\Exception $e) {
            Log::error('Recharge Email Notify Error: ' . $e->getMessage());
        }
    }

    public static function article($orderSn, array $extra = [])
    {
        $order = Db::name('article_order')->where('order_sn', $orderSn)->lock(true)->find();
        if (!$order) {
            throw new \Exception('订单不存在');
        }
        if ($order['pay_status'] == 1) {
            return true;
        }

        $userId = $order['user_id'];
        $inviter = Db::name('user')
            ->where('id', $userId)
            ->value('inviter_id');
        
        $distributorId = 0;
        if ($inviter && $inviter != $userId) {
            $distributorId = $inviter;
        }

        Db::name('article_order')->where('id', $order['id'])->update([
            'pay_status' => 1,
            'pay_time' => time(),
            'transaction_id' => $extra['transaction_id'] ?? '',
            'distributor_id' => $distributorId,
            'update_time' => time()
        ]);

        if (!empty($order['coupon_id'])) {
            Db::name('user_coupon')
                ->where('id', $order['coupon_id'])
                ->lock(true)
                ->update([
                    'status' => 1,
                    'use_time' => time(),
                    'update_time' => time()
                ]);
        }

        ProfitDistributionLogic::distribute($order['id']);

        try {
            $article = Db::name('article')->where('id', $order['article_id'])->field('title, merchant_id')->find();
            if ($article && $article['merchant_id']) {
                $orderInfo = [
                    'article_title' => $article['title'],
                    'order_sn' => $order['order_sn'],
                    'pay_price' => $order['order_amount'],
                    'create_time' => time()
                ];
                EmailNotifyLogic::sendOrderNotify($article['merchant_id'], $orderInfo);
            }
        } catch (\Exception $e) {
            Log::error('Order Email Notify Error: ' . $e->getMessage());
        }
    }


}
