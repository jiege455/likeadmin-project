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
use app\common\enum\YesNoEnum;
use app\common\enum\user\AccountLogEnum;
use app\common\model\pay\PayWay;
use app\common\model\recharge\RechargeOrder;
use app\common\model\article\ArticleOrder;
use app\common\model\user\User;
use app\common\service\pay\AliPayService;
use app\common\service\pay\WeChatPayService;
use app\common\service\pay\RainbowPayService;
use think\facade\Db;


/**
 * 支付逻辑
 * Class PaymentLogic
 * @package app\common\logic
 */
class PaymentLogic extends BaseLogic
{

    /**
     * @notes 支付方式
     * @param $userId
     * @param $terminal
     * @param $params
     * @return array|false
     * @author 段誉
     * @date 2023/2/24 17:53
     */
    public static function getPayWay($userId, $terminal, $params)
    {
        try {
            $order = [];
            if ($params['from'] == 'recharge') {
                $order = RechargeOrder::findOrEmpty($params['order_id'])->toArray();
            } elseif ($params['from'] == 'article') {
                $order = ArticleOrder::findOrEmpty($params['order_id'])->toArray();
            }

            if (empty($order)) {
                throw new \Exception('待支付订单不存在');
            }

            //获取支付场景
            $pay_way = PayWay::alias('pw')
                ->join('dev_pay_config dp', 'pw.pay_config_id = dp.id')
                ->where(['pw.scene' => $terminal, 'pw.status' => YesNoEnum::YES])
                ->field('dp.id,dp.name,dp.pay_way,dp.icon,dp.sort,dp.remark,pw.is_default')
                ->order('pw.is_default desc,dp.sort desc,id asc')
                ->select()
                ->toArray();

            foreach ($pay_way as $k => &$item) {
                if ($item['pay_way'] == PayEnum::WECHAT_PAY) {
                    $item['extra'] = '微信快捷支付';
                }
                if ($item['pay_way'] == PayEnum::ALI_PAY) {
                    $item['extra'] = '支付宝快捷支付';
                }
                if ($item['pay_way'] == PayEnum::RAINBOW_PAY) {
                    // 彩虹易支付根据场景显示不同的名称，让用户感觉是原生支付
                    if ($terminal == PayEnum::SCENE_MNP || $terminal == PayEnum::SCENE_OA) {
                        $item['name'] = '微信支付';
                        $item['extra'] = '推荐使用微信支付';
                    } elseif ($terminal == PayEnum::SCENE_H5 || $terminal == PayEnum::SCENE_PC) {
                        $item['name'] = '在线支付';
                        $item['extra'] = '支持微信、支付宝、QQ 等';
                    } elseif ($terminal == PayEnum::SCENE_APP) {
                        $item['name'] = '在线支付';
                        $item['extra'] = '支持多种支付方式';
                    } else {
                        $item['name'] = '在线支付';
                        $item['extra'] = '安全便捷的支付方式';
                    }
                }
                if ($item['pay_way'] == PayEnum::BALANCE_PAY) {
                    $user_money = User::where(['id' => $userId])->value('user_money');
                    $item['extra'] = '可用余额:' . $user_money;
                }
                // 充值时去除余额支付
                if ($params['from'] == 'recharge' && $item['pay_way'] == PayEnum::BALANCE_PAY) {
                    unset($pay_way[$k]);
                }
            }

            return [
                'lists' => array_values($pay_way),
                'order_amount' => $order['order_amount'],
            ];

        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes 获取支付状态
     * @param $params
     * @return array|false
     * @author 段誉
     * @date 2023/3/1 16:23
     */
    public static function getPayStatus($params)
    {
        try {
            $order = [];
            $orderInfo = [];
            switch ($params['from']) {
                case 'recharge':
                    $order = RechargeOrder::where(['user_id' => $params['user_id'], 'id' => $params['order_id']])
                        ->findOrEmpty();
                    $payTime = empty($order['pay_time']) ? '' : date('Y-m-d H:i:s', $order['pay_time']);
                    $orderInfo = [
                        'order_id' => $order['id'],
                        'order_sn' => $order['sn'],
                        'order_amount' => $order['order_amount'],
                        'pay_way' => PayEnum::getPayDesc($order['pay_way']),
                        'pay_status' => PayEnum::getPayStatusDesc($order['pay_status']),
                        'pay_time' => $payTime,
                    ];
                    break;
                case 'article':
                    $order = ArticleOrder::where(['user_id' => $params['user_id'], 'id' => $params['order_id']])
                        ->findOrEmpty();
                    $payTime = empty($order['pay_time']) ? '' : date('Y-m-d H:i:s', $order['pay_time']);
                    $orderInfo = [
                        'order_id' => $order['id'],
                        'order_sn' => $order['order_sn'],
                        'order_amount' => $order['order_amount'],
                        'pay_way' => PayEnum::getPayDesc($order['pay_way']),
                        'pay_status' => PayEnum::getPayStatusDesc($order['pay_status']),
                        'pay_time' => $payTime,
                    ];
                    break;
            }

            if (empty($order)) {
                throw new \Exception('订单不存在');
            }

            return [
                'pay_status' => $order['pay_status'],
                'pay_way' => $order['pay_way'],
                'order' => $orderInfo
            ];
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes 获取预支付订单信息
     * @param $params
     * @return RechargeOrder|array|false|\think\Model
     * @author 段誉
     * @date 2023/2/27 15:19
     */
    public static function getPayOrderInfo($params)
    {
        try {
            $order = [];
            switch ($params['from']) {
                case 'recharge':
                    $order = RechargeOrder::findOrEmpty($params['order_id']);
                    if ($order->isEmpty()) {
                        throw new \Exception('充值订单不存在');
                    }
                    break;
                case 'article':
                    $order = ArticleOrder::findOrEmpty($params['order_id']);
                    if ($order->isEmpty()) {
                        throw new \Exception('文章订单不存在');
                    }
                    break;
            }

            if (empty($order)) {
                throw new \Exception('订单不存在');
            }
            if ($order['pay_status'] == PayEnum::ISPAID) {
                throw new \Exception('订单已支付');
            }

            // 检查订单是否超过15分钟支付时限（仅对文章订单）
            if ($params['from'] == 'article' && isset($order['create_time'])) {
                $payTimeout = 900; // 15分钟 = 900秒
                if (time() - $order['create_time'] > $payTimeout) {
                    throw new \Exception('订单已超时，请重新下单');
                }
            }

            return $order;
        } catch (\Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 支付
     * @param $payWay
     * @param $from
     * @param $order
     * @param $terminal
     * @param $redirectUrl
     * @return array|false|mixed|string|string[]
     * @throws \Exception
     * @author mjf
     * @date 2024/3/18 16:49
     */
    public static function pay($payWay, $from, $order, $terminal, $redirectUrl)
    {
        $paySn = $order['sn'] ?? $order['order_sn'] ?? '';
        if ($payWay == PayEnum::WECHAT_PAY) {
            $paySn = self::formatOrderSn($order['sn'] ?? $order['order_sn'], $terminal);
        }

        switch ($from) {
            case 'recharge':
                RechargeOrder::update(['pay_way' => $payWay, 'pay_sn' => $paySn], ['id' => $order['id']]);
                break;
            case 'article':
                $updateData = ['pay_way' => $payWay, 'pay_sn' => $paySn];
                if (isset($order['coupon_id'])) {
                    $updateData['coupon_id'] = $order['coupon_id'];
                    $updateData['coupon_money'] = $order['coupon_money'];
                    $updateData['order_amount'] = $order['order_amount'];
                }
                ArticleOrder::update($updateData, ['id' => $order['id']]);
                break;
        }

        if (floatval($order['order_amount']) <= 0) {
            PayNotifyLogic::handle($from, $order['sn'] ?? $order['order_sn']);
            return ['pay_way' => PayEnum::BALANCE_PAY];
        }

        $payService = null;
        switch ($payWay) {
            case PayEnum::BALANCE_PAY:
                $result = self::balancePay($from, $order);
                break;
            case PayEnum::WECHAT_PAY:
                $payService = (new WeChatPayService($terminal, $order['user_id'] ?? null));
                $order['pay_sn'] = $paySn;
                $order['redirect_url'] = $redirectUrl;
                $result = $payService->pay($from, $order);
                break;
            case PayEnum::ALI_PAY:
                $payService = (new AliPayService($terminal));
                $order['redirect_url'] = $redirectUrl;
                $result = $payService->pay($from, $order);
                break;
            case PayEnum::RAINBOW_PAY:
                $payService = (new RainbowPayService());
                $order['redirect_url'] = $redirectUrl;
                $result = $payService->pay($from, $order);
                break;
            default:
                self::$error = '订单异常';
                $result = false;
        }

        if (false === $result && !self::hasError()) {
            self::setError($payService->getError());
        }
        return $result;
    }

    /**
     * @notes 余额支付
     * @param $from
     * @param $order
     * @return array|false
     * @author 杰哥
     * @qq 2711793818
     * @date 2024/02/02
     */
    public static function balancePay($from, $order)
    {
        try {
            // 安全修复：使用事务保护，确保数据一致性
            return Db::transaction(function () use ($from, $order) {
                $userId = $order['user_id'];
                $orderAmount = $order['order_amount'];
                $orderSn = $order['sn'] ?? $order['order_sn'];

                $user = User::lock(true)->findOrEmpty($userId);
                if ($user->isEmpty()) {
                    throw new \Exception('用户不存在');
                }

                if ($user->user_money < $orderAmount) {
                    throw new \Exception('余额不足，请选择其他支付方式');
                }

                // 扣除余额
                $user->user_money = bcsub(strval($user->user_money), strval($orderAmount), 2);
                $user->save();

                // 记录账户变动日志
                AccountLogLogic::add(
                    $userId,
                    AccountLogEnum::UM_DEC_ARTICLE_BUY,
                    AccountLogEnum::DEC,
                    $orderAmount,
                    $orderSn,
                    '购买文章'
                );

                // 处理支付回调逻辑
                PayNotifyLogic::handle($from, $orderSn);

                return ['pay_way' => PayEnum::BALANCE_PAY];
            });
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 设置订单号 支付回调时截取前面的单号 18个
     * @param $orderSn
     * @param $terminal
     * @return string
     * @author 段誉
     * @date 2023/3/1 16:31
     * @remark 回调时使用了不同的回调地址,导致跨客户端支付时(例如小程序,公众号)可能出现201,商户订单号重复错误
     */
    public static function formatOrderSn($orderSn, $terminal)
    {
        $suffix = mb_substr(time(), -4);
        return $orderSn . $terminal . $suffix;
    }

    /**
     * @notes 应用优惠券
     * @param $order
     * @param $couponId
     * @return array|false
     * @author 段誉
     * @date 2023/2/28 14:21
     */
    public static function applyCoupon($order, $couponId)
    {
        try {
            $userId = $order['user_id'];
            $orderAmount = $order['order_amount'];
            
            // 安全修复：使用事务锁防止并发使用同一张优惠券
            $userCoupon = Db::name('user_coupon')
                ->lock(true)
                ->where('id', $couponId)
                ->where('user_id', $userId)
                ->where('status', 0)
                ->where('use_time_start', '<=', time())
                ->where('use_time_end', '>=', time())
                ->find();
                
            if (!$userCoupon) {
                throw new \Exception('优惠券不可用或已被使用');
            }
            
            // 验证订单金额是否满足使用条件
            if (!isset($userCoupon['condition_money']) || $userCoupon['condition_money'] > $orderAmount) {
                throw new \Exception('订单金额不满足优惠券使用条件');
            }
            
            // 验证优惠券金额
            if (!isset($userCoupon['money']) || $userCoupon['money'] <= 0) {
                throw new \Exception('优惠券金额无效');
            }
            
            // 计算最终支付金额，确保不会为负数
            $finalAmount = max(0, bcsub(strval($orderAmount), strval($userCoupon['money']), 2));
            
            return [
                'coupon_money' => $userCoupon['money'],
                'final_amount' => $finalAmount
            ];
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

}