<?php
namespace app\api\lists\push;

use app\api\lists\BaseApiDataLists;
use app\common\model\user\UserPushKeyword;

/**
 * 推送关键词列表
 * 开发者：杰哥网络科技 qq2711793818 杰哥
 */
class PushKeywordLists extends BaseApiDataLists
{
    public function lists(): array
    {
        $merchantId = $this->params['merchant_id'] ?? 0;
        
        $lists = UserPushKeyword::where([
            'user_id' => $this->userId,
            'merchant_id' => $merchantId
        ])
            ->where('delete_time', null)
            ->field('id, merchant_id, keyword, is_enable, create_time')
            ->order('id', 'desc')
            ->select()
            ->toArray();

        return $lists;
    }

    public function count(): int
    {
        $merchantId = $this->params['merchant_id'] ?? 0;
        
        return UserPushKeyword::where([
            'user_id' => $this->userId,
            'merchant_id' => $merchantId
        ])
            ->where('delete_time', null)
            ->count();
    }
}
