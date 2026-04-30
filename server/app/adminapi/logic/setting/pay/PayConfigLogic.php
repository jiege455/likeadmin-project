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

namespace app\adminapi\logic\setting\pay;


use app\common\enum\PayEnum;
use app\common\logic\BaseLogic;
use app\common\model\pay\PayConfig;
use app\common\service\FileService;

/**
 * 支付配置
 * Class PayConfigLogic
 * @package app\adminapi\logic\setting\pay
 */
class PayConfigLogic extends BaseLogic
{

    /**
     * @notes 设置配置
     * @param $params
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2023/2/23 16:16
     */
    public static function setConfig($params)
    {
        $payConfig = PayConfig::findOrEmpty($params['id']);
        if ($payConfig->isEmpty()) {
            self::setError('支付配置不存在');
            return false;
        }

        $config = '';
        if ($payConfig['pay_way'] == PayEnum::WECHAT_PAY) {
            $config = [
                'interface_version' => $params['config']['interface_version'],
                'merchant_type' => $params['config']['merchant_type'],
                'mch_id' => $params['config']['mch_id'],
                'pay_sign_key' => $params['config']['pay_sign_key'],
                'apiclient_cert' => $params['config']['apiclient_cert'],
                'apiclient_key' => $params['config']['apiclient_key'],
            ];
        }
        if ($payConfig['pay_way'] == PayEnum::ALI_PAY) {
            $config = [
                'mode' => $params['config']['mode'],
                'merchant_type' => $params['config']['merchant_type'],
                'app_id' => $params['config']['app_id'],
                'private_key' => $params['config']['private_key'],
                'ali_public_key' => $params['config']['mode'] == 'normal_mode' ? $params['config']['ali_public_key'] : '',
                'public_cert' => $params['config']['mode'] == 'certificate' ? $params['config']['public_cert'] : '',
                'ali_public_cert' => $params['config']['mode'] == 'certificate' ? $params['config']['ali_public_cert'] : '',
                'ali_root_cert' => $params['config']['mode'] == 'certificate' ? $params['config']['ali_root_cert'] : '',
            ];
        }
        if ($payConfig['pay_way'] == PayEnum::RAINBOW_PAY) {
            $config = [
                'app_id' => $params['config']['app_id'],
                'app_secret' => $params['config']['app_secret'],
                'pay_key' => $params['config']['pay_key'],
                'gateway_url' => $params['config']['gateway_url'] ?? '',
            ];
        }

        $payConfig->name = $params['name'];
        $payConfig->icon = FileService::setFileUrl($params['icon']);
        $payConfig->sort = $params['sort'];
        $payConfig->config = $config;
        $payConfig->remark = $params['remark'] ?? '';
        return $payConfig->save();
    }


    /**
     * @notes 获取配置
     * @param $params
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2023/2/23 16:16
     */
    public static function getConfig($params)
    {
        $payConfig = PayConfig::findOrEmpty($params['id']);
        if ($payConfig->isEmpty()) {
            return [];
        }
        $payConfig = $payConfig->toArray();
        $payConfig['icon'] = FileService::getFileUrl($payConfig['icon']);
        $payConfig['domain'] = request()->domain();
        
        if (isset($payConfig['config']) && is_array($payConfig['config'])) {
            $payConfig['config'] = self::maskSensitiveFields($payConfig['config']);
        }
        
        return $payConfig;
    }

    /**
     * @notes 对敏感字段进行脱敏处理
     * @param array $config
     * @return array
     */
    private static function maskSensitiveFields(array $config): array
    {
        $sensitiveFields = [
            'pay_sign_key',
            'apiclient_key',
            'private_key',
            'ali_public_key',
        ];
        
        foreach ($sensitiveFields as $field) {
            if (isset($config[$field]) && !empty($config[$field])) {
                $value = $config[$field];
                $length = strlen($value);
                if ($length > 8) {
                    $config[$field] = substr($value, 0, 4) . '****' . substr($value, -4);
                } else {
                    $config[$field] = '****';
                }
            }
        }
        
        return $config;
    }

}
