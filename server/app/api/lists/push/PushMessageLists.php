<?php
namespace app\api\lists\push;

use app\api\lists\BaseApiDataLists;
use app\common\model\notice\PushMessage;
use app\common\service\FileService;

/**
 * 推送消息列表
 * 开发者：杰哥网络科技 qq2711793818 杰哥
 */
class PushMessageLists extends BaseApiDataLists
{
    public function lists(): array
    {
        $lists = PushMessage::alias('pm')
            ->join('merchant m', 'm.id = pm.merchant_id')
            ->where('pm.user_id', $this->userId)
            ->where('pm.delete_time', null)
            ->field('pm.id, pm.merchant_id, pm.article_id, pm.keyword, pm.title, pm.content, pm.is_read, pm.create_time, m.name as merchant_name, m.logo as merchant_logo')
            ->order('pm.id', 'desc')
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['merchant_logo'] = $item['merchant_logo'] ? FileService::getFileUrl($item['merchant_logo']) : '';
        }

        return $lists;
    }

    public function count(): int
    {
        return PushMessage::where('user_id', $this->userId)
            ->where('delete_time', null)
            ->count();
    }
}
