<?php
namespace app\api\logic\push;

use app\common\logic\BaseLogic;
use app\common\model\user\UserPushKeyword;
use app\common\model\merchant\Merchant;
use think\facade\Db;

/**
 * 推送关键词逻辑
 * 开发者：杰哥网络科技 qq2711793818 杰哥
 */
class PushKeywordLogic extends BaseLogic
{
    const MAX_KEYWORDS_PER_MERCHANT = 10;

    public static function lists($userId, $merchantId)
    {
        return UserPushKeyword::where([
            'user_id' => $userId,
            'merchant_id' => $merchantId
        ])
            ->where('delete_time', null)
            ->field('id, keyword, is_enable, create_time')
            ->order('id', 'desc')
            ->select()
            ->toArray();
    }

    public static function add($userId, $merchantId, $keyword)
    {
        $merchant = Merchant::where('id', $merchantId)
            ->where('delete_time', null)
            ->find();
        if (!$merchant || $merchant->status != 1) {
            return '商家不存在或已下架';
        }

        $count = UserPushKeyword::where([
            'user_id' => $userId,
            'merchant_id' => $merchantId
        ])->where('delete_time', null)->count();

        if ($count >= self::MAX_KEYWORDS_PER_MERCHANT) {
            return '每个商家最多设置' . self::MAX_KEYWORDS_PER_MERCHANT . '个关键词';
        }

        $exists = UserPushKeyword::where([
            'user_id' => $userId,
            'merchant_id' => $merchantId,
            'keyword' => $keyword
        ])->where('delete_time', null)->find();

        if ($exists) {
            return '该关键词已存在';
        }

        UserPushKeyword::create([
            'user_id' => $userId,
            'merchant_id' => $merchantId,
            'keyword' => $keyword,
            'is_enable' => 1,
            'create_time' => time()
        ]);

        return true;
    }

    public static function edit($userId, $id, $keyword)
    {
        $pushKeyword = UserPushKeyword::where('id', $id)
            ->where('delete_time', null)
            ->find();
        if (!$pushKeyword || $pushKeyword->user_id != $userId) {
            return '关键词不存在';
        }

        $exists = UserPushKeyword::where([
            'user_id' => $userId,
            'merchant_id' => $pushKeyword->merchant_id,
            'keyword' => $keyword
        ])->where('id', '<>', $id)
          ->where('delete_time', null)
          ->find();

        if ($exists) {
            return '该关键词已存在';
        }

        $pushKeyword->keyword = $keyword;
        $pushKeyword->update_time = time();
        $pushKeyword->save();

        return true;
    }

    public static function delete($userId, $id)
    {
        $pushKeyword = UserPushKeyword::where('id', $id)
            ->where('delete_time', null)
            ->find();
        if (!$pushKeyword || $pushKeyword->user_id != $userId) {
            return '关键词不存在';
        }

        $pushKeyword->delete();
        return true;
    }

    public static function toggle($userId, $id)
    {
        $pushKeyword = UserPushKeyword::where('id', $id)
            ->where('delete_time', null)
            ->find();
        if (!$pushKeyword || $pushKeyword->user_id != $userId) {
            return '关键词不存在';
        }

        $pushKeyword->is_enable = $pushKeyword->is_enable ? 0 : 1;
        $pushKeyword->update_time = time();
        $pushKeyword->save();

        return true;
    }
}
