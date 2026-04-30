<?php
namespace app\api\logic\merchant;

use app\common\logic\BaseLogic;
use app\common\model\merchant\MerchantFollow;
use app\common\model\merchant\Merchant;
use app\common\model\user\User;
use app\common\service\FileService;

/**
 * 关注逻辑
 * 开发者：杰哥网络科技 qq2711793818 杰哥
 */
class FollowLogic extends BaseLogic
{
    /**
     * @notes 切换关注状态
     * @param $userId
     * @param $merchantId
     * @return bool|string
     */
    public static function toggle($userId, $merchantId)
    {
        $merchant = Merchant::find($merchantId);
        if (!$merchant || $merchant['status'] != 1) {
            return '商家不存在或已下架';
        }

        $where = [
            'user_id' => $userId,
            'merchant_id' => $merchantId
        ];
        
        $follow = MerchantFollow::where($where)->find();
        
        if ($follow) {
            $count = MerchantFollow::where('user_id', $userId)->count();
            if ($count <= 1) {
                return '您必须至少关注一个商家，请先关注其他商家后再取消';
            }
            
            $follow->delete();
            
            $user = User::find($userId);
            if ($user && $user->current_merchant_id == $merchantId) {
                $firstFollow = MerchantFollow::where('user_id', $userId)->find();
                if ($firstFollow) {
                    $user->current_merchant_id = $firstFollow->merchant_id;
                    $user->save();
                }
            }
            
            return true;
        } else {
            MerchantFollow::create([
                'user_id' => $userId,
                'merchant_id' => $merchantId,
                'push_enable' => 1,
                'create_time' => time()
            ]);
            return true;
        }
    }

    /**
     * @notes 自动关注商家（推广链接注册时调用）
     * @param $userId
     * @param $merchantId
     * @return bool
     */
    public static function autoFollow($userId, $merchantId)
    {
        if (empty($merchantId)) {
            return false;
        }

        $merchant = Merchant::find($merchantId);
        if (!$merchant || $merchant['status'] != 1) {
            return false;
        }

        $exists = MerchantFollow::where([
            'user_id' => $userId,
            'merchant_id' => $merchantId
        ])->find();

        if (!$exists) {
            MerchantFollow::create([
                'user_id' => $userId,
                'merchant_id' => $merchantId,
                'push_enable' => 1,
                'create_time' => time()
            ]);
        }

        $user = User::find($userId);
        if ($user && empty($user->current_merchant_id)) {
            $user->current_merchant_id = $merchantId;
            $user->save();
        }

        return true;
    }

    /**
     * @notes 设置当前显示商家
     * @param $userId
     * @param $merchantId
     * @return bool|string
     */
    public static function setCurrentMerchant($userId, $merchantId)
    {
        $isFollow = MerchantFollow::where([
            'user_id' => $userId,
            'merchant_id' => $merchantId
        ])->find();

        if (!$isFollow) {
            return '您未关注该商家';
        }

        $user = User::find($userId);
        if (!$user) {
            return '用户不存在';
        }

        $user->current_merchant_id = $merchantId;
        $user->save();

        return true;
    }

    /**
     * @notes 获取当前显示商家信息
     * @param $userId
     * @return array
     */
    public static function getCurrentMerchant($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return [];
        }

        $merchantId = $user->current_merchant_id;
        
        if (empty($merchantId)) {
            $firstFollow = MerchantFollow::alias('mf')
                ->join('merchant m', 'm.id = mf.merchant_id')
                ->where('mf.user_id', $userId)
                ->where('m.status', 1)
                ->order('mf.create_time', 'asc')
                ->find();
            
            if ($firstFollow) {
                $merchantId = $firstFollow['merchant_id'];
                $user->current_merchant_id = $merchantId;
                $user->save();
            } else {
                return [];
            }
        }

        $merchant = Merchant::where('id', $merchantId)->where('status', 1)->find();
        if (!$merchant) {
            $firstFollow = MerchantFollow::alias('mf')
                ->join('merchant m', 'm.id = mf.merchant_id')
                ->where('mf.user_id', $userId)
                ->where('m.status', 1)
                ->where('m.id', '<>', $merchantId)
                ->order('mf.create_time', 'asc')
                ->find();
            
            if ($firstFollow) {
                $user->current_merchant_id = $firstFollow['merchant_id'];
                $user->save();
                $merchant = Merchant::find($firstFollow['merchant_id']);
            } else {
                $user->current_merchant_id = 0;
                $user->save();
                return [];
            }
        }

        return [
            'id' => $merchant->id,
            'name' => $merchant->name,
            'image' => FileService::getFileUrl($merchant->image),
            'cover' => FileService::getFileUrl($merchant->cover),
            'logo' => FileService::getFileUrl($merchant->logo),
            'desc' => $merchant->desc,
            'intro' => $merchant->intro,
            'mobile' => $merchant->mobile,
            'wechat' => $merchant->wechat,
            'ratio' => $merchant->distribution_ratio,
            'distribution_switch' => $merchant->distribution_switch ?? 1,
        ];
    }

    /**
     * @notes 获取已关注商家列表
     * @param $userId
     * @return array
     */
    public static function getFollowedMerchants($userId)
    {
        $list = MerchantFollow::alias('mf')
            ->join('merchant m', 'm.id = mf.merchant_id')
            ->where('mf.user_id', $userId)
            ->where('m.status', 1)
            ->field('m.id, m.name, m.image, m.logo, m.desc')
            ->order('mf.create_time', 'desc')
            ->select()
            ->toArray();

        foreach ($list as &$item) {
            $item['image'] = FileService::getFileUrl($item['image']);
            $item['logo'] = FileService::getFileUrl($item['logo']);
        }

        return $list;
    }

    public static function togglePush($userId, $merchantId)
    {
        $follow = MerchantFollow::where([
            'user_id' => $userId,
            'merchant_id' => $merchantId
        ])->find();

        if (!$follow) {
            return '您未关注该商家';
        }

        $follow->push_enable = $follow->push_enable ? 0 : 1;
        $follow->save();

        return true;
    }
}
