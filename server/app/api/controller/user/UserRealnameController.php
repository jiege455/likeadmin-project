<?php
namespace app\api\controller\user;

use app\api\controller\BaseApiController;
use app\common\model\user\UserRealname;
use app\common\service\realname\RealNameService;
use think\facade\Db;

class UserRealnameController extends BaseApiController
{
    /**
     * @notes 获取实名配置及状态
     */
    public function info()
    {
        $config = RealNameService::getConfig();
        
        $realname = UserRealname::where('user_id', $this->userId)->find();
        
        $data = [
            'open_realname' => (int)($config['status'] ?? 0), // 是否强制实名开关
            'user_realname' => $realname ? [
                'status' => $realname->status, // 0待审 1通过 2拒绝
                'real_name' => $realname->real_name,
                'id_card' => substr_replace($realname->id_card, '********', 6, 8),
                'fail_reason' => $realname->fail_reason
            ] : null
        ];

        return $this->data($data);
    }

    /**
     * @notes 提交实名认证
     */
    public function submit()
    {
        $post = $this->request->post();
        $realName = $post['real_name'] ?? '';
        $idCard = $post['id_card'] ?? '';
        $mobile = $post['mobile'] ?? '';

        if (empty($realName) || empty($idCard)) {
            return $this->fail('请填写真实姓名和身份证号');
        }

        // 检查是否已认证
        $exist = UserRealname::where('user_id', $this->userId)->find();
        if ($exist && $exist->status == UserRealname::STATUS_SUCCESS) {
            return $this->fail('您已通过实名认证');
        }
        if ($exist && $exist->status == UserRealname::STATUS_WAIT) {
            return $this->fail('您的认证正在审核中，请勿重复提交');
        }

        // 调用服务进行校验
        list($pass, $msg, $resData) = RealNameService::check($realName, $idCard);

        if (!$pass) {
            return $this->fail($msg);
        }

        $status = $resData['status'] ?? UserRealname::STATUS_WAIT;

        if ($exist) {
            $exist->real_name = $realName;
            $exist->id_card = $idCard;
            $exist->mobile = $mobile;
            $exist->status = $status;
            $exist->fail_reason = '';
            $exist->save();
        } else {
            UserRealname::create([
                'user_id' => $this->userId,
                'real_name' => $realName,
                'id_card' => $idCard,
                'mobile' => $mobile,
                'status' => $status,
                'create_time' => time(),
                'update_time' => time(),
            ]);
        }

        // 如果认证成功，同步更新user表的real_name
        if ($status == UserRealname::STATUS_SUCCESS) {
            \app\common\model\user\User::update(['real_name' => $realName], ['id' => $this->userId]);
        }

        return $this->success($status == UserRealname::STATUS_SUCCESS ? '认证成功' : '提交成功，请等待审核');
    }
}
