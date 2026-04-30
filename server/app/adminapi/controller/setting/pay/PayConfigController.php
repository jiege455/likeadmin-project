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
namespace app\adminapi\controller\setting\pay;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\setting\pay\PayConfigLists;
use app\adminapi\logic\setting\pay\PayConfigLogic;
use app\adminapi\validate\setting\PayConfigValidate;
use think\response\Json;

/**
 * 支付配置
 * Class PayConfigController
 * @package app\adminapi\controller\setting\pay
 */
class PayConfigController extends BaseAdminController
{


    /**
     * @notes 设置支付配置
     * @return Json
     * @author 段誉
     * @date 2023/2/23 16:14
     */
    public function setConfig(): Json
    {
        $params = (new PayConfigValidate())->post()->goCheck();
        PayConfigLogic::setConfig($params);
        return $this->success('设置成功', [], 1, 1);
    }


    /**
     * @notes 获取支付配置
     * @return Json
     * @author 段誉
     * @date 2023/2/23 16:14
     */
    public function getConfig(): Json
    {
        $id = (new PayConfigValidate())->goCheck('get');
        $result = PayConfigLogic::getConfig($id);
        return $this->success('获取成功', $result);
    }


    /**
     * @notes
     * @return Json
     * @author 段誉
     * @date 2023/2/23 16:15
     */
    public function lists(): Json
    {
        return $this->dataLists(new PayConfigLists());
    }


    /**
     * @notes 测试支付网关连接
     * @return Json
     * @author likeadminTeam
     * @date 2026/3/5
     */
    public function testGateway(): Json
    {
        $params = $this->request->post();
        
        try {
            // 验证必填参数
            if (empty($params['pay_way'])) {
                return $this->fail('请选择支付方式');
            }
            
            if (empty($params['config']['gateway_url'])) {
                return $this->fail('请填写支付网关地址');
            }
            
            if (empty($params['config']['app_id'])) {
                return $this->fail('请填写商户 ID');
            }
            
            if (empty($params['config']['app_secret'])) {
                return $this->fail('请填写商户密钥');
            }
            
            if (empty($params['config']['pay_key'])) {
                return $this->fail('请填写支付密钥');
            }
            
            // 测试连接
            $gatewayUrl = $params['config']['gateway_url'];
            $testUrl = rtrim($gatewayUrl, '/') . '/query.php';
            
            // 发起简单的 HTTP 请求测试连接
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $testUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'pid' => $params['config']['app_id'],
                'sign' => md5('test=' . $params['config']['pay_key']),
            ]));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            
            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($httpCode == 200 || $httpCode == 0) {
                return $this->success('连接成功！支付网关响应正常');
            } else {
                return $this->fail('连接失败：' . ($error ?: 'HTTP 状态码：' . $httpCode));
            }
            
        } catch (\Exception $e) {
            return $this->fail('测试失败：' . $e->getMessage());
        }
    }
}