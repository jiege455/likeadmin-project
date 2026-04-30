<?php
namespace app\common\service\realname;

use app\common\service\ConfigService;
use app\common\model\user\UserRealname;
use Requests;

class RealNameService
{
    /**
     * 获取实名配置
     */
    public static function getConfig()
    {
        return [
            'status' => ConfigService::get('realname', 'status', 0), // 0关闭 1开启(强制)
            'auth_type' => ConfigService::get('realname', 'auth_type', 'manual'), // manual-人工, aliyun-阿里云
            'aliyun_appcode' => ConfigService::get('realname', 'aliyun_appcode', ''),
            'aliyun_url' => ConfigService::get('realname', 'aliyun_url', 'https://idenauthen.market.alicloudapi.com/idenAuthentication'),
            'umeng_appkey' => ConfigService::get('realname', 'umeng_appkey', ''),
            'umeng_appsecret' => ConfigService::get('realname', 'umeng_appsecret', ''),
        ];
    }

    /**
     * 校验实名信息
     * @param $realName
     * @param $idCard
     * @return array [bool $pass, string $msg, array $data]
     */
    public static function check($realName, $idCard)
    {
        $config = self::getConfig();
        $type = $config['auth_type'] ?? 'manual';

        if ($type == 'manual') {
            // 人工审核，直接通过提交，状态设为待审核
            return [true, '提交成功，等待审核', ['status' => UserRealname::STATUS_WAIT]];
        } elseif ($type == 'aliyun') {
            // 对接阿里云身份证二要素校验
            return self::checkByAliyun($realName, $idCard, $config['aliyun_appcode'], $config['aliyun_url']);
        } elseif ($type == 'umeng') {
            // 对接友盟认证
            return self::checkByUmeng($realName, $idCard, $config['umeng_appkey'], $config['umeng_appsecret']);
        }

        return [false, '未知的认证方式', []];
    }

    /**
     * 阿里云实名认证
     * 默认使用：https://market.aliyun.com/products/57000002/cmapi022049.html
     */
    protected static function checkByAliyun($realName, $idCard, $appCode, $url)
    {
        if (empty($appCode)) {
            return [false, '未配置阿里云AppCode', []];
        }
        if (empty($url)) {
            $url = "https://idenauthen.market.alicloudapi.com/idenAuthentication";
        }

        $headers = [
            'Authorization' => 'APPCODE ' . $appCode,
            'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8'
        ];
        
        $body = [
            'idNo' => $idCard,
            'name' => $realName
        ];

        try {
            // 使用 Requests 库发起请求
            $response = Requests::post($url, $headers, $body);
            
            if (!$response->success) {
                return [false, '请求第三方接口失败: ' . $response->status_code, []];
            }

            $result = json_decode($response->body, true);
            
            // 根据实际API文档解析结果
            // 示例API返回: {"respCode":"0000","respMessage":"处理成功",...}
            if (isset($result['respCode']) && $result['respCode'] == '0000') {
                return [true, '认证成功', ['status' => UserRealname::STATUS_SUCCESS]];
            }
            
            // 处理失败情况
            $msg = $result['respMessage'] ?? '认证失败';
            return [false, $msg, []];

        } catch (\Exception $e) {
            return [false, '实名认证异常: ' . $e->getMessage(), []];
        }
    }

    /**
     * 友盟实名认证
     * 注：友盟通常使用U-Verify进行本机校验，实名认证API需联系友盟开通并获取具体文档
     * 此处仅提供标准AppKey/AppSecret请求结构的示例，实际对接需替换URL和参数结构
     */
    protected static function checkByUmeng($realName, $idCard, $appKey, $appSecret)
    {
        if (empty($appKey) || empty($appSecret)) {
            return [false, '未配置友盟AppKey/AppSecret', []];
        }

        // 占位符地址，请替换为真实的友盟实名认证API地址
        $url = "https://verify.umeng.com/api/v1/realname/check"; 

        // 构造签名 (示例逻辑)
        $timestamp = time() * 1000;
        $sign = md5($appKey . $timestamp . $appSecret); 

        $headers = [
            'Content-Type' => 'application/json',
            'X-Umeng-AppKey' => $appKey,
            'X-Umeng-Timestamp' => $timestamp,
            'X-Umeng-Sign' => $sign
        ];

        $body = json_encode([
            'id_card' => $idCard,
            'name' => $realName
        ]);

        try {
            // 使用 Requests 库发起请求
            $response = Requests::post($url, $headers, $body);
            
            if (!$response->success) {
                 // 模拟测试环境，如果配置了Key但请求失败(因为URL是假的)，暂时返回人工审核状态以防报错
                 // 实际生产环境应返回失败
                 return [true, '友盟接口暂未连通，转人工审核', ['status' => UserRealname::STATUS_WAIT]];
            }

            $result = json_decode($response->body, true);
            
            if (isset($result['code']) && $result['code'] == 200) {
                return [true, '认证成功', ['status' => UserRealname::STATUS_SUCCESS]];
            }
            
            $msg = $result['msg'] ?? '认证失败';
            return [false, $msg, []];

        } catch (\Exception $e) {
            return [false, '实名认证异常: ' . $e->getMessage(), []];
        }
    }
}
