<?php

namespace app\api\controller\merchant;

use app\api\controller\BaseApiController;
use think\facade\Db;
use app\common\service\EmailService;
use app\common\service\sms\SmsDriver;
use app\common\service\ConfigService;
use app\common\enum\notice\NoticeEnum;
use app\common\enum\notice\EmailEnum;
use app\common\enum\SystemEnum;
use app\common\model\notice\EmailLog;
use app\common\logic\EmailNotifyLogic;

/**
 * 商户入驻申请控制器
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
class ApplyController extends BaseApiController
{
    public function add()
    {
        // 支持JSON格式和表单格式数据
        $post = $this->request->post();
        if (empty($post)) {
            $input = file_get_contents('php://input');
            $post = json_decode($input, true) ?: [];
        }
        $userId = $this->userId;

        if (empty($post['name'])) {
            return $this->fail('请填写店铺名称');
        }

        $verifyType = ConfigService::get('system', 'merchant_apply_verify_type', 'email');
        $emailNotifyOpen = ConfigService::get('system', 'email_notify_open', 0);
        $smsNotifyOpen = ConfigService::get('system', 'sms_notify_open', 1);
        $openAudit = ConfigService::get('merchant', 'open_audit', 0);

        if ($verifyType === SystemEnum::VERIFY_TYPE_EMAIL && $emailNotifyOpen) {
            if (empty($post['email'])) {
                return $this->fail('请填写邮箱');
            }
            if (empty($post['email_code'])) {
                return $this->fail('请输入邮箱验证码');
            }
            $emailVerify = $this->verifyEmailCode($post['email'], $post['email_code'], EmailEnum::MERCHANT_APPLY);
            if (!$emailVerify) {
                return $this->fail('邮箱验证码错误或已过期');
            }
        }

        if ($verifyType === SystemEnum::VERIFY_TYPE_MOBILE && $smsNotifyOpen) {
            if (empty($post['mobile'])) {
                return $this->fail('请填写手机号');
            }
            if (empty($post['code'])) {
                return $this->fail('请输入手机验证码');
            }
            $smsDriver = new SmsDriver();
            $verifyResult = $smsDriver->verify($post['mobile'], $post['code'], NoticeEnum::MERCHANT_APPLY_CAPTCHA);
            if (!$verifyResult) {
                return $this->fail('手机验证码错误或已过期');
            }
        }

        $existMerchant = Db::name('merchant')->where('user_id', $userId)->whereNull('delete_time')->find();
        if ($existMerchant) {
            return $this->fail('您已是商户，无需重复申请');
        }

        $pendingApply = Db::name('merchant_apply')
            ->where('user_id', $userId)
            ->where('status', 0)
            ->whereNull('delete_time')
            ->find();
        if ($pendingApply) {
            return $this->fail('您已有待审核的申请，请耐心等待审核');
        }

        try {
            if ($openAudit == 1) {
                $insertData = [
                    'user_id' => $userId,
                    'name' => mb_substr($post['name'], 0, 100),
                    'mobile' => isset($post['mobile']) ? mb_substr($post['mobile'], 0, 20) : '',
                    'email' => isset($post['email']) ? mb_substr($post['email'], 0, 100) : '',
                    'wechat' => isset($post['wechat']) ? mb_substr($post['wechat'], 0, 50) : '',
                    'desc' => isset($post['desc']) ? mb_substr($post['desc'], 0, 255) : '',
                    'status' => 0,
                    'create_time' => time(),
                    'update_time' => time()
                ];

                $res = Db::name('merchant_apply')->insertGetId($insertData);

                if (!$res) {
                    return $this->fail('数据库写入失败');
                }

                $insertData['id'] = $res;
                EmailNotifyLogic::sendMerchantApplyNotifyToAdmin($insertData);

                return $this->success('申请提交成功，请等待审核');
            } else {
                $applyData = [
                    'user_id' => $userId,
                    'name' => mb_substr($post['name'], 0, 100),
                    'mobile' => isset($post['mobile']) ? mb_substr($post['mobile'], 0, 20) : '',
                    'email' => isset($post['email']) ? mb_substr($post['email'], 0, 100) : '',
                    'wechat' => isset($post['wechat']) ? mb_substr($post['wechat'], 0, 50) : '',
                    'desc' => isset($post['desc']) ? mb_substr($post['desc'], 0, 255) : '',
                    'status' => 1,
                    'create_time' => time(),
                    'update_time' => time()
                ];
                Db::name('merchant_apply')->insert($applyData);

                Db::name('merchant')->insert([
                    'user_id' => $userId,
                    'name' => mb_substr($post['name'], 0, 100),
                    'mobile' => isset($post['mobile']) ? mb_substr($post['mobile'], 0, 20) : '',
                    'email' => isset($post['email']) ? mb_substr($post['email'], 0, 100) : '',
                    'wechat' => isset($post['wechat']) ? mb_substr($post['wechat'], 0, 50) : '',
                    'desc' => isset($post['desc']) ? mb_substr($post['desc'], 0, 255) : '',
                    'intro' => isset($post['desc']) ? mb_substr($post['desc'], 0, 255) : '',
                    'status' => 1,
                    'create_time' => time(),
                    'update_time' => time()
                ]);

                return $this->success('入驻成功');
            }
        } catch (\Exception $e) {
            \think\facade\Log::error('Merchant Apply Insert Error: ' . $e->getMessage());
            return $this->fail('申请提交失败: ' . $e->getMessage());
        }
    }

    public function detail()
    {
        $userId = $this->userId;
        $info = Db::name('merchant_apply')
            ->where('user_id', $userId)
            ->whereNull('delete_time')
            ->order('id', 'desc')
            ->find();

        if (empty($info)) {
            $info = [];
        }

        return $this->data($info);
    }

    private function verifyEmailCode(string $email, string $code, int $sceneId): bool
    {
        $log = EmailLog::where('email', $email)
            ->where('scene_id', $sceneId)
            ->where('send_status', 1)
            ->where('is_verify', 0)
            ->where('send_time', '>', time() - 300)
            ->order('send_time', 'desc')
            ->findOrEmpty();

        if ($log->isEmpty()) {
            return false;
        }

        if ($log->code !== $code) {
            return false;
        }

        $log->is_verify = 1;
        $log->save();

        return true;
    }
}
