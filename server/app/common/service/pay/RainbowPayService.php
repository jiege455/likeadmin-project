<?php
// +----------------------------------------------------------------------
// | likeadmin 快速开发前后端分离管理后台（PHP 版）
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权 logo
// | gitee 下载：https://gitee.com/likeshop_gitee/likeadmin
// | github 下载：https://github.com/likeshop-github/likeadmin
// | 访问官网：https://www.likeadmin.cn
// | likeadmin 团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeadminTeam
// +----------------------------------------------------------------------

namespace app\common\service\pay;

use app\common\enum\PayEnum;
use app\common\logic\PayNotifyLogic;
use app\common\model\pay\PayConfig;
use app\common\model\recharge\RechargeOrder;
use app\common\model\article\ArticleOrder;

/**
 * 彩虹易支付服务类
 * Class RainbowPayService
 * @package app\common\service\pay
 */
class RainbowPayService extends BasePayService
{
    /**
     * 彩虹易支付配置
     * @var array
     */
    protected $config;

    /**
     * 支付网关地址
     * @var string
     */
    protected $gateway;

    /**
     * 初始化彩虹易支付配置
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function __construct()
    {
        $payConfig = PayConfig::where('pay_way', PayEnum::RAINBOW_PAY)->findOrEmpty();
        if ($payConfig->isEmpty()) {
            $this->setError('彩虹易支付配置不存在');
            return;
        }
        $this->config = $payConfig['config'];
        
        // 设置支付网关地址（从配置读取，如果没有则使用默认值）
        // 安全修复：强制使用 HTTPS 协议，防止中间人攻击
        $gatewayUrl = $this->config['gateway_url'] ?? 'https://api.rainbowpay.com';
        // 确保网关地址使用 HTTPS
        if (strpos($gatewayUrl, 'http://') === 0) {
            $gatewayUrl = str_replace('http://', 'https://', $gatewayUrl);
        }
        $this->gateway = $gatewayUrl;
    }

    /**
     * @notes 发起支付
     * @param $from 支付来源：recharge(充值)/article(文章)
     * @param $order 订单信息
     * @return array|false
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function pay($from, $order)
    {
        if ($this->hasError()) {
            return false;
        }

        try {
            // 统一处理订单号（可能是数组或模型）
            $orderNo = is_array($order) ? ($order['sn'] ?? $order['order_sn'] ?? '') : ($order->sn ?? $order->order_sn ?? '');
            $orderAmount = is_array($order) ? ($order['order_amount'] ?? 0) : ($order->order_amount ?? 0);
            $orderName = is_array($order) ? ($order['name'] ?? '') : ($order->name ?? '');
            
            // 订单名称默认值
            if (empty($orderName)) {
                $orderName = $from == 'recharge' ? '充值订单' : '文章订单';
            }

            $params = [
                'pid' => $this->config['app_id'],
                'type' => 'wxpay', // 默认微信支付，可根据需求调整
                'out_trade_no' => $orderNo,
                'return_url' => request()->domain() . '/api/pay/notify/rainbow_return',
                'notify_url' => request()->domain() . '/api/pay/notify/rainbow',
                'name' => $orderName,
                'money' => $orderAmount,
                'sign' => '',
                'sign_type' => 'MD5',
            ];

            // 生成签名
            $params['sign'] = $this->generateSign($params);

            // 发起 HTTP 请求获取支付二维码或链接
            $result = $this->httpPost($this->gateway . '/v1/pay', $params);

            if (!$result) {
                $this->setError('请求彩虹易支付失败：' . $this->getError());
                return false;
            }

            $result = json_decode($result, true);

            if (isset($result['code']) && $result['code'] == 1) {
                return [
                    'pay_url' => $result['data']['payurl'] ?? '',
                    'qr_code' => $result['data']['qrcode'] ?? '',
                    'order_no' => $orderNo,
                ];
            } else {
                $this->setError($result['msg'] ?? '彩虹易支付下单失败');
                return false;
            }
        } catch (\Exception $e) {
            $this->setError('支付异常：' . $e->getMessage());
            return false;
        }
    }

    /**
     * @notes 处理支付回调
     * @param $params 回调参数
     * @return bool
     */
    public function notify($params)
    {
        try {
            // 安全修复：严格的签名验证
            $sign = $params['sign'] ?? '';
            if (empty($sign)) {
                $this->setError('签名不能为空');
                return false;
            }
            
            $signParams = $params;
            unset($signParams['sign']);
            unset($signParams['sign_type']);
            
            // 验证商户 ID 是否匹配，防止伪造请求
            if (($params['pid'] ?? '') !== $this->config['app_id']) {
                $this->setError('商户 ID 不匹配');
                return false;
            }

            $mySign = $this->generateSign($signParams);

            if ($sign !== $mySign) {
                $this->setError('签名验证失败');
                return false;
            }

            // 处理订单
            $orderNo = $params['out_trade_no'] ?? '';
            if (empty($orderNo)) {
                $this->setError('订单号不能为空');
                return false;
            }
            
            // 安全修复：规范化订单号处理逻辑，避免查询到错误订单
            // 订单号格式：原始订单号 (18 位) + 终端类型 + 时间戳后 4 位
            $originalOrderNo = mb_substr($orderNo, 0, 18);
            
            // 优先使用原始订单号查询
            $order = RechargeOrder::where('pay_sn', $originalOrderNo)->findOrEmpty();
            $from = 'recharge';
            
            if ($order->isEmpty()) {
                $order = ArticleOrder::where('pay_sn', $originalOrderNo)->findOrEmpty();
                $from = 'article';
            }
            
            // 如果原始订单号查询失败，尝试使用完整订单号查询
            if ($order->isEmpty()) {
                $order = RechargeOrder::where('pay_sn', $orderNo)->findOrEmpty();
                $from = 'recharge';
                
                if ($order->isEmpty()) {
                    $order = ArticleOrder::where('pay_sn', $orderNo)->findOrEmpty();
                    $from = 'article';
                }
            }
            
            if ($order->isEmpty()) {
                $this->setError('订单不存在：' . $orderNo);
                return false;
            }
            
            // 检查订单是否已支付，防止重复回调
            if ($order['pay_status'] == PayEnum::ISPAID) {
                // 订单已支付，直接返回成功
                echo 'success';
                return true;
            }

            // 调用支付回调逻辑
            $result = PayNotifyLogic::handle($from, $order['sn'] ?? $order['order_sn'], PayEnum::RAINBOW_PAY);

            if ($result) {
                echo 'success';
                return true;
            } else {
                echo 'fail';
                return false;
            }
        } catch (\Exception $e) {
            $this->setError('回调处理异常：' . $e->getMessage());
            echo 'fail';
            return false;
        }
    }

    /**
     * @notes 生成签名
     * @param $params
     * @return string
     */
    public function generateSign($params)
    {
        ksort($params);
        $signStr = '';
        foreach ($params as $key => $val) {
            if ($val == '' || $key == 'sign' || $key == 'sign_type') {
                continue;
            }
            $signStr .= $key . '=' . $val . '&';
        }
        $signStr = rtrim($signStr, '&');
        $signStr .= '&key=' . $this->config['pay_key'];
        return md5($signStr);
    }

    /**
     * @notes HTTP POST 请求
     * @param $url
     * @param $data
     * @return false|string
     */
    protected function httpPost($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            $this->setError('CURL 错误：' . curl_error($ch));
            curl_close($ch);
            return false;
        }
        curl_close($ch);
        return $result;
    }

    /**
     * @notes 查询订单状态
     * @param $orderNo 订单号
     * @return array|false
     */
    public function queryOrder($orderNo)
    {
        try {
            $params = [
                'pid' => $this->config['app_id'],
                'out_trade_no' => $orderNo,
                'sign' => '',
                'sign_type' => 'MD5',
            ];

            $params['sign'] = $this->generateSign($params);

            $result = $this->httpPost($this->gateway . '/v1/query', $params);

            if (!$result) {
                return false;
            }

            $result = json_decode($result, true);

            if (isset($result['code']) && $result['code'] == 1) {
                return [
                    'status' => $result['data']['status'] ?? '',
                    'trade_no' => $result['data']['trade_no'] ?? '',
                ];
            } else {
                $this->setError($result['msg'] ?? '查询失败');
                return false;
            }
        } catch (\Exception $e) {
            $this->setError('查询异常：' . $e->getMessage());
            return false;
        }
    }
}
