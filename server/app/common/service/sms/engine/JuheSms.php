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
namespace app\common\service\sms\engine;

/**
 * 聚合数据短信
 * 官网：https://www.juhe.cn/
 * API文档：https://www.juhe.cn/docs/api/id/54
 * 
 * 请求参数说明：
 * mobile: 接收短信的手机号码
 * tpl_id: 短信模板ID，在官网申请模板后获得
 * tpl_value: 变量名和变量值对，格式：#code#=123456&#name#=张三
 * key: 应用APPKEY
 * 
 * 返回示例：
 * 成功：{"error_code":0,"reason":"发送成功","result":{"sid":"xxx","fee":1}}
 * 失败：{"error_code":xxx,"reason":"错误原因","result":null}
 * 
 * Class JuheSms
 * @package app\common\service\sms\engine
 */
class JuheSms
{
    protected $error = null;
    protected $config;
    protected $mobile;
    protected $templateId;
    protected $templateParams;

    public function __construct($config)
    {
        if(empty($config)) {
            $this->error = '请联系管理员配置参数';
            return false;
        }
        
        if(empty($config['app_key'])) {
            $this->error = '请配置聚合数据AppKey';
            return false;
        }
        
        $this->config = $config;
    }


    /**
     * @notes 设置手机号
     * @param $mobile
     * @return $this
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
        return $this;
    }


    /**
     * @notes 设置模板id
     * @param $templateId
     * @return $this
     */
    public function setTemplateId($templateId)
    {
        $this->templateId = $templateId;
        return $this;
    }


    /**
     * @notes 设置模板参数
     * @param $templateParams
     * @return $this
     */
    public function setTemplateParams($templateParams)
    {
        $this->templateParams = $templateParams;
        return $this;
    }


    /**
     * @notes 错误信息
     * @return string|null
     */
    public function getError()
    {
        return $this->error;
    }


    /**
     * @notes 发送短信
     * @return array|false
     */
    public function send()
    {
        try {
            if(empty($this->mobile)) {
                throw new \Exception('手机号码不能为空');
            }
            
            if(empty($this->templateId)) {
                throw new \Exception('短信模板ID不能为空');
            }
            
            $url = 'https://v.juhe.cn/sms/send';
            
            $params = [
                'mobile' => $this->mobile,
                'tpl_id' => $this->templateId,
                'tpl_value' => $this->formatTemplateParams(),
                'key' => $this->config['app_key'],
            ];
            
            $response = $this->httpRequest($url, $params);
            
            if($response === false) {
                throw new \Exception('HTTP请求失败：' . $this->error);
            }
            
            $result = json_decode($response, true);
            
            if(json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('JSON解析失败：' . json_last_error_msg());
            }
            
            if (isset($result['error_code']) && $result['error_code'] == 0) {
                return $result;
            }
            
            $message = $result['reason'] ?? '未知错误';
            $errorCode = $result['error_code'] ?? 'unknown';
            throw new \Exception('聚合数据短信错误[' . $errorCode . ']：' . $message);
        } catch(\Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }


    /**
     * @notes 格式化模板参数
     * 聚合数据模板变量格式：#code#=123456&#name#=张三
     * @return string
     */
    private function formatTemplateParams()
    {
        if (empty($this->templateParams)) {
            return '';
        }
        
        $params = $this->templateParams;
        
        if (is_string($params)) {
            $params = json_decode($params, true);
        }
        
        if (empty($params) || !is_array($params)) {
            return '';
        }
        
        $tplValue = [];
        foreach ($params as $key => $value) {
            $key = urlencode('#' . $key . '#');
            $value = urlencode($value);
            $tplValue[] = $key . '=' . $value;
        }
        
        return implode('&', $tplValue);
    }


    /**
     * @notes HTTP请求
     * @param $url
     * @param $params
     * @return bool|string
     */
    private function httpRequest($url, $params)
    {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            $this->error = 'CURL错误：' . curl_error($ch);
            curl_close($ch);
            return false;
        }
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode != 200) {
            $this->error = 'HTTP状态码错误：' . $httpCode;
            return false;
        }
        
        return $response;
    }
}
