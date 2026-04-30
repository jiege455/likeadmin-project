<?php
namespace app\common\service\wechat;

use app\common\model\user\User;
use app\common\model\user\UserAuth;
use app\common\service\wechat\WeChatConfigService;
use think\facade\Db;
use think\facade\Log;

class WeChatServerService
{
    public function serve()
    {
        $app = WeChatConfigService::getOaApp();
        $server = $app->server;

        $server->push(function ($message) {
            switch ($message['MsgType']) {
                case 'event':
                    switch ($message['Event']) {
                        case 'subscribe':
                            return $this->handleSubscribe($message);
                        case 'unsubscribe':
                            return $this->handleUnsubscribe($message);
                        case 'SCAN':
                            return $this->handleScan($message);
                    }
                    break;
            }
        });
        return $server->serve();
    }

    private function handleSubscribe($message)
    {
        $openid = $message['FromUserName'];
        
        // 处理带参二维码
        if (isset($message['EventKey']) && strpos($message['EventKey'], 'qrscene_') === 0) {
            $scene = substr($message['EventKey'], 8);
            $this->bindRelation($openid, $scene);
        }
        
        return '欢迎关注!';
    }

    private function handleScan($message)
    {
        $openid = $message['FromUserName'];
        if (isset($message['EventKey'])) {
            $this->bindRelation($openid, $message['EventKey']);
        }
        return '欢迎回来!';
    }

    private function handleUnsubscribe($message)
    {
        $openid = $message['FromUserName'];
        $userAuth = UserAuth::where('openid', $openid)->find();
        if ($userAuth) {
            // 1. 清除全局邀请人 (取消关注 -> 解绑)
            User::update(['inviter_id' => 0], ['id' => $userAuth->user_id]);
            
            // 2. 清除所有商家绑定关系
            Db::name('user_merchant')->where('user_id', $userAuth->user_id)->delete();
        }
    }

    private function bindRelation($openid, $scene)
    {
        // 解析参数: 假设格式为 mid=1&code=1001
        // 如果只是简单参数，需要根据业务约定
        // 这里尝试解析
        parse_str($scene, $params);
        $merchantId = $params['mid'] ?? 0;
        $inviteCode = $params['code'] ?? '';

        // 如果解析失败，尝试直接作为 code
        if (empty($inviteCode) && empty($merchantId)) {
            // 简单兼容: 假设纯数字是 code
            if (is_numeric($scene)) {
                $inviteCode = $scene;
            }
        }

        if (empty($inviteCode)) return;

        $userAuth = UserAuth::where('openid', $openid)->find();
        if (!$userAuth) return;
        
        $userId = $userAuth->user_id;

        // 查找邀请人
        $inviter = User::where('sn', $inviteCode)->find();
        if (!$inviter) return;

        // 1. 全局绑定 (如果为空则绑定)
        $user = User::find($userId);
        if ($user && empty($user->inviter_id)) {
            $user->inviter_id = $inviter->id;
            $user->save();
        }

        // 2. 商家绑定 (如果 merchant_id 存在)
        if ($merchantId > 0) {
             $exists = Db::name('user_merchant')
                ->where(['user_id' => $userId, 'merchant_id' => $merchantId])
                ->find();
             if (!$exists) {
                 Db::name('user_merchant')->insert([
                     'user_id' => $userId,
                     'merchant_id' => $merchantId,
                     'inviter_id' => $inviter->id,
                     'create_time' => time(),
                     'update_time' => time()
                 ]);
             }
        }
    }
}
